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
        Schema::create('spatial_responses', function (Blueprint $table) {
            $table->id();

            // Relación con test_session_task
            $table->foreignId('test_session_task_id')
                  ->constrained('test_session_tasks')
                  ->onDelete('cascade');

            // Relación con item
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->onDelete('cascade');

            // Respuesta del participante (A, B, C, D, E, F)
            $table->string('participant_answer', 1);

            // Si la respuesta es correcta
            $table->boolean('is_correct')->default(false);

            // Tiempo de respuesta en milisegundos
            $table->unsignedInteger('response_time_ms');

            $table->timestamps();

            // Índices para mejorar búsquedas
            $table->index('test_session_task_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spatial_responses');
    }
};
