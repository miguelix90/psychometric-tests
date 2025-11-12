<?php

namespace App\Enums;

enum SessionStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ABANDONED = 'abandoned';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::IN_PROGRESS => 'En Progreso',
            self::COMPLETED => 'Completada',
            self::ABANDONED => 'Abandonada',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'blue',
            self::COMPLETED => 'green',
            self::ABANDONED => 'red',
        };
    }
}
