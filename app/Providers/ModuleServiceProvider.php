<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $modulesPath = app_path('Modules');

        if (File::exists($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $module) {
                $moduleName = basename($module); // e.g., Library
                $providerName = "{$moduleName}ServiceProvider";
                $providerClass = "App\\Modules\\{$moduleName}\\Providers\\{$providerName}";

                // Manually require the file if it exists, to bypass Composer classmap caching issues on new files
                $providerPath = "{$module}/Providers/{$providerName}.php";
                if (File::exists($providerPath)) {
                    require_once $providerPath;
                }

                if (class_exists($providerClass)) {
                    $this->app->register($providerClass);
                }
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // No need to manually load routes/views; the individual Module ServiceProviders do that.
    }
}
