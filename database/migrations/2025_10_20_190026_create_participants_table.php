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
            $table->string('code')->unique();
            $table->integer('age_months'); // Edad en meses
            $table->enum('sex', array_column(Sex::cases(), 'value'));
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('restrict');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
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
