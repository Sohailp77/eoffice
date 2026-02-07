<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckModulePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $moduleSlug = null): Response
    {
        if (!Auth::check()) {
            abort(401);
        }

        $user = Auth::user();

        // If module slug is passed directly to middleware: ->middleware('module:dashboard-module')
        if ($moduleSlug) {
            if (!$this->checkModuleAccess($user, $moduleSlug)) {
                abort(403, 'Unauthorized access to this module.');
            }
            return $next($request);
        }

        // Keep dynamic check if needed (e.g. looking up module by route name or segment)
        // For now, we enforce explicit middleware usage like middleware('module:slug')
        // Or we can find module by route prefix if we implemented that.

        return $next($request);
    }

    protected function checkModuleAccess($user, $slug)
    {
        Log::info("Checking access for User {$user->id} to module '$slug'");

        $module = Module::where('slug', $slug)->first();

        if (!$module || !$module->active) {
            Log::info("Module '$slug' not found or inactive.");
            return false;
        }

        // Check module permissions
        // Usage: Module requires ANY of its permissions? Or ALL?
        // Requirement implies: "Module require one or more permissions"
        // "Access is granted ONLY if permission exists"
        // Usually, if a module is linked to permissions A and B, a user needs A OR B? Or A AND B?
        // "Modules require one or more permissions" -> Usually implies these are the GATEKEYS.
        // If a user has ONE of the required permissions, they can enter? Or must they have ALL?
        // Standard RBAC: A module is protected by a permission (e.g. 'access_dashboard').
        // If the module has multiple permissions linked in `module_permission`, we should probably check if user has AT LEAST ONE of them. 

        $requiredPermissions = $module->permissions->pluck('slug')->toArray();
        Log::info("Module '$slug' requires: " . implode(', ', $requiredPermissions));

        if (empty($requiredPermissions)) {
            // If module has no permissions assigned, is it public? 
            // Requirement: "All module routes must be protected"
            // "Access is granted ONLY if permission exists" implies default deny.
            Log::info("No permissions defined for module.");
            return false;
        }

        // Check if user has ANY of the required permissions
        foreach ($requiredPermissions as $permSlug) {
            if ($user->hasPermission($permSlug)) {
                Log::info("User has required permission '$permSlug'. Access GRANTED.");
                return true;
            }
        }

        Log::info("User missing required permissions. Access DENIED.");
        return false;
    }
}
