<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $connection = 'pgsql_app';

    protected $fillable = ['name', 'slug', 'parent_id', 'is_active', 'order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get parent module
     */
    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    /**
     * Get sub-modules
     */
    public function subModules()
    {
        return $this->hasMany(Module::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get all sub-modules recursively
     */
    public function allSubModules()
    {
        return $this->subModules()->with('allSubModules');
    }

    /**
     * Check if this is a parent module
     */
    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * Check if this is a sub-module
     */
    public function isSubModule(): bool
    {
        return $this->parent_id !== null;
    }

    public function levels()
    {
        return $this->hasMany(ModuleLevel::class);
    }
}
