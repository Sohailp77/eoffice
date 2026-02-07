<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemRole extends Model
{
    protected $connection = 'pgsql_app';
    protected $fillable = ['name', 'slug'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'system_role_user', 'system_role_id', 'user_id');
    }
}
