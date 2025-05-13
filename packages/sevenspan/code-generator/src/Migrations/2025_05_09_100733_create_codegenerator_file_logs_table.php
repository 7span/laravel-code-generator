<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This method creates the `codegenerator_file_logs` table, which is used to log
     * details about file generation operations performed by the code generator.
     */
    public function up(): void
    {
        Schema::create('codegenerator_file_logs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('file_type'); // Type of the file (e.g., Controller, Model, etc.)
            $table->string('file_path'); // Path where the file is generated
            $table->string('status'); // Status of the file generation (e.g., success, error)
            $table->text('message')->nullable(); // Optional message or description
            $table->boolean('is_overwrite')->default(false); // Indicates if the file was overwritten
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * This method drops the `codegenerator_file_logs` table if it exists.
     */
    public function down(): void
    {
        Schema::dropIfExists('codegenerator_file_logs');
    }
};
