# Module Development Guide

## Overview

The eOffice application uses a **modular, level-based access control system**. This guide explains how to create new modules that integrate seamlessly with the existing architecture.

---

## Development Mode 🚀

**Speed up development by auto-granting full access to all modules!**

### Enable Development Mode
```bash
php artisan dev:toggle-access --enable
```

## Disable development access
```bash
php artisan dev:toggle-access --disable
```

**What it does:**
- ✅ Automatically grants **highest access level** to all modules for all authenticated users
- ✅ No need to run seeders or manually assign access
- ✅ Only works when `APP_ENV=local` (safety feature)
- ✅ Perfect for rapid module development

### Disable Development Mode
```bash
php artisan dev:toggle-access --disable
```

> [!CAUTION]
> **Never enable development mode in production!** The middleware will refuse to enable it if `APP_ENV` is not `local`.

### Check Status
```bash
php artisan dev:toggle-access
```

---

## Architecture Principles

### 1. Module Structure
Each module is self-contained in `app/Modules/{ModuleName}/`:
```
app/Modules/MyModule/
├── Module.php              # Module metadata
├── routes.php              # Module routes
├── Controllers/            # Controllers
│   └── MyModuleController.php
├── Services/              # Business logic (optional)
│   └── MyService.php
└── Resources/             # Views (optional)
    └── Views/
        └── index.blade.php
```

### 2. Access Levels
Modules define **priority-based access levels**:
- **Lower priority** = Basic access (e.g., Browse, Read)
- **Higher priority** = Advanced access (e.g., Write, Manage)
- Users with higher priority can access lower priority routes

### 3. Database Tables
- **`modules`**: Module registry
- **`module_levels`**: Access levels per module
- **`user_module_levels`**: User assignments (one level per user per module)

---

## Step-by-Step: Creating a Module

### Step 1: Create Module Metadata

Create `app/Modules/MyModule/Module.php`:

```php
<?php

namespace App\Modules\MyModule;

class Module
{
    public const NAME = 'My Module';
    public const SLUG = 'my-module';
    
    // Define access levels with priorities
    public const LEVELS = [
        ['name' => 'View Only', 'slug' => 'view', 'priority' => 1],
        ['name' => 'Edit Access', 'slug' => 'edit', 'priority' => 2],
        ['name' => 'Full Access', 'slug' => 'manage', 'priority' => 3],
    ];
}
```

**Key Points:**
- `SLUG` must be unique and URL-friendly
- `priority` determines access hierarchy (higher = more access)
- Users with priority 3 can access routes requiring priority 1 or 2

---

### Step 2: Create Routes

Create `app/Modules/MyModule/routes.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Modules\MyModule\Controllers\MyModuleController;

Route::middleware(['web', 'auth', 'module:my-module'])
    ->prefix('my-module')
    ->name('my-module.')
    ->group(function () {
        
        // View-only route (priority >= 1)
        Route::get('/', [MyModuleController::class, 'index'])
            ->middleware('level:view')
            ->name('index');
            
        // Edit route (priority >= 2)
        Route::post('/update', [MyModuleController::class, 'update'])
            ->middleware('level:edit')
            ->name('update');
            
        // Management route (priority >= 3)
        Route::delete('/{id}', [MyModuleController::class, 'destroy'])
            ->middleware('level:manage')
            ->name('destroy');
    });
```

**Middleware Explained:**
- `module:my-module` - Verifies user has ANY access to this module
- `level:view` - Requires user's priority >= level's priority

---

### Step 3: Create Controller

Create `app/Modules/MyModule/Controllers/MyModuleController.php`:

```php
<?php

namespace App\Modules\MyModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyModuleController extends Controller
{
    public function index()
    {
        // Your logic here
        return view('my-module::index');
    }

    public function update(Request $request)
    {
        // Update logic
    }

    public function destroy($id)
    {
        // Delete logic
    }
}
```

---

### Step 4: Register Module in Database

Create a seeder `database/seeders/MyModuleSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\ModuleLevel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MyModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Register the module
        $module = Module::firstOrCreate(
            ['slug' => 'my-module'],
            ['name' => 'My Module', 'is_active' => true]
        );

        // 2. Register access levels
        $viewLevel = ModuleLevel::firstOrCreate(
            ['module_id' => $module->id, 'slug' => 'view'],
            ['name' => 'View Only', 'priority' => 1]
        );

        $editLevel = ModuleLevel::firstOrCreate(
            ['module_id' => $module->id, 'slug' => 'edit'],
            ['name' => 'Edit Access', 'priority' => 2]
        );

        $manageLevel = ModuleLevel::firstOrCreate(
            ['module_id' => $module->id, 'slug' => 'manage'],
            ['name' => 'Full Access', 'priority' => 3]
        );

        // 3. Assign access to specific users
        $adminUsers = [1, 100, 103]; // User IDs
        
        foreach ($adminUsers as $userId) {
            $exists = DB::connection('pgsql_app')
                ->table('user_module_levels')
                ->where('user_id', $userId)
                ->where('module_id', $module->id)
                ->exists();
            
            if (!$exists) {
                DB::connection('pgsql_app')->table('user_module_levels')->insert([
                    'user_id' => $userId,
                    'module_id' => $module->id,
                    'module_level_id' => $manageLevel->id, // Give full access
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
```

