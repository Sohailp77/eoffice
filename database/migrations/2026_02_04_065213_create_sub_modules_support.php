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
        // Add hierarchy support to modules table
        Schema::connection('pgsql_app')->table('modules', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->integer('order')->default(0)->after('is_active');

            // Self-referencing FK
            $table->foreign('parent_id')->references('id')->on('modules')->onDelete('cascade');
        });

        // Create granular permissions table for sub-modules
        Schema::connection('pgsql_app')->create('sub_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sub_module_id'); // References modules.id
            $table->unsignedBigInteger('module_level_id'); // Browse/Read/Write/Manage
            $table->timestamps();

            // Constraints
            $table->unique(['user_id', 'sub_module_id']);
            $table->foreign('sub_module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('module_level_id')->references('id')->on('module_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql_app')->dropIfExists('sub_module_permissions');

        Schema::connection('pgsql_app')->table('modules', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'order']);
        });
    }
};
