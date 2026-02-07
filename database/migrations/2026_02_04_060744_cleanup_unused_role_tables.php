<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old role system tables (replaced by system_roles)
        Schema::connection('pgsql_app')->dropIfExists('permission_user');
        Schema::connection('pgsql_app')->dropIfExists('permission_role');
        Schema::connection('pgsql_app')->dropIfExists('role_user');
        Schema::connection('pgsql_app')->dropIfExists('permissions');
        Schema::connection('pgsql_app')->dropIfExists('roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tables if rollback is needed
        Schema::connection('pgsql_app')->create('roles', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::connection('pgsql_app')->create('permissions', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::connection('pgsql_app')->create('role_user', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
        });

        Schema::connection('pgsql_app')->create('permission_role', function ($table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
        });

        Schema::connection('pgsql_app')->create('permission_user', function ($table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }
};
