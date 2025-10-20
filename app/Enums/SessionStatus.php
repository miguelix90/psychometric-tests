<?php

namespace App\Enums;

enum SessionStatus: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ABANDONED = 'abandoned';

    public function label(): string
    {
        return match($this) {
            self::IN_PROGRESS => 'En Progreso',
            self::COMPLETED => 'Completada',
            self::ABANDONED => 'Abandonada',
        };
    }
}
