<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleLevel extends Model
{
    protected $connection = 'pgsql_app';
    protected $fillable = ['module_id', 'name', 'slug', 'priority'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function levels()
    {
        return $this->hasMany(ModuleLevel::class, 'parent_id');
    }

}
