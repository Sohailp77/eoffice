<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SystemRoleSeeder extends Seeder
{
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
        // IDs: 1 (Sohail), 100 (Sheetal) - typical IDs, let's just assign to all current users for dev safety or specific ones
        // Better: Assign to specific users if known, or just the first few.
        // Let's assign to user 1 and 100 as known in context.

        $userIds = [1, 100, 103]; // Add any critical user IDs here

        foreach ($userIds as $id) {
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
    }
}
