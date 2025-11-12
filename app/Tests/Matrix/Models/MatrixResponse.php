<?php

namespace App\Tests\Matrix\Models;

use App\Models\Item;
use App\Models\TestSessionTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrixResponse extends Model
{
    protected $fillable = [
        'test_session_task_id',
        'item_id',
        'participant_answer',
        'is_correct',
        'response_time_ms',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'response_time_ms' => 'integer',
    ];

    /**
     * Relación: Una respuesta pertenece a una tarea de sesión
     */
    public function testSessionTask(): BelongsTo
    {
        return $this->belongsTo(TestSessionTask::class);
    }

    /**
     * Relación: Una respuesta pertenece a un item
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Obtener el tiempo de respuesta en segundos
     */
    public function getResponseTimeInSeconds(): float
    {
        return $this->response_time_ms / 1000;
    }

    /**
     * Calcular si la respuesta es correcta
     */
    public function calculateCorrectness(): bool
    {
        return $this->participant_answer === $this->item->correct_answer;
    }

    /**
     * Scope: Solo respuestas correctas
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope: Solo respuestas incorrectas
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Scope: Respuestas rápidas (menos de X ms)
     */
    public function scopeFast($query, $maxMs = 3000)
    {
        return $query->where('response_time_ms', '<=', $maxMs);
    }

    /**
     * Scope: Respuestas lentas (más de X ms)
     */
    public function scopeSlow($query, $minMs = 10000)
    {
        return $query->where('response_time_ms', '>=', $minMs);
    }
}
