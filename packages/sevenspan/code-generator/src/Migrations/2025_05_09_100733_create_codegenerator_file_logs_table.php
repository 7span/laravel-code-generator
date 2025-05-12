<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('codegenerator_file_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file_type');
            $table->string('file_path');
            $table->enum('status', ['success', 'error']);
            $table->text('message')->nullable();
            $table->boolean('is_overwrite')->default(false);
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codegenerator_file_logs');
    }
};
