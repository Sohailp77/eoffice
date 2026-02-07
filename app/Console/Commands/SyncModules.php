<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SyncModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-discover and sync modules and sub-modules from code to database';

    protected $levels = [
        'browse' => 1,
        'read' => 2,
        'write' => 3,
        'manage' => 4,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting module sync...');

        $modulesPath = app_path('Modules');
        if (!File::exists($modulesPath)) {
            $this->error("Modules directory not found at {$modulesPath}");
            return;
        }

        $directories = File::directories($modulesPath);

        foreach ($directories as $dir) {
            $this->processParentModule($dir);
        }

        $this->info('Module sync completed successfully!');
    }

    private function processParentModule(string $dir)
    {
        $moduleName = basename($dir);
        $configClass = "App\\Modules\\{$moduleName}\\Module";

        if (!class_exists($configClass)) {
            $this->warn("Skipping {$moduleName}: Module.php config not found.");
            return;
        }

        // Get Parent Config
        $name = defined("{$configClass}::NAME") ? constant("{$configClass}::NAME") : $moduleName;
        $slug = defined("{$configClass}::SLUG") ? constant("{$configClass}::SLUG") : \Illuminate\Support\Str::slug($moduleName);
        $isActive = true; // Default to active

        $this->info("Syncing Parent: {$name} ({$slug})");

        // Sync Parent to DB
        $parentId = DB::connection('pgsql_app')->table('modules')->updateOrInsert(
            ['slug' => $slug, 'parent_id' => null],
            [
                'name' => $name,
                'is_active' => $isActive,
                'updated_at' => now(),
            ]
        );

        // updateOrInsert doesn't return ID, so fetch it
        $parentModule = DB::connection('pgsql_app')->table('modules')->where('slug', $slug)->first();

        // Sync Levels for Parent
        $this->syncLevels($parentModule->id);

        // Process Sub-Modules
        $subModulesPath = $dir . '/SubModules';
        if (File::exists($subModulesPath)) {
            $subDirs = File::directories($subModulesPath);
            foreach ($subDirs as $subDir) {
                $this->processSubModule($subDir, $parentModule->id, $moduleName);
            }
        }
    }

    private function processSubModule(string $dir, int $parentId, string $parentNamespace)
    {
        $subModuleName = basename($dir);
        $configPath = $dir . '/Config.php';

        if (!File::exists($configPath)) {
            $this->warn("  Skipping SubModule {$subModuleName}: Config.php not found.");
            return;
        }

        // Load Config array
        $config = include($configPath);

        if (!is_array($config) || !isset($config['slug'])) {
            $this->error("  Invalid config for {$subModuleName}");
            return;
        }

        $name = $config['name'] ?? $subModuleName;
        $slug = $config['slug'];
        $order = $config['order'] ?? 0;

        $this->info("  -> Syncing Child: {$name} ({$slug})");

        // Sync Child to DB
        DB::connection('pgsql_app')->table('modules')->updateOrInsert(
            ['slug' => $slug, 'parent_id' => $parentId],
            [
                'name' => $name,
                'is_active' => true,
                'order' => $order,
                'updated_at' => now(),
            ]
        );

        // We don't need to sync levels for sub-modules as they use parent's levels?
        // Wait, the new `sub_module_permissions` table links to `module_levels`.
        // `module_levels` belongs to a module_id.
        // If we want granular control, sub-modules might need their own levels OR link to parent's levels.
        // The implementation plan says "Sub-modules reuse parent module's levels".
        // So `sub_module_permissions.module_level_id` should point to Parent's levels.
        // RbacService logic: `ModuleLevel::where('module_id', $parentModule->id)`
        // So we do NOT create levels for sub-modules. Correct.
    }

    private function syncLevels(int $moduleId)
    {
        foreach ($this->levels as $slug => $hierarchy) {
            DB::connection('pgsql_app')->table('module_levels')->updateOrInsert(
                ['module_id' => $moduleId, 'slug' => $slug],
                [
                    'name' => ucfirst($slug),
                    'priority' => $hierarchy,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
