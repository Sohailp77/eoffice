<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use App\Models\ModuleLevel;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $module = Module::where('slug', $moduleSlug)->first();

        if (!$module || !$module->is_active) {
            abort(404);
        }

        // DEVELOPMENT MODE: Auto-grant highest access level
        if (config('app.env') === 'local' && config('app.dev_mode_bypass_access', false)) {
            // Get the highest priority level for this module
            $highestLevel = ModuleLevel::where('module_id', $module->id)
                ->orderBy('priority', 'desc')
                ->first();

            if ($highestLevel) {
                $request->attributes->set('current_module', $module);
                $request->attributes->set('user_module_level', $highestLevel);
                $request->attributes->set('dev_mode_access', true); // Flag for debugging

                return $next($request);
            }
        }

        // PRODUCTION MODE: Check actual user access
        $userModuleLevel = $user->moduleLevels()
            ->where('module_id', $module->id)
            ->with('level')
            ->first();

        if (!$userModuleLevel) {
            abort(403, 'Unauthorized module access.');
        }

        $request->attributes->set('current_module', $module);
        $request->attributes->set('user_module_level', $userModuleLevel->level);

        return $next($request);
    }
}
