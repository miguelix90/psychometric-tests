<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Sex;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('age_months'); // Edad en meses (calculado)
            $table->string('sex', 1); // M, F, O
            $table->string('iug', 64)->unique(); // Identificador Único Global (hash)
            $table->string('iuc', 64)->unique(); // Identificador Único Centro (hash)
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Índices para optimizar búsquedas
            $table->index('institution_id');
            $table->index('created_by_user_id');
            $table->index('iug');
            $table->index('iuc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
