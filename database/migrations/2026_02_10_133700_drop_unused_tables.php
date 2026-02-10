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
        Schema::connection('pgsql_app')->dropIfExists('sub_module_permissions');
        Schema::connection('pgsql_app')->dropIfExists('module_level_routes');
        Schema::connection('pgsql_app')->dropIfExists('permission_user'); // Just in case
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't really want to reverse this as we are deleting the logic source too.
        // But for correctness we could define them.
        // For now, leaving empty as these are legacy/unused.
    }
};
