<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Module;
use App\Models\ModuleLevel;
use App\Models\UserModuleLevel;
use App\Models\SystemRole;
use App\Models\SystemRoleUser;

class GuestUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'pgsql_app';
    protected $table = 'guest_users';
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'mobile',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function systemRoles()
    {
        $role = new SystemRole;
        $role->setConnection('pgsql_app');

        return $this->belongsToMany(SystemRole::class, 'system_role_user', 'user_id', 'system_role_id')
            ->using(SystemRoleUser::class);
    }

    public function moduleLevels()
    {
        return $this->hasMany(UserModuleLevel::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->systemRoles()->where('slug', 'admin')->exists();
    }

    public function hasModuleAccess(string $moduleSlug, int $minPriority): bool
    {
        $currentModule = request()->get('current_module');
        $userLevel = request()->get('user_module_level');

        if ($currentModule && $currentModule->slug === $moduleSlug && $userLevel) {
            return $userLevel->priority >= $minPriority;
        }

        $module = Module::where('slug', $moduleSlug)->first();
        if (!$module) return false;

        $userModuleLevel = $this->moduleLevels()
            ->where('module_id', $module->id)
            ->with('level')
            ->first();

        if (!$userModuleLevel || !$userModuleLevel->level) return false;

        return $userModuleLevel->level->priority >= $minPriority;
    }
}
