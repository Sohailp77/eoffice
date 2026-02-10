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
        Schema::connection('pgsql_app')->dropIfExists('books');
        Schema::connection('pgsql_app')->create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('file_path'); // Path to PDF file
            $table->bigInteger('file_size')->default(0); // Size in bytes
            $table->string('thumbnail_path')->nullable(); // Cover image
            $table->unsignedBigInteger('uploaded_by'); // User ID from ecourtisuserdb
            $table->string('category')->nullable();
            $table->integer('published_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('download_count')->default(0);
            $table->timestamps();

            $table->index('uploaded_by');
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pgsql_app')->dropIfExists('books');
    }
};
