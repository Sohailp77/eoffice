<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubModulePermission extends Model
{
    protected $connection = 'pgsql_app';
    protected $table = 'sub_module_permissions';

    protected $fillable = [
        'user_id',
        'sub_module_id',
        'module_level_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'userid')
            ->setConnection('pgsql');
    }

    public function subModule()
    {
        return $this->belongsTo(Module::class, 'sub_module_id');
    }

    public function level()
    {
        return $this->belongsTo(ModuleLevel::class, 'module_level_id');
    }
}
