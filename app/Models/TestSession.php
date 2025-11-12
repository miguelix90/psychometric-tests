<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    protected $fillable = [
        'participant_id',
        'battery_id',
        'institution_id',
        'assigned_by_user_id',
        'battery_code_id',
        'status',
        'started_at',
        'completed_at',
        'use_deducted',
    ];

    protected $casts = [
        'status' => SessionStatus::class,
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'use_deducted' => 'boolean',
    ];

    // Relaciones
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function battery()
    {
        return $this->belongsTo(Battery::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function batteryCode()
    {
        return $this->belongsTo(BatteryCode::class);
    }

    public function testSessionTasks()
    {
        return $this->hasMany(TestSessionTask::class);
    }

    // Métodos de estado
    public function isPending(): bool
    {
        return $this->status === SessionStatus::PENDING;
    }

    public function isInProgress(): bool
    {
        return $this->status === SessionStatus::IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === SessionStatus::COMPLETED;
    }

    public function isAbandoned(): bool
    {
        return $this->status === SessionStatus::ABANDONED;
    }

    public function canBeStarted(): bool
    {
        return $this->status === SessionStatus::PENDING;
    }

    // Métodos de acción
    public function start(): void
    {
        $this->update([
            'status' => SessionStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => SessionStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function abandon(): void
    {
        $this->update([
            'status' => SessionStatus::ABANDONED,
        ]);
    }

    public function deductUse(): void
    {
        if (!$this->use_deducted) {
            $this->institution->decrement('available_uses');
            $this->update(['use_deducted' => true]);
        }
    }

    public function refundUse(): void
    {
        if ($this->use_deducted) {
            $this->institution->increment('available_uses');
            $this->update(['use_deducted' => false]);
        }
    }

    public function cancel(): void
    {
        $this->abandon();
        $this->refundUse();
    }

    // Método estático de validación
    public static function hasActiveSessions($participantId, $batteryId): bool
    {
        return self::where('participant_id', $participantId)
            ->where('battery_id', $batteryId)
            ->whereIn('status', [SessionStatus::PENDING, SessionStatus::IN_PROGRESS])
            ->exists();
    }

    // Método para inicializar tareas
    public function initializeTasks(): void
    {
        $tasksWithOrder = $this->battery->tasks()->orderBy('battery_tasks.order')->get();

        foreach ($tasksWithOrder as $task) {
            TestSessionTask::create([
                'test_session_id' => $this->id,
                'task_id' => $task->id,
                'order' => $task->pivot->order,
                'status' => \App\Enums\TestSessionTaskStatus::NOT_STARTED,
            ]);
        }
    }

    // Scopes
    public function scopeForParticipant($query, $participantId)
    {
        return $query->where('participant_id', $participantId);
    }

    public function scopeForBattery($query, $batteryId)
    {
        return $query->where('battery_id', $batteryId);
    }

    public function scopePending($query)
    {
        return $query->where('status', SessionStatus::PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', SessionStatus::IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', SessionStatus::COMPLETED);
    }

    public function scopeAbandoned($query)
    {
        return $query->where('status', SessionStatus::ABANDONED);
    }

    public function scopeForInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeAssignedBy($query, $userId)
    {
        return $query->where('assigned_by_user_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByCode($query, $codeId)
    {
        return $query->where('battery_code_id', $codeId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [SessionStatus::PENDING, SessionStatus::IN_PROGRESS]);
    }
}
