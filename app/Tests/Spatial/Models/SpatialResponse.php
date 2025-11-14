<?php

namespace App\Tests\Spatial\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TestSessionTask;
use App\Models\Item;

class SpatialResponse extends Model
{
    use HasFactory;

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
     * Relación con TestSessionTask
     */
    public function testSessionTask()
    {
        return $this->belongsTo(TestSessionTask::class);
    }

    /**
     * Relación con Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Scope para filtrar por sesión
     */
    public function scopeForSession($query, $testSessionTaskId)
    {
        return $query->where('test_session_task_id', $testSessionTaskId);
    }

    /**
     * Scope para filtrar respuestas correctas
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope para filtrar respuestas incorrectas
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }
}
