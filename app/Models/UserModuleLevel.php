<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModuleLevel extends Model
{
    protected $connection = 'pgsql_app';
    protected $table = 'user_module_levels';
    protected $fillable = ['user_id', 'module_id', 'module_level_id'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function level()
    {
        return $this->belongsTo(ModuleLevel::class, 'module_level_id');
    }
}
