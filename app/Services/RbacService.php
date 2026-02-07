<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RbacService
{
    /**
     * Check if a user has a specific permission.
     * Simplified: Only checks if user is admin.
     *
     * @param User $user
     * @param string $permissionSlug
     * @return bool
     */
    public function userHasPermission(User $user, string $permissionSlug): bool
    {
        // For now, all permissions require admin access
        return $user->isAdmin();
    }

    /**
     * Check if user has access to a specific module level
     */
    public function userHasLevel(User $user, string $moduleSlug, string $levelSlug): bool
    {
        $module = \App\Models\Module::where('slug', $moduleSlug)->first();
        if (!$module)
            return false;

        $userLevel = $user->moduleLevels()
            ->where('module_id', $module->id)
            ->first();

        if (!$userLevel)
            return false;

        $requiredLevel = \App\Models\ModuleLevel::where('module_id', $module->id)
            ->where('slug', $levelSlug)
            ->first();

        // Assuming 'priority' column matches 'priority' logic from CheckLevelAccess
        // You might need to check if your DB uses 'hierarchy' or 'priority'
        // Based on previous chats, it was 'priority'.
        return $userLevel->priority >= ($requiredLevel->priority ?? 0);
    }

    /**
     * Check if user has access to a sub-module
     */
    public function userHasSubModuleAccess(User $user, string $parentSlug, string $subSlug, string $requiredLevel = 'browse'): bool
    {
        // 1. Get parent module
        $parentModule = \App\Models\Module::where('slug', $parentSlug)->first();
        if (!$parentModule)
            return false;

        // 2. Get sub-module
        $subModule = \App\Models\Module::where('slug', $subSlug)
            ->where('parent_id', $parentModule->id)
            ->first();

        if (!$subModule)
            return false;

        // 3. Check if user has parent module access first
        if (!$this->userHasLevel($user, $parentSlug, 'browse')) {
            return false;
        }

        // 4. Check specific sub-module permission
        $subPermission = \App\Models\SubModulePermission::where('user_id', $user->userid)
            ->where('sub_module_id', $subModule->id)
            ->with('level')
            ->first();

        if (!$subPermission)
            return false;

        // 5. Get required level hierarchy
        // Sub-modules reuse parent's levels
        $requiredLevelObj = \App\Models\ModuleLevel::where('module_id', $parentModule->id)
            ->where('slug', $requiredLevel)
            ->first();

        // 6. Check hierarchy
        return $subPermission->level->priority >= ($requiredLevelObj->priority ?? 0);
    }

    /**
     * Get user's accessible sub-modules for a parent module
     */
    public function getUserSubModules(User $user, string $parentSlug): \Illuminate\Support\Collection
    {
        $parentModule = \App\Models\Module::where('slug', $parentSlug)->first();

        if (!$parentModule) {
            return collect([]);
        }

        return \App\Models\SubModulePermission::where('user_id', $user->userid)
            ->whereHas('subModule', function ($q) use ($parentModule) {
                $q->where('parent_id', $parentModule->id);
            })
            ->with(['subModule', 'level'])
            ->get()
            ->map(function ($perm) {
                return [
                    'module' => $perm->subModule,
                    'level' => $perm->level,
                ];
            });
    }

    /**
     * Invalidate cache for a user.
     *
     * @param int $userId
     * @return void
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("user_admin_status_{$userId}");
    }
}
