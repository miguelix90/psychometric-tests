<?php

namespace App\Enums;

enum BatteryType: string
{
    case SCREENING = 'screening';
    case COMPLETE = 'complete';
    case VALIDATION = 'validation';

    public function label(): string
    {
        return match($this) {
            self::SCREENING => 'Screening',
            self::COMPLETE => 'Completa',
            self::VALIDATION => 'Validación',
        };
    }

    public function hasScoring(): bool
    {
        return match($this) {
            self::SCREENING, self::COMPLETE => true,
            self::VALIDATION => false,
        };
    }
}
