<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Module;
use App\Models\SystemRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;


Route::get("/", function () {
    return redirect()->route('welcome');
})->name('home');


Route::get('/login', function () {
    return redirect()->route('login');
});
Route::get('welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');





Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();

        // Fetch modules the user has access to, eager loading module and level
        $userModuleLevels = $user->moduleLevels()->with(['module', 'level'])->get();

        // Format helper
        $formatModule = function ($uml) {
            $slug = $uml->module->slug;
            $routeName = $slug . '.index';
            // Check if route exists, if not fallback
            $url = \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : '#';

            // Fallback: checks if a prefix route exists or just use slug as relative url
            if ($url === '#') {
                $url = url($slug);
            }

            return [
                'name' => $uml->module->name,
                'description' => "Access Level: " . $uml->level->name,
                'route' => $url,
                'slug' => $slug,
                'active' => $uml->module->is_active,
                'parent_id' => $uml->module->parent_id
            ];
        };

        // All Accessible Modules (formatted)
        $accessibleModules = $userModuleLevels->map($formatModule)->filter(fn($m) => $m['active']);

        // Parent Modules (formatted)
        $formattedParentModules = $userModuleLevels
            ->filter(fn($uml) => $uml->module && $uml->module->parent_id === null && $uml->module->is_active)
            ->map($formatModule)
            ->values();

        // Sub Modules (formatted)
        $formattedSubModules = $userModuleLevels
            ->filter(fn($uml) => $uml->module && $uml->module->parent_id !== null && $uml->module->is_active)
            ->map($formatModule)
            ->values();


        $totalUsers = User::count();
        $recentUsers = User::orderBy('dt_of_creation', 'desc')->take(5)->get(); // Assuming 'id' as proxy for 'latest' if timestamps not available or just to be safe

        return view('dashboard', [
            'accessibleModules' => $accessibleModules,
            //accesible parent modules (formatted)
            'accesibleparentmodule' => $formattedParentModules,
            //accesible sub modules (formatted)
            'accesiblesubmodule' => $formattedSubModules,
            'totalUsers' => $totalUsers,
            //total parent modules
            'totalParentModules' => Module::whereNull('parent_id')->count(),
            //total sub modules
            'totalSubModules' => Module::whereNotNull('parent_id')->count(),
            //total users parent modules access count
            'totalUsersParentModules' => $formattedParentModules->count(),
            //total users sub modules access count
            'totalUsersSubModules' => $formattedSubModules->count(),
            'recentUsers' => $recentUsers
        ]);
    })->name('dashboard');

    // User Management Routes (Admin Only)
    Route::get('/admin/users', [UserManagementController::class, 'index'])
        ->name('admin.users.index');

    Route::post('/admin/users/{id}/toggle-admin', [UserManagementController::class, 'toggleAdmin'])
        ->name('admin.users.toggle-admin');

    // Module Access Management Routes
    Route::get('/admin/module-access', [\App\Http\Controllers\AdminModuleAccessController::class, 'index'])
        ->name('admin.module-access.index');

    Route::get('/admin/module-access/{userId}', [\App\Http\Controllers\AdminModuleAccessController::class, 'edit'])
        ->name('admin.module-access.edit');

    Route::post('/admin/module-access/{userId}', [\App\Http\Controllers\AdminModuleAccessController::class, 'update'])
        ->name('admin.module-access.update');


    // System Module Management
    Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
        Route::resource('modules', \App\Http\Controllers\System\ModuleController::class);
        Route::post('modules/{module}/submodules', [\App\Http\Controllers\System\ModuleController::class, 'storeSubModule'])->name('modules.submodules.store');
    });

});
