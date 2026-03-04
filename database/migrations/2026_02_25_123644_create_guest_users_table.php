<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('pgsql_app')->create('guest_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('mobile')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Offset the ID sequence so it starts at 1,000,000,000
        // This avoids collisions with legacy 'users' table IDs when stored in 'system_role_user' etc.
        \Illuminate\Support\Facades\DB::connection('pgsql_app')->statement('ALTER SEQUENCE guest_users_id_seq RESTART WITH 1000000000');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql_app')->dropIfExists('guest_users');
    }
};
