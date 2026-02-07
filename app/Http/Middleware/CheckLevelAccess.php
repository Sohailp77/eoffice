<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ModuleLevel;

class CheckLevelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredLevelSlug): Response
    {
        // Retrieve context from CheckModuleAccess
        $module = $request->attributes->get('current_module');
        $userLevel = $request->attributes->get('user_module_level');

        if (!$module || !$userLevel) {
            // Should not happen if module middleware is used correctly
            abort(403, 'Module context missing.');
        }

        // Find the required level priority for this module
        $requiredLevel = ModuleLevel::where('module_id', $module->id)
            ->where('slug', $requiredLevelSlug)
            ->first();

        if (!$requiredLevel) {
            abort(500, "Invalid level configuration: Level '$requiredLevelSlug' not found for module.");
        }

        // Priority Check: User Priority >= Required Priority
        // Assuming Higher Number = Higher Privilege? Or 1=Read, 2=Write?
        // "priority" usually implies Order.
        // Prompt example: "user_level.priority >= required_level.priority"
        // If 1=Read, 2=Write. User has 2 (Write). Route needs 1 (Read). 2 >= 1. OK.
        // User has 1 (Read). Route needs 2 (Write). 1 >= 2. False. OK.

        if ($userLevel->priority >= $requiredLevel->priority) {
            return $next($request);
        }

        abort(403, 'Insufficient access level.');
    }
}
