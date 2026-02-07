<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public $modules;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $user = Auth::user();

        if ($user) {
            // Fetch all modules the user has access to
            $userModuleLevels = $user->moduleLevels()->with('module')->get();

            $allModules = $userModuleLevels->map(function ($uml) {
                return $uml->module;
            })->filter(function ($module) {
                return $module->is_active;
            });

            // Group by parent_id
            $parents = $allModules->whereNull('parent_id');
            $children = $allModules->whereNotNull('parent_id')->groupBy('parent_id');

            // Attach children to parents
            $this->modules = $parents->map(function ($parent) use ($children) {
                $parent->subModules = $children->get($parent->id, collect([]));
                return $parent;
            });
        } else {
            $this->modules = collect([]);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
