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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->string('code')->unique()->comment('Identificador único del ítem, ej: MAT_001');
            $table->decimal('difficulty', 5, 2)->comment('Nivel de dificultad del ítem');
            $table->json('content')->comment('Contenido del ítem (estructura flexible)');
            $table->string('correct_answer')->comment('Respuesta correcta');
            $table->boolean('is_active')->default(true)->comment('Si está activo se puede usar en evaluaciones');
            $table->timestamps();

            // Índices
            $table->index('task_id');
            $table->index('is_active');
            $table->index(['task_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
