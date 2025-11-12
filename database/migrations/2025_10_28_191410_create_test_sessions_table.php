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
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('battery_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('restrict');
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('battery_code_id')->nullable()->constrained('battery_codes')->onDelete('set null');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'abandoned'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('use_deducted')->default(false); // Control de descuento de uso
            $table->timestamps();

            // Índices
            $table->index('participant_id');
            $table->index('battery_id');
            $table->index('institution_id');
            $table->index('status');
            $table->index(['participant_id', 'battery_id', 'status']); // Compuesto para validaciones
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_sessions');
    }
};
