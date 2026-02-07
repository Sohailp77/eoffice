<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Module;
use App\Models\ModuleLevel;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'userid';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'full_name',
        'user_password',
        'mobile',
        'display',
        'ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'userid';
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dt_of_creation' => 'datetime',
        ];
    }

    /**
     * The roles that belong to the user.
     * Note: This is an SQLite relationship from a PGSQL model.
     * We cannot use standard belongsToMany efficiently with cross-database joins usually,
     * but Laravel supports it if we are careful or use `setConnection`.
     * Actually, standard belongsToMany works across connections in Laravel if configured right,
     * but we cannot use database-level constraints.
     */
    /**
     * System roles relationship (admin/user).
     */
    public function systemRoles()
    {
        // Force pgsql_app connection for related SystemRole
        $role = new SystemRole;
        $role->setConnection('pgsql_app');

        return $this->belongsToMany(SystemRole::class, 'system_role_user', 'user_id', 'system_role_id')
            ->using(SystemRoleUser::class);
    }

    public function moduleLevels()
    {
        return $this->hasMany(UserModuleLevel::class, 'user_id');
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->systemRoles()->where('slug', 'admin')->exists();
    }

    /**
     * Check if user has access to a module at a minimum priority level.
     * 
     * @param string $moduleSlug
     * @param int $minPriority
     * @return bool
     */
    public function hasModuleAccess(string $moduleSlug, int $minPriority): bool
    {
        // 1. Check Request Context (Performance Optimization)
        $currentModule = request()->get('current_module'); // Set by middleware
        $userLevel = request()->get('user_module_level'); // Set by middleware

        if ($currentModule && $currentModule->slug === $moduleSlug && $userLevel) {
            return $userLevel->priority >= $minPriority;
        }

        // 2. Fallback to DB Query (if outside middleware context)
        $module = Module::where('slug', $moduleSlug)->first();
        if (!$module)
            return false;

        $userModuleLevel = $this->moduleLevels()
            ->where('module_id', $module->id)
            ->with('level')
            ->first();

        if (!$userModuleLevel || !$userModuleLevel->level)
            return false;

        return $userModuleLevel->level->priority >= $minPriority;
    }
}
