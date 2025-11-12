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
        Schema::create('test_session_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // Orden de presentación
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('test_session_id');
            $table->index('task_id');
            $table->index('status');
            $table->unique(['test_session_id', 'task_id']); // Evitar duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_session_tasks');
    }
};
