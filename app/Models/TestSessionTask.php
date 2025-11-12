<?php

namespace App\Models;

use App\Enums\TestSessionTaskStatus;
use Illuminate\Database\Eloquent\Model;

class TestSessionTask extends Model
{
    protected $fillable = [
        'test_session_id',
        'task_id',
        'order',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => TestSessionTaskStatus::class,
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relaciones
    public function testSession()
    {
        return $this->belongsTo(TestSession::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Métodos de estado
    public function isNotStarted(): bool
    {
        return $this->status === TestSessionTaskStatus::NOT_STARTED;
    }

    public function isInProgress(): bool
    {
        return $this->status === TestSessionTaskStatus::IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === TestSessionTaskStatus::COMPLETED;
    }

    // Métodos de acción
    public function start(): void
    {
        $this->update([
            'status' => TestSessionTaskStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => TestSessionTaskStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Relaciones polimórficas a respuestas específicas
    public function matrixResponses()
    {
        return $this->hasMany(\App\Tests\Matrix\Models\MatrixResponse::class);
    }

    /*
    public function selectionResponses()
    {
        return $this->hasMany(\App\Tests\Selection\Models\SelectionResponse::class);
    }
        */

    /**
     * MÉTODO CLAVE: Obtener la URL para ejecutar esta tarea según su tipo
     */
    public function getExecutionUrl(): string
    {
        return match($this->task->type) {
            \App\Enums\TaskType::MATRIX => route('test.matrix.task', [
                'testSession' => $this->test_session_id,
                'testSessionTask' => $this->id
            ]),
            // \App\Enums\TaskType::SELECTION => route('test.selection.task', [
            //     'testSession' => $this->test_session_id,
            //     'testSessionTask' => $this->id
            // ]),
            default => throw new \Exception("Tipo de tarea no implementado: {$this->task->type->value}")
        };
    }

    /**
     * Obtener el modelo de respuesta correspondiente según el tipo
     */
    public function getResponseModel(): string
    {
        return match($this->task->type) {
            \App\Enums\TaskType::MATRIX => \App\Tests\Matrix\Models\MatrixResponse::class,
            //\App\Enums\TaskType::SELECTION => \App\Tests\Selection\Models\SelectionResponse::class,
            default => throw new \Exception("Tipo de tarea no implementado: {$this->task->type->value}")
        };
    }

    /**
     * Contar respuestas para esta tarea (independiente del tipo)
     */
    public function getResponsesCount(): int
    {
        return match($this->task->type) {
            \App\Enums\TaskType::MATRIX => $this->matrixResponses()->count(),
            //\App\Enums\TaskType::SELECTION => $this->selectionResponses()->count(),
            default => 0
        };
    }
}
