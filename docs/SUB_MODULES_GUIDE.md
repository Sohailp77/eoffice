# Sub-Modules Implementation Guide

## Overview

We have implemented a **Dynamic Auto-Discovery** sub-module system. This allows developers to create sub-modules by simply creating folders and configuration files, without needing to manually insert records into the database.

**Key Features:**
- **Dynamic Discovery**: `php artisan modules:sync` automatically scans and registers modules.
- **Strict Hierarchy**: 2-Level structure (Module -> Sub-Module).
- **Granular Permissions**: Each sub-module has its own permission levels managed in the database.

---

## 1. Directory Structure

To create a sub-module, use the following structure:

```
app/Modules/
└── {ParentModule}/          # e.g., Library
    ├── Module.php          # Parent Configuration
    └── SubModules/
        └── {SubModule}/     # e.g., Books
            ├── Config.php   # Child Configuration
            ├── routes.php   # Sub-module Routes
            └── Controllers/ # Sub-module Controllers
```

### Parent Configuration (`Module.php`)
```php
namespace App\Modules\Library;

class Module
{
    const NAME = 'Library';
    const SLUG = 'library';
}
```

### Child Configuration (`Config.php`)
```php
return [
    'name' => 'Books Management', // Display Name
    'slug' => 'books',            // URL Slug
    'order' => 1,                 // Sort Order
];
```

---

## 2. Syncing Modules

After creating or modifying your module structure, run the sync command:

```bash
php artisan modules:sync
```

This command will:
1. Scan `app/Modules` recursively.
2. Register new Parent Modules in the database.
3. Register new Sub-Modules and link them to their Parent.
4. Update names and ordering if changed in config.

---

## 3. Protecting Routes

Use the `submodule` middleware to protect your routes.

**Syntax:**
`submodule:{parent_slug},{sub_module_slug},{level}`

**Example `routes.php`:**

```php
Route::prefix('books')->name('books.')->group(function () {
    
    // Browse Access (View List)
    Route::middleware('submodule:library,books,browse')->group(function () {
        Route::get('/', [BooksController::class, 'index'])->name('index');
    });

    // Read Access (View Details)
    Route::middleware('submodule:library,books,read')->group(function () {
        Route::get('/{id}', [BooksController::class, 'show'])->name('show');
    });

    // Write Access (Create/Edit)
    Route::middleware('submodule:library,books,write')->group(function () {
        Route::post('/', [BooksController::class, 'store'])->name('store');
    });
});
```

---

## 4. Checking Permissions in Code

Use the `RbacService` to check permissions in views or controllers.

```php
// Check access
if ($rbacService->userHasSubModuleAccess($user, 'library', 'books', 'write')) {
    // Show 'Create' button
}

// Get all accessible sub-modules for a user
$subModules = $rbacService->getUserSubModules($user, 'library');
```

---

## 5. Database Schema

The system uses the following schema:

- **modules**: 
    - `parent_id` (Nullable): Links sub-modules to parents.
    - `order`: Controls display order.
- **sub_module_permissions**:
    - Links `user_id`, `sub_module_id`, and `module_level_id`.
- **module_levels**:
    - Defines levels (Browse, Read, Write, Manage). 
    - Sub-modules reuse the Parent Module's levels.
