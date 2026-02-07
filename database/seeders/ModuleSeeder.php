<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\ModuleLevel;
use App\Models\User;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Register Example Module
        $module = Module::firstOrCreate(
            ['slug' => 'example-module'],
            ['name' => 'Example Module', 'is_active' => true]
        );

        // Register Levels
        $read = ModuleLevel::firstOrCreate(
            ['module_id' => $module->id, 'slug' => 'read'],
            ['name' => 'Read Access', 'priority' => 1]
        );

        $write = ModuleLevel::firstOrCreate(
            ['module_id' => $module->id, 'slug' => 'write'],
            ['name' => 'Write Access', 'priority' => 2]
        );

        // Assign 'Write' access to user 100 (Sheetal) and 1 (Sohail)
        $userIds = [1, 100, 103];
        foreach ($userIds as $id) {
            $user = User::find($id);
            if ($user) {
                // Using DB to avoid model complexity
                $exists = DB::connection('pgsql_app')->table('user_module_levels')
                    ->where('user_id', $id)
                    ->where('module_id', $module->id)
                    ->exists();

                if (!$exists) {
                    DB::connection('pgsql_app')->table('user_module_levels')->insert([
                        'user_id' => $id,
                        'module_id' => $module->id,
                        'module_level_id' => $write->id, // Giving Write access
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
