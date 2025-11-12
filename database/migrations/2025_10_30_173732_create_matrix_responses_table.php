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
        Schema::create('matrix_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_session_task_id')
                ->constrained('test_session_tasks')
                ->onDelete('cascade');
            $table->foreignId('item_id')
                ->constrained('items')
                ->onDelete('cascade');
            $table->string('participant_answer', 10);
            $table->boolean('is_correct');
            $table->unsignedInteger('response_time_ms');
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index('test_session_task_id');
            $table->index('item_id');
            $table->index('is_correct');
            $table->index('response_time_ms');

            // Prevenir respuestas duplicadas
            $table->unique(['test_session_task_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matrix_responses');
    }
};
