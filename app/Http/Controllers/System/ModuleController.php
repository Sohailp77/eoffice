<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\ModuleGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class ModuleController extends Controller
{
    protected $generator;

    public function __construct(ModuleGeneratorService $generator)
    { // Inject Service
        $this->generator = $generator;
    }

    public function index()
    {
        $modules = DB::connection('pgsql_app')->table('modules')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('system.modules.index', compact('modules'));
    }

    public function create()
    {
        return view('system.modules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:pgsql_app.modules,slug',
        ]);

        // 1. Scaffold Files
        try {
            $this->generator->generate($validated['name'], $validated['slug']);
        } catch (\Exception $e) {
            return back()->withErrors(['name' => 'Failed to generate files: ' . $e->getMessage()]);
        }

        // 2. Register in DB
        // 2. Register in DB
        $moduleId = DB::connection('pgsql_app')->table('modules')->insertGetId([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2.1 Create Default 'View' Level for Parent (Container)
        DB::connection('pgsql_app')->table('module_levels')->insert([
            'module_id' => $moduleId,
            'name' => 'View',
            'slug' => "{$validated['slug']}.view",
            'priority' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Clear route cache to pick up new module routes
        Artisan::call('route:clear');

        return redirect()->route('system.modules.index')->with('success', 'Module created successfully. Please run "composer dump-autoload" if the new class is not found.');
    }

    public function show($id)
    {
        $module = DB::connection('pgsql_app')->table('modules')->find($id);
        $subModules = DB::connection('pgsql_app')->table('modules')->where('parent_id', $id)->get();
        return view('system.modules.show', compact('module', 'subModules'));
    }

    public function storeSubModule(Request $request, $id)
    {
        $parentModule = DB::connection('pgsql_app')->table('modules')->find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:50', 'unique:pgsql_app.modules,slug', 'regex:/^[a-z]+$/'], // Strict alphabetic only
        ]);

        $validated['slug'] = Str::lower($validated['slug']);


        // 1. Register Sub-module in DB
        $subModuleId = DB::connection('pgsql_app')->table('modules')->insertGetId([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'parent_id' => $parentModule->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 1.1 Create Default Levels for Sub-Module
        $this->createDefaultLevels($subModuleId, $validated['slug']);

        // 2. Scaffold Sub-Module (CRUD, Migration, Views, Route)
        try {
            $this->generator->generateSubModule($parentModule->slug, $validated['name'], $validated['slug']);
        } catch (\Exception $e) {
            // For now, warn
            return back()->with('warning', 'Sub-module registered in DB but scaffolding failed: ' . $e->getMessage());
        }

        // Clear route cache to pick up new sub-module routes
        Artisan::call('route:clear');

        return redirect()->route('system.modules.show', $id)->with('success', 'Sub-module registered and CRUD scaffolding created.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        DB::connection('pgsql_app')->table('modules')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'is_active' => $validated['is_active'] ?? true,
                'updated_at' => now(),
            ]);

        // If specific redirect needed (e.g. back to show), can adjust. 
        // Test expects assertRedirect().
        return redirect()->route('system.modules.index')->with('success', 'Module updated successfully.');
    }

    public function destroy($id)
    {
        $module = DB::connection('pgsql_app')->table('modules')->find($id);

        if (!$module) {
            return back()->with('error', 'Module not found.');
        }

        if ($module->parent_id) {
            // Delete Sub-module
            $parent = DB::connection('pgsql_app')->table('modules')->find($module->parent_id);
            if ($parent) {
                try {
                    $this->generator->deleteSubModule($parent->slug, $module->slug);
                } catch (\Exception $e) {
                    return back()->with('warning', 'DB record deleted but file cleanup failed: ' . $e->getMessage());
                }
            }

            DB::connection('pgsql_app')->table('modules')->delete($id);
            return redirect()->route('system.modules.show', $module->parent_id)->with('success', 'Sub-module deleted successfully.');
        } else {
            // Delete Parent Module
            try {
                $this->generator->delete($module->slug);
            } catch (\Exception $e) {
                return back()->with('warning', 'DB record deleted but file cleanup failed: ' . $e->getMessage());
            }

            DB::connection('pgsql_app')->table('modules')->delete($id);

            // Clear route cache after deletion
            Artisan::call('route:clear');

            return redirect()->route('system.modules.index')->with('success', 'Module deleted successfully.');
        }
    }




    protected function createDefaultLevels($moduleId, $slug)
    {
        $levels = [
            ['name' => 'Browse', 'slug' => 'browse', 'priority' => 1],
            ['name' => 'Read', 'slug' => 'read', 'priority' => 2],
            ['name' => 'Write', 'slug' => 'write', 'priority' => 3],
            ['name' => 'Manage', 'slug' => 'manage', 'priority' => 4],
        ];

        foreach ($levels as $level) {
            DB::connection('pgsql_app')->table('module_levels')->insert([
                'module_id' => $moduleId,
                'name' => $level['name'],
                'slug' => "{$slug}.{$level['slug']}",
                'priority' => $level['priority'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
