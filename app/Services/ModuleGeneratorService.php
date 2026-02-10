<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class ModuleGeneratorService
{
    public function generate(string $name, string $slug)
    {
        $studlyName = Str::studly($slug);
        $modulePath = app_path("Modules/{$studlyName}");

        if (File::exists($modulePath)) {
            throw new \Exception("Module directory already exists: {$modulePath}");
        }

        // 1. Create Directories
        $directories = [
            "$modulePath/Controllers",
            "$modulePath/Models",
            "$modulePath/Database/Migrations",
            "$modulePath/Database/Seeders",
            "$modulePath/Providers",
            "$modulePath/Resources/Views",
            "$modulePath/Routes",
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

        // 2. Create Module.php
        $this->createModuleFile($modulePath, $name, $slug, $studlyName);

        // 3. Create ServiceProvider
        $this->createServiceProvider($modulePath, $slug, $studlyName);

        // 4. Create Routes
        $this->createRoutesFile($modulePath, $slug, $studlyName);

        // 5. Create basic Controller (optional, maybe later)

        // 6. Create Parent Index View
        $this->createModuleIndexView($modulePath, $name, $slug);

        Artisan::call('route:clear');

        return $modulePath;
    }

    protected function createModuleIndexView($path, $name, $slug)
    {
        $content = <<<HTML
<x-layouts.app>
    <x-ui.page-header title="{$name} Dashboard" description="Overview of {$name} sub-modules" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse(\$subModules as \$sub)
            @php
                \$hasAccess = auth()->user()->hasModuleAccess(\$sub->slug, 1);
            @endphp

            @if(\$hasAccess)
                <a href="{{ route('{$slug}.' . \$sub->slug . '.index') }}" 
                   class="group block p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-brand-primary/50 dark:hover:border-brand-primary/50 transition-all duration-300 shadow-sm hover:shadow-md relative overflow-hidden">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-brand-primary transition-colors relative z-10">
                        {{ \$sub->name }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm relative z-10">
                        Manage {{ \$sub->name }} records
                    </p>
                    
                    <div class="mt-4 flex items-center text-sm text-brand-primary font-medium opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                        <span>Access Module</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </a>
            @else
                <!-- Locked Module -->
                <div class="group block p-6 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-800 relative overflow-hidden opacity-75 cursor-not-allowed">
                    <div class="absolute inset-0 bg-gray-100/50 dark:bg-gray-800/50 backdrop-blur-[1px]"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-500 dark:text-gray-500">
                                {{ \$sub->name }}
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 dark:text-gray-600 text-sm">
                            Access Restricted
                        </p>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full">
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-16 h-16 mx-auto bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No Sub-modules Found</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Create a sub-module to get started.</p>
                </div>
            </div>
        @endforelse
    </div>
</x-layouts.app>
HTML;
        File::put("$path/Resources/Views/index.blade.php", $content);
    }

    protected function createRoutesFile($path, $slug, $studlyName)
    {
        $content = <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use App\Models\Module;

// Define your module routes here
Route::get('/', function () {
    \$module = Module::where('slug', '{$slug}')->first();
    \$subModules = \$module ? \$module->subModules()->where('is_active', true)->get() : collect();
    
    return view('{$slug}::index', compact('module', 'subModules'));
})->name('index');

// Sub-module routes will be appended here
PHP;
        File::put("$path/Routes/web.php", $content);
    }


    protected function createServiceProvider($path, $slug, $studlyName)
    {
        $content = <<<PHP
<?php

namespace App\Modules\\{$studlyName}\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class {$studlyName}ServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        \$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        \$this->loadViewsFrom(__DIR__ . '/../Resources/Views', '{$slug}');

        \$this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth', 'module:{$slug}'])
            ->prefix('{$slug}')
            ->name('{$slug}.')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
PHP;
        File::put("$path/Providers/{$studlyName}ServiceProvider.php", $content);
    }



    public function generateSubModule(string $parentSlug, string $subName, string $subSlug)
    {
        $parentStudly = Str::studly($parentSlug);
        $modulePath = app_path("Modules/{$parentStudly}");

        $modelName = Str::studly(Str::singular($subSlug));
        $controllerName = Str::studly($subSlug) . 'Controller';

        // 1. Model
        $this->createSubModuleModel($modulePath, $parentStudly, $modelName, $subSlug);

        // 2. Controller
        $this->createSubModuleController($modulePath, $parentStudly, $controllerName, $modelName, $subSlug, $parentSlug, $subName);

        // 3. Views
        $this->createSubModuleViews($modulePath, $subName, $subSlug, $parentSlug);

        // 4. Migration
        $this->createSubModuleMigration($modulePath, $subSlug, $parentSlug);

        // 5. Route
        $this->appendSubModuleRoute($parentSlug, $subSlug, $controllerName);

        Artisan::call('route:clear');
    }

    protected function createSubModuleModel($path, $namespace, $modelName, $subSlug)
    {
        $table = Str::snake($namespace) . '_' . Str::snake($subSlug); // e.g. library_books

        $content = <<<PHP
<?php

namespace App\Modules\\{$namespace}\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {$modelName} extends Model
{
    use SoftDeletes;

    protected \$table = '{$table}';

    protected \$fillable = ['name', 'description'];
}
PHP;
        File::put("{$path}/Models/{$modelName}.php", $content);
    }

    protected function createSubModuleController($path, $namespace, $controllerName, $modelName, $subSlug, $parentSlug, $subName)
    {
        $varName = Str::camel($modelName); // book
        $parentTitle = Str::studly($parentSlug);

        $content = <<<PHP
<?php

namespace App\Modules\\{$namespace}\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\\{$namespace}\Models\\{$modelName};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class {$controllerName} extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasModuleAccess('{$subSlug}', 1)) {
            abort(403, 'Unauthorized module access.');
        }

        \${$subSlug} = {$modelName}::latest()->paginate(10);
        
        \$crumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')], // path of Main dashboard
            ['label' => '{$parentTitle}', 'url' => route('{$parentSlug}.index')],
            ['label' => '{$subName}', 'url' => route('{$parentSlug}.{$subSlug}.index')],
        ];

        return view('{$parentSlug}::{$subSlug}.index', compact('{$subSlug}', 'crumbs'));
    }

    public function create()
    {
        if (!auth()->user()->hasModuleAccess('{$subSlug}', 3)) {
            abort(403, 'Unauthorized action.');
        }

        \$crumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')], // path of Main dashboard
            ['label' => '{$parentTitle}', 'url' => route('{$parentSlug}.index')],
            ['label' => '{$subName}', 'url' => route('{$parentSlug}.{$subSlug}.index')],
            ['label' => 'Create New'],
        ];

        return view('{$parentSlug}::{$subSlug}.create', compact('crumbs'));
    }

    public function store(Request \$request)
    {
        if (!auth()->user()->hasModuleAccess('{$subSlug}', 3)) {
            abort(403, 'Unauthorized action.');
        }

        \$validated = \$request->validate([
            'name' => 'required|string|max:255',
            // Add more validation here
        ]);

        {$modelName}::create(\$validated);

        return redirect()->route('{$parentSlug}.{$subSlug}.index')->with('success', '{$subName} created successfully.');
    }

    public function show({$modelName} \${$varName})
    {
        if (!auth()->user()->hasModuleAccess('{$subSlug}', 1)) {
            abort(403, 'Unauthorized module access.');
        }

        \$crumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')], // path of Main dashboard
            ['label' => '{$parentTitle}', 'url' => route('{$parentSlug}.index')],
            ['label' => '{$subName}', 'url' => route('{$parentSlug}.{$subSlug}.index')],
            ['label' => \${$varName}->name],
        ];

        return view('{$parentSlug}::{$subSlug}.show', compact('{$varName}', 'crumbs'));
    }

    public function edit({$modelName} \${$varName})
    {
        if (!auth()->user()->hasModuleAccess('{$subSlug}', 3)) {
            abort(403, 'Unauthorized action.');
        }

        \$crumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')], // path of Main dashboard
            ['label' => '{$parentTitle}', 'url' => route('{$parentSlug}.index')],
            ['label' => '{$subName}', 'url' => route('{$parentSlug}.{$subSlug}.index')],
            ['label' => 'Edit: ' . \${$varName}->name],
        ];

        return view('{$parentSlug}::{$subSlug}.edit', compact('{$varName}', 'crumbs'));
    }

    public function update(Request \$request, {$modelName} \${$varName})
    {
        if (!auth()->user()->hasModuleAccess('{$subSlug}', 3)) {
            abort(403, 'Unauthorized action.');
        }

        \$validated = \$request->validate([
            'name' => 'required|string|max:255',
        ]);

        \${$varName}->update(\$validated);

        return redirect()->route('{$parentSlug}.{$subSlug}.index')->with('success', '{$subName} updated successfully.');
    }

    public function destroy({$modelName} \${$varName})
    {
        if (!auth()->user()->hasModuleAccess('{$subSlug}', 3)) {
            abort(403, 'Unauthorized action.');
        }

        \${$varName}->delete();
        return redirect()->route('{$parentSlug}.{$subSlug}.index')->with('success', '{$subName} deleted successfully.');
    }
}
PHP;
        File::put("{$path}/Controllers/{$controllerName}.php", $content);
    }

    protected function createSubModuleViews($path, $subName, $subSlug, $parentSlug)
    {
        $viewPath = "{$path}/Resources/Views/{$subSlug}";
        File::makeDirectory($viewPath, 0755, true);

        // Index
        File::put(
            "{$viewPath}/index.blade.php",
            <<<HTML
<x-layouts.app>
    <x-ui.breadcrumbs :crumbs="\$crumbs" />
    <x-ui.page-header title="{$subName} List" description="Manage {$subName}" />
    
    @if(auth()->user()->hasModuleAccess('{$subSlug}', 3))
    <div class="mb-6">
        <x-ui.button tag="a" href="{{ route('{$parentSlug}.{$subSlug}.create') }}" variant="primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create New
        </x-ui.button>
    </div>
    @endif

    <x-ui.table>
        <x-slot:header>
            <tr class="bg-gray-50 dark:bg-gray-900/50">
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                @if(auth()->user()->hasModuleAccess('{$subSlug}', 3))
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                @endif
            </tr>
        </x-slot:header>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
            @forelse(\${$subSlug} as \$item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ \$item->name }}</td>
                    @if(auth()->user()->hasModuleAccess('{$subSlug}', 3))
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <x-ui.button tag="a" href="{{ route('{$parentSlug}.{$subSlug}.edit', \$item) }}" variant="ghost" size="sm">
                                Edit
                            </x-ui.button>
                            <form action="{{ route('{$parentSlug}.{$subSlug}.destroy', \$item) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 p-1.5 rounded-lg transition-all" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <x-slot:footer>
            {{ \${$subSlug}->links() }}
        </x-slot:footer>
    </x-ui.table>
</x-layouts.app>
HTML
        );

        // Create
        File::put(
            "{$viewPath}/create.blade.php",
            <<<HTML
<x-layouts.app>
    <x-ui.breadcrumbs :crumbs="\$crumbs" />
    <div class="mb-6">
        <x-ui.button tag="a" href="{{ route('{$parentSlug}.{$subSlug}.index') }}" variant="ghost" size="sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to List
        </x-ui.button>
    </div>

    <x-ui.page-header title="Create {$subName}" description="Add a new item" />
    
    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('{$parentSlug}.{$subSlug}.store') }}">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Name</label>
                <input type="text" name="name" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none ring-1 ring-brand-primary focus:ring-2 focus:ring-brand-primary/50 transition-all shadow-sm" 
                    required>
            </div>
            
            <div class="flex items-center gap-4 border-t border-gray-100 dark:border-gray-700 pt-6">
                <x-ui.button type="submit" variant="primary">Save</x-ui.button>
                <x-ui.button tag="a" href="{{ route('{$parentSlug}.{$subSlug}.index') }}" variant="secondary">Cancel</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
