<?php

namespace App\Enums;

enum Sex: string
{
    case MALE = 'M';
    case FEMALE = 'F';
    case OTHER = 'O';

    public function label(): string
    {
        return match($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Femenino',
            self::OTHER => 'Otro',
        };
    }
}
