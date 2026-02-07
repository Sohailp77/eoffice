<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Module;
use App\Models\ModuleLevel;
use App\Models\UserModuleLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminModuleAccessController extends Controller
{
    /**
     * Display list of users with their module access summary
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%")
                    ->orWhere('full_name', 'ILIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('userid')->paginate(20);

        // Load module access for each user
        foreach ($users as $user) {
            $user->moduleAccess = $user->moduleLevels()->with(['module', 'level'])->get();
        }

        return view('admin.module-access.index', compact('users'));
    }

    /**
     * Show form to manage a specific user's module access
     */
    public function edit($userId)
    {
        $user = User::findOrFail($userId);

        // Get all available modules
        $modules = Module::where('is_active', true)
            ->whereNull('parent_id') // Only get top-level first
            ->with(['levels', 'subModules.levels']) // Eager load levels for both parent and children
            ->orderBy('name')
            ->get();

        // Get user's current module access
        $userAccess = UserModuleLevel::where('user_id', $userId)
            ->with(['module', 'level'])
            ->get()
            ->keyBy('module_id');

        return view('admin.module-access.edit', compact('user', 'modules', 'userAccess'));
    }

    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'modules' => 'nullable|array',
            'modules.*' => 'exists:pgsql_app.modules,id',
            'levels' => 'nullable|array',
        ]);

        DB::connection('pgsql_app')->beginTransaction();

        try {
            // Remove all existing access for this user
            UserModuleLevel::where('user_id', $userId)->delete();

            $assignedCount = 0;

            // Add new access based on form submission
            if ($request->has('modules') && is_array($request->modules)) {
                foreach ($request->modules as $moduleId) {
                    $levelId = $request->levels[$moduleId] ?? null;

                    if (!$levelId) {
                        // If no level selected, default to the lowest priority (Browse)
                        $defaultLevel = \App\Models\ModuleLevel::where('module_id', $moduleId)
                            ->orderBy('priority', 'asc')
                            ->first();

                        $levelId = $defaultLevel ? $defaultLevel->id : null;
                    }

                    if ($levelId) {
                        UserModuleLevel::create([
                            'user_id' => $userId,
                            'module_id' => $moduleId,
                            'module_level_id' => $levelId,
                        ]);
                        $assignedCount++;
                    }
                }
            }

            DB::connection('pgsql_app')->commit();

            $message = $assignedCount > 0
                ? "Successfully assigned {$assignedCount} module(s) to {$user->username}"
                : "Removed all module access for {$user->username}";

            return redirect()
                ->route('admin.module-access.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::connection('pgsql_app')->rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update module access: ' . $e->getMessage());
        }
    }
}
