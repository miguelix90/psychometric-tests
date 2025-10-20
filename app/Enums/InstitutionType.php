<?php

namespace App\Enums;

enum InstitutionType: string
{
    case SOCIESCUELA = 'sociescuela';
    case EDUCATIVO = 'educativo';
    case PROFESIONAL = 'profesional';
    case ASOCIACION = 'asociacion';
    case OTHERS = 'others';

    public function label(): string
    {
        return match($this) {
            self::SOCIESCUELA => 'Sociescuela',
            self::EDUCATIVO => 'Centro Educativo',
            self::PROFESIONAL => 'Centro Profesional',
            self::ASOCIACION => 'Asociación',
            self::OTHERS => 'Otros',
        };
    }
}
