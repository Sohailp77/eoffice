<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = 'pgsql_app';

        // Drop existing tables if they exist to ensure clean slate
        Schema::connection($connection)->dropIfExists('user_module_levels');
        Schema::connection($connection)->dropIfExists('module_level_routes');
        // Drop sub_module_permissions dependent on module_levels
        Schema::connection($connection)->dropIfExists('sub_module_permissions');
        Schema::connection($connection)->dropIfExists('module_levels');
        // Drop module_permission if it exists from previous architecture to avoid FK issues when dropping modules
        Schema::connection($connection)->dropIfExists('module_permission');
        Schema::connection($connection)->dropIfExists('modules');
        Schema::connection($connection)->dropIfExists('system_role_user');
        Schema::connection($connection)->dropIfExists('system_roles');

        Schema::connection($connection)->create('system_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::connection($connection)->create('system_role_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // From ecourtisuserdb
            $table->foreignId('system_role_id')->constrained('system_roles')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'system_role_id']);
        });

        Schema::connection($connection)->create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::connection($connection)->create('module_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->integer('priority'); // 1=Read, 2=Write, etc.
            $table->timestamps();

            $table->unique(['module_id', 'slug']);
        });

        // Module Level Routes removed as unused
        // Schema::connection($connection)->create('module_level_routes', ...);

        Schema::connection($connection)->create('user_module_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // From ecourtisuserdb
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('module_level_id')->constrained('module_levels')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'module_id']); // One level per module per user
        });
    }

    public function down(): void
    {
        $connection = 'pgsql_app';
        Schema::connection($connection)->dropIfExists('user_module_levels');
        Schema::connection($connection)->dropIfExists('module_level_routes');
        Schema::connection($connection)->dropIfExists('module_levels');
        Schema::connection($connection)->dropIfExists('modules');
        Schema::connection($connection)->dropIfExists('system_role_user');
        Schema::connection($connection)->dropIfExists('system_roles');
    }
};
