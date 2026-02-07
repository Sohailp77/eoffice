<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ToggleDevMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:toggle-access {--enable} {--disable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toggle development mode for automatic module access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if ($this->option('enable')) {
            if (config('app.env') !== 'local') {
                $this->error('Development mode can only be enabled in local environment!');
                return 1;
            }

            // Add or update the setting
            if (str_contains($envContent, 'DEV_MODE_BYPASS_ACCESS')) {
                $envContent = preg_replace(
                    '/DEV_MODE_BYPASS_ACCESS=.*/',
                    'DEV_MODE_BYPASS_ACCESS=true',
                    $envContent
                );
            } else {
                $envContent .= "\n# Development Mode - Auto-grant module access (DO NOT USE IN PRODUCTION)\nDEV_MODE_BYPASS_ACCESS=true\n";
            }

            file_put_contents($envPath, $envContent);
            $this->info('✓ Development mode ENABLED - All authenticated users now have full access to all modules');
            $this->warn('⚠ Remember to disable this before deploying to production!');

        } elseif ($this->option('disable')) {
            $envContent = preg_replace(
                '/DEV_MODE_BYPASS_ACCESS=.*/',
                'DEV_MODE_BYPASS_ACCESS=false',
                $envContent
            );

            file_put_contents($envPath, $envContent);
            $this->info('✓ Development mode DISABLED - Normal access control restored');

        } else {
            // Show status
            $status = config('app.dev_mode_bypass_access', false);
            $this->info('Development Mode Status: ' . ($status ? 'ENABLED ✓' : 'DISABLED'));

            if ($status) {
                $this->warn('All authenticated users have full access to all modules');
            }

            $this->line('');
            $this->line('Usage:');
            $this->line('  php artisan dev:toggle-access --enable   Enable dev mode');
            $this->line('  php artisan dev:toggle-access --disable  Disable dev mode');
        }

        return 0;
    }
}
