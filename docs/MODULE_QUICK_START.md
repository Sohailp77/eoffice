# Module Development Quick Start

## Development Mode (Recommended for Developers)

**Skip manual access assignment during development!**

```bash
# Enable automatic full access to all modules
php artisan dev:toggle-access --enable

# Now you can access any module without running seeders!
# Disable when deploying to production
php artisan dev:toggle-access --disable
```

> [!WARNING]
> Development mode only works when `APP_ENV=local`. Never enable in production!

---

## TL;DR - Create a Module in 5 Steps

### 1. Create Module Structure
```bash
mkdir -p app/Modules/MyModule/Controllers
```

### 2. Module Metadata (`app/Modules/MyModule/Module.php`)
```php
<?php
namespace App\Modules\MyModule;

class Module
{
    public const NAME = 'My Module';
    public const SLUG = 'my-module';
    public const LEVELS = [
        ['name' => 'View', 'slug' => 'view', 'priority' => 1],
        ['name' => 'Edit', 'slug' => 'edit', 'priority' => 2],
    ];
}
```

### 3. Routes (`app/Modules/MyModule/routes.php`)
```php
<?php
use Illuminate\Support\Facades\Route;
use App\Modules\MyModule\Controllers\MyModuleController;

Route::middleware(['web', 'auth', 'module:my-module'])
    ->prefix('my-module')
    ->name('my-module.')
    ->group(function () {
        Route::get('/', [MyModuleController::class, 'index'])
            ->middleware('level:view')->name('index');
    });
```

### 4. Controller (`app/Modules/MyModule/Controllers/MyModuleController.php`)
```php
<?php
namespace App\Modules\MyModule\Controllers;
use App\Http\Controllers\Controller;

class MyModuleController extends Controller
{
    public function index()
    {
        return "My Module Works!";
    }
}
```

### 5. Seeder (`database/seeders/MyModuleSeeder.php`)
```php
<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{Module, ModuleLevel};
use Illuminate\Support\Facades\DB;

class MyModuleSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::firstOrCreate(
            ['slug' => 'my-module'],
            ['name' => 'My Module', 'is_active' => true]
        );

        $level = ModuleLevel::firstOrCreate(
            ['module_id' => $module->id, 'slug' => 'view'],
            ['name' => 'View', 'priority' => 1]
        );

        // Assign to user ID 100
        DB::connection('pgsql_app')->table('user_module_levels')->insertOrIgnore([
            'user_id' => 100,
            'module_id' => $module->id,
            'module_level_id' => $level->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

Run: `php artisan db:seed --class=MyModuleSeeder`

---

## Access Level Priority System

**Higher priority = More access**

```
Priority 1 (View)  ──→ Can access routes requiring priority 1
Priority 2 (Edit)  ──→ Can access routes requiring priority 1 OR 2
Priority 3 (Admin) ──→ Can access routes requiring priority 1, 2, OR 3
```

**Example:**
```php
// User with priority 2 (Edit)
Route::get('/view', ...)->middleware('level:view');   // ✅ Allowed (2 >= 1)
Route::post('/edit', ...)->middleware('level:edit');  // ✅ Allowed (2 >= 2)
Route::delete('/delete', ...)->middleware('level:admin'); // ❌ Denied (2 < 3)
```

---

## Common Patterns

### File Upload Module
```php
public const LEVELS = [
    ['name' => 'Browse', 'slug' => 'browse', 'priority' => 1],
    ['name' => 'Download', 'slug' => 'download', 'priority' => 2],
    ['name' => 'Upload', 'slug' => 'upload', 'priority' => 3],
    ['name' => 'Delete', 'slug' => 'delete', 'priority' => 4],
];
```

### Reporting Module
```php
public const LEVELS = [
    ['name' => 'View Reports', 'slug' => 'view', 'priority' => 1],
    ['name' => 'Generate Reports', 'slug' => 'generate', 'priority' => 2],
    ['name' => 'Export Data', 'slug' => 'export', 'priority' => 3],
];
```

### Admin Panel Module
```php
public const LEVELS = [
    ['name' => 'Read Only', 'slug' => 'read', 'priority' => 1],
    ['name' => 'Full Admin', 'slug' => 'admin', 'priority' => 2],
];
```

---

## Verification Checklist

After creating your module:

- [ ] Routes visible: `php artisan route:list --path=my-module`
- [ ] Seeder ran: `php artisan db:seed --class=MyModuleSeeder`
- [ ] Module appears on dashboard for assigned users
- [ ] Clicking module card navigates to module
- [ ] Access levels enforced (test with different user priorities)

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Module not on dashboard | Check `is_active = true` and user has `user_module_levels` entry |
| 403 on all routes | Verify `module:{slug}` middleware matches module slug |
| 403 on specific routes | Check user priority >= route's required priority |
| Routes not found | Ensure `ModuleServiceProvider` is in `bootstrap/providers.php` |

---

**For detailed documentation, see:** `docs/MODULE_DEVELOPMENT.md`
