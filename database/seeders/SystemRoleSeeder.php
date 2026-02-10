<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SystemRoleSeeder extends Seeder
{
    public $targetIds = [1, 100, 103]; // Add any critical user IDs here

    public function run(): void
    {
        // Ensure roles exist
        $admin = SystemRole::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator']
        );

        $user = SystemRole::firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'Standard User']
        );

        // Assign Admin role to existing users (safety net)
        foreach ($this->targetIds as $id) {
            $u = User::find($id);
            if ($u) {
                // Use DB directly for pivot to avoid any model relation issues during migration
                $exists = DB::connection('pgsql_app')->table('system_role_user')
                    ->where('user_id', $id)
                    ->where('system_role_id', $admin->id)
                    ->exists();

                if (!$exists) {
                    DB::connection('pgsql_app')->table('system_role_user')->insert([
                        'user_id' => $id,
                        'system_role_id' => $admin->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Fallback: If no admin assigned yet (e.g. fresh install with unknown user IDs), make the user with id 1 an admin
        $adminRoleCount = DB::connection('pgsql_app')->table('system_role_user')
            ->where('system_role_id', $admin->id)
            ->count();

        if ($adminRoleCount === 0) {
            $firstUser = User::find(1);
            if ($firstUser) {
                DB::connection('pgsql_app')->table('system_role_user')->insert([
                    'user_id' => $firstUser->userid,
                    'system_role_id' => $admin->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                if ($this->command) {
                    $this->command->info("Assigned Admin role to first user found: {$firstUser->username} (ID: {$firstUser->userid})");
                }
            } else {
                if ($this->command) {
                    $this->command->warn("No users found in the database. Created Admin role but could not assign it.");
                }
            }
        }
    }
}
