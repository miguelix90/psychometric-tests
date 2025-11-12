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
        Schema::create('battery_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 7)->unique(); // Código único de 6-7 caracteres
            $table->foreignId('battery_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->integer('max_uses')->default(1); // Límite de usos (configurable)
            $table->integer('current_uses')->default(0); // Usos actuales
            $table->boolean('is_active')->default(true); // Activo/Inactivo
            $table->timestamp('expires_at'); // Caduca a las 8 horas
            $table->timestamps();

            // Índices
            $table->index('code');
            $table->index('battery_id');
            $table->index('institution_id');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_codes');
    }
};
