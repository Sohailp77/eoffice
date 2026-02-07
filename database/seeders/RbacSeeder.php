<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // permissions
        $permDashboard = Permission::create(['name' => 'Access Dashboard', 'slug' => 'access_dashboard']);
        $permReports = Permission::create(['name' => 'Access Reports', 'slug' => 'access_reports']);
        $permSettings = Permission::create(['name' => 'Access Settings', 'slug' => 'access_settings']);

        // roles
        $roleAdmin = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $roleEmployee = Role::create(['name' => 'Employee', 'slug' => 'employee']);

        // assign permissions to roles
        $roleAdmin->permissions()->attach([$permDashboard->id, $permReports->id, $permSettings->id]);
        $roleEmployee->permissions()->attach([$permDashboard->id]);

        // modules
        $modDashboard = Module::create([
            'name' => 'Dashboard',
            'slug' => 'dashboard-module',
            'route' => 'dashboard',
            'active' => true,
        ]);

        $modReports = Module::create([
            'name' => 'Reports',
            'slug' => 'reports-module',
            'route' => 'reports',
            'active' => true,
        ]);

        $modSettings = Module::create([
            'name' => 'Settings',
            'slug' => 'settings-module',
            'route' => 'settings',
            'active' => true,
        ]);

        // link modules to permissions
        // Dashboard needs access_dashboard
        $modDashboard->permissions()->attach($permDashboard->id);

        // reports needs access_reports
        $modReports->permissions()->attach($permReports->id);

        // Settings needs access_settings
        $modSettings->permissions()->attach($permSettings->id);

        // --- NEW: User Management ---
        $permManageUsers = Permission::create(['name' => 'Manage Users', 'slug' => 'manage_users']);
        $roleAdmin->permissions()->attach($permManageUsers->id);

        $modUserMgmt = Module::create([
            'name' => 'User Management',
            'slug' => 'user-management-module',
            'route' => 'admin.users.index',
            'active' => true,
        ]);
        $modUserMgmt->permissions()->attach($permManageUsers->id);

        // For demonstration, assign User 1 to Admin, User 2 to Employee
        // We use DB::table to insert into pivot since we might not have User models available if PG is offline
        // But role_user is in SQLite, distinct from Users.

        // Simulating mappings for existing hypothetical users
        DB::table('role_user')->insert([
            ['user_id' => 1, 'role_id' => $roleAdmin->id],
            ['user_id' => 2, 'role_id' => $roleEmployee->id],
        ]);
    }
}
