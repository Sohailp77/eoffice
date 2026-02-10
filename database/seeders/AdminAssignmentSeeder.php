<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class AdminAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            // User 103 is likely 'sohail'
            $userId = 103;

            // Check if already assigned
            $exists = DB::connection('sqlite')->table('role_user')
                ->where('user_id', $userId)
                ->where('role_id', $adminRole->id)
                ->exists();

            if (!$exists) {
                DB::connection('pgsql_app')->table('role_user')->insert([
                    'user_id' => $userId,
                    'role_id' => $adminRole->id,
                ]);
                $this->command->info("Assigned Admin role to User ID $userId");
            } else {
                $this->command->info("User ID $userId is already an Admin");
            }
        } else {
            $this->command->error("Admin role not found!");
        }
    }
}