Run the seeder:
```bash
php artisan db:seed --class=MyModuleSeeder
```

---

### Step 5: Create Views (Optional)

If using module-specific views, create `app/Modules/MyModule/Resources/Views/index.blade.php`:

```blade
<x-layouts.app>
    <x-ui.page-header 
        title="My Module" 
        description="Module description here" 
    />
    
    <div class="space-y-6">
        <!-- Your content -->
    </div>
</x-layouts.app>
```

Register views in `ModuleServiceProvider` (already done automatically).

---

## How Access Control Works

### Middleware Flow

1. **User visits `/my-module`**
2. **`module:my-module` middleware**:
   - Checks if user has ANY `user_module_levels` entry for this module
   - If NO → 403 Forbidden
   - If YES → Sets context (`current_module`, `user_module_level`)
3. **`level:view` middleware**:
   - Compares user's priority with required priority
   - If `user_priority >= required_priority` → Allow
   - Else → 403 Forbidden

### Example Scenarios

**User A** has `view` level (priority 1):
- ✅ Can access `GET /my-module` (requires priority 1)
- ❌ Cannot access `POST /my-module/update` (requires priority 2)

**User B** has `manage` level (priority 3):
- ✅ Can access ALL routes (priority 3 >= all others)

---

## Best Practices

### 1. Naming Conventions
- **Module slug**: `kebab-case` (e.g., `file-manager`, `user-reports`)
- **Level slug**: `lowercase` (e.g., `read`, `write`, `admin`)
- **Route names**: `{module-slug}.{action}` (e.g., `library.index`, `library.upload`)

### 2. Priority Guidelines
- **1-2**: Read/View access
- **3-4**: Write/Edit access
- **5+**: Administrative/Delete access

### 3. Route Organization
```php
// Group by access level for clarity
Route::middleware(['web', 'auth', 'module:my-module'])
    ->prefix('my-module')
    ->name('my-module.')
    ->group(function () {
        
        // Public routes (lowest priority)
        Route::middleware('level:view')->group(function () {
            Route::get('/', [Controller::class, 'index'])->name('index');
            Route::get('/{id}', [Controller::class, 'show'])->name('show');
        });
        
        // Edit routes
        Route::middleware('level:edit')->group(function () {
            Route::post('/', [Controller::class, 'store'])->name('store');
            Route::put('/{id}', [Controller::class, 'update'])->name('update');
        });
        
        // Admin routes (highest priority)
        Route::middleware('level:manage')->group(function () {
            Route::delete('/{id}', [Controller::class, 'destroy'])->name('destroy');
        });
    });
```

### 4. Database Connections
- **User data**: `pgsql` connection (ecourtisuserdb)
- **App data**: `pgsql_app` connection (eoffice)
- Always use `DB::connection('pgsql_app')` for module tables

---

## Testing Your Module

### 1. Verify Routes
```bash
php artisan route:list --path=my-module
```

### 2. Test Access Control
```bash
php artisan tinker
```
```php
$user = App\Models\User::find(100);
Auth::login($user);

// Simulate request
$req = Illuminate\Http\Request::create('/my-module', 'GET');
$res = app()->handle($req);
echo $res->getStatusCode(); // Should be 200 if user has access
```

### 3. Verify Dashboard Display
- Login as a user with access
- Check if module appears on `/dashboard`
- Click module card to verify routing

---

## Troubleshooting

### Module doesn't appear on dashboard
- ✅ Check `modules.is_active = true`
- ✅ Verify user has entry in `user_module_levels`
- ✅ Run seeder: `php artisan db:seed --class=MyModuleSeeder`

### 403 Forbidden on routes
- ✅ Check middleware order: `module` before `level`
- ✅ Verify user's priority >= route's required priority
- ✅ Check module slug matches in routes and middleware

### Routes not loading
- ✅ Verify `ModuleServiceProvider` is registered in `bootstrap/providers.php`
- ✅ Check `routes.php` exists in module directory
- ✅ Clear route cache: `php artisan route:clear`

---

## Quick Reference

### File Checklist
- [ ] `app/Modules/{Name}/Module.php`
- [ ] `app/Modules/{Name}/routes.php`
- [ ] `app/Modules/{Name}/Controllers/{Name}Controller.php`
- [ ] `database/seeders/{Name}ModuleSeeder.php`

### Commands
```bash
# Create migration (if needed)
php artisan make:migration create_my_table

# Run seeder
php artisan db:seed --class=MyModuleSeeder

# Verify routes
php artisan route:list --path=my-module

# Clear caches
php artisan route:clear
php artisan view:clear
```

---

## Example: Complete Minimal Module

See `app/Modules/Example/` for a working reference implementation with:
- ✅ Two access levels (Read, Write)
- ✅ Protected routes
- ✅ Simple controller
- ✅ Database seeder

Study this module as a template for your own modules!
