<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SystemRoleUser extends Pivot
{
    protected $connection = 'pgsql_app';
    protected $table = 'system_role_user';
}