HTML
        );

        // Edit
        File::put(
            "{$viewPath}/edit.blade.php",
            <<<HTML
<x-layouts.app>
    <x-ui.breadcrumbs :crumbs="\$crumbs" />
    <div class="mb-6">
        <x-ui.button tag="a" href="{{ route('{$parentSlug}.{$subSlug}.index') }}" variant="ghost" size="sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to List
        </x-ui.button>
    </div>

    <x-ui.page-header title="Edit {$subName}" description="Edit item details" />
    
    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('{$parentSlug}.{$subSlug}.update', \${$subSlug}->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Name</label>
                <input type="text" name="name" value="{{ \${$subSlug}->name }}" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none ring-1 ring-brand-primary focus:ring-2 focus:ring-brand-primary/50 transition-all shadow-sm" 
                    required>
            </div>
            
            <div class="flex items-center gap-4 border-t border-gray-100 dark:border-gray-700 pt-6">
                <x-ui.button type="submit" variant="primary">Update</x-ui.button>
                <x-ui.button tag="a" href="{{ route('{$parentSlug}.{$subSlug}.index') }}" variant="secondary">Cancel</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
HTML
        );

        // Show
        File::put(
            "{$viewPath}/show.blade.php",
            <<<HTML
<x-layouts.app>
    <x-ui.breadcrumbs :crumbs="\$crumbs" />
    <div class="mb-6">
        <x-ui.button tag="a" href="{{ route('{$parentSlug}.{$subSlug}.index') }}" variant="ghost" size="sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to List
        </x-ui.button>
    </div>

    <x-ui.page-header title="{$subName} Details" description="View details" />
    
    <x-ui.card>
         <h2 class="text-2xl text-gray-900 dark:text-white font-bold mb-4">{{ \${$subSlug}->name }}</h2>
         <p class="text-gray-500 dark:text-gray-400">Created At: {{ \${$subSlug}->created_at->format('M d, Y') }}</p>
    </x-ui.card>
</x-layouts.app>
HTML
        );
    }

    protected function createSubModuleMigration($path, $subSlug, $parentSlug)
    {
        $table = Str::snake($parentSlug) . '_' . Str::snake($subSlug);
        $timestamp = date('Y_m_d_His');
        $className = 'Create' . Str::studly($table) . 'Table';

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
        File::put("{$path}/Database/Migrations/{$timestamp}_create_{$table}_table.php", $content);
    }

    public function appendSubModuleRoute(string $parentSlug, string $subSlug, string $controllerName)
    {
        $parentStudly = Str::studly($parentSlug);
        $routePath = app_path("Modules/{$parentStudly}/Routes/web.php");

        if (!File::exists($routePath)) {
            throw new \Exception("Route file not found for module: {$parentSlug}");
        }

        $appendContent = "\nRoute::resource('{$subSlug}', \\App\\Modules\\{$parentStudly}\\Controllers\\{$controllerName}::class);";

        File::append($routePath, $appendContent);
    }
    public function delete(string $slug)
    {
        $studlyName = Str::studly($slug);
        $modulePath = app_path("Modules/{$studlyName}");

        if (File::exists($modulePath)) {
            File::deleteDirectory($modulePath);
        }

        Artisan::call('route:clear');
    }

    public function deleteSubModule(string $parentSlug, string $subSlug)
    {
        $parentStudly = Str::studly($parentSlug);
        $modulePath = app_path("Modules/{$parentStudly}");

        $modelName = Str::studly(Str::singular($subSlug));
        $controllerName = Str::studly($subSlug) . 'Controller';

        // 1. Delete Controller
        $controllerPath = "{$modulePath}/Controllers/{$controllerName}.php";
        if (File::exists($controllerPath)) {
            File::delete($controllerPath);
        }

        // 2. Delete Model
        $modelPath = "{$modulePath}/Models/{$modelName}.php";
        if (File::exists($modelPath)) {
            File::delete($modelPath);
        }

        // 3. Delete Views
        $viewPath = "{$modulePath}/Resources/Views/{$subSlug}";
        if (File::exists($viewPath)) {
            File::deleteDirectory($viewPath);
        }

        // 4. Delete Migration (Wildcard search)
        $migrationPattern = "{$modulePath}/Database/Migrations/*_create_{$parentSlug}_{$subSlug}_table.php";
        // The pattern above needs careful matching because snake case might vary.
        // Let's iterate directory to be safe.
        $migrationPath = "{$modulePath}/Database/Migrations";
        if (File::exists($migrationPath)) {
            $files = File::files($migrationPath);
            foreach ($files as $file) {
                if (str_contains($file->getFilename(), "_create_" . Str::snake($parentSlug) . "_" . Str::snake($subSlug) . "_table")) {
                    File::delete($file->getPathname());
                }
            }
        }

        // 5. Cleanup Route
        $this->removeSubModuleRoute($parentSlug, $subSlug);

        // 6. Drop Table
        $table = Str::snake($parentSlug) . '_' . Str::snake($subSlug);
        Schema::dropIfExists($table);

        Artisan::call('route:clear');
    }

    protected function removeSubModuleRoute(string $parentSlug, string $subSlug)
    {
        $parentStudly = Str::studly($parentSlug);
        $routePath = app_path("Modules/{$parentStudly}/Routes/web.php");

        if (!File::exists($routePath)) {
            return;
        }

        $lines = file($routePath);
        $newLines = [];
        $resourcePattern = "/Route::resource\(['\"]{$subSlug}['\"]/";

        foreach ($lines as $line) {
            if (!preg_match($resourcePattern, $line)) {
                $newLines[] = $line;
            }
        }

        File::put($routePath, implode("", $newLines));
    }

    protected function createModuleFile($path, $name, $slug, $studlyName)
    {
        $content = <<<PHP
<?php

namespace App\Modules\\{$studlyName};

class Module
{
    const NAME = '{$name}';
    const SLUG = '{$slug}';
    const DESCRIPTION = '{$name} Module';
    const VERSION = '1.0.0';

    const LEVELS = [
        'browse' => 1,
        'read' => 2,
        'write' => 3,
        'manage' => 4,
    ];
}
PHP;
        File::put("$path/Module.php", $content);
    }
}
