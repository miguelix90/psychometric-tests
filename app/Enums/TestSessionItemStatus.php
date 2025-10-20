<?php

namespace App\Enums;

enum TestSessionItemStatus: string
{
    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'No Iniciado',
            self::IN_PROGRESS => 'En Progreso',
            self::COMPLETED => 'Completado',
        };
    }
}
