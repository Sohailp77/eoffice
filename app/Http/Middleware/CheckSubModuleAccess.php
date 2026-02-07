<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RbacService;

class CheckSubModuleAccess
{
    protected $rbacService;

    public function __construct(RbacService $rbacService)
    {
        $this->rbacService = $rbacService;
    }

    /**
     * Handle sub-module access check
     * 
     * Usage: ->middleware('submodule:parent_slug,sub_slug,level')
     */
    public function handle(Request $request, Closure $next, string $parentSlug, string $subSlug, string $level = 'browse')
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if (!$this->rbacService->userHasSubModuleAccess($user, $parentSlug, $subSlug, $level)) {
            abort(403, "You don't have {$level} access to this sub-module.");
        }

        return $next($request);
    }
}
