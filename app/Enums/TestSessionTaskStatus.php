<?php

namespace App\Enums;

enum TestSessionTaskStatus: string
{
    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'No Iniciada',
            self::IN_PROGRESS => 'En Progreso',
            self::COMPLETED => 'Completada',
        };
    }
}
