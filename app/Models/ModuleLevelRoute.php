<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleLevelRoute extends Model
{
    protected $connection = 'pgsql_app';
    protected $fillable = ['module_level_id', 'route_name'];

    public function level()
    {
        return $this->belongsTo(ModuleLevel::class);
    }
}
