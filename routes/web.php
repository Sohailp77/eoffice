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

        $accessibleModules = $userModuleLevels->map(function ($uml) {
            $slug = $uml->module->slug;
            // Assume entry route convention: {slug}.index
            $routeName = $slug . '.index';
            $url = \Illuminate\Support\Facades\Route::has($routeName) ? route($routeName) : '#';

            // Fallback: checks if a prefix route exists or just use slug as relative url
            if ($url === '#') {
                $url = url($slug);
            }

            return [
                'name' => $uml->module->name,
                'description' => "Access Level: " . $uml->level->name, // Showing level as description
                'route' => $url, // View uses 'route' or 'url'
                'slug' => $slug,
                'active' => $uml->module->is_active
            ];
        })->filter(function ($m) {
            return $m['active'];
        });

        $totalUsers = User::count();
        $totalModules = Module::count();
        $recentUsers = User::orderBy('dt_of_creation', 'desc')->take(5)->get(); // Assuming 'id' as proxy for 'latest' if timestamps not available or just to be safe

        return view('dashboard', [
            'accessibleModules' => $accessibleModules,
            'totalUsers' => $totalUsers,
            'totalModules' => $totalModules,
            'recentUsers' => $recentUsers
        ]);
    })->name('dashboard');

    Route::get('/reports', function () {
        return view('reports');
    })->middleware('module:reports-module')->name('reports');

    Route::get('/settings', function () {
        return view('settings');
    })->middleware('module:settings-module')->name('settings');

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
