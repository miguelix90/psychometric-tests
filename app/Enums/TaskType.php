<?php

namespace App\Enums;

enum TaskType: string
{
    case MATRIX = 'matrix';
    case SELECTION = 'selection';
    case SPATIAL = 'spatial';
    // Futuros tipos: MEMORY, NUMERIC, etc.

    public function label(): string
    {
        return match($this) {
            self::MATRIX => 'Matriz de opciones',
            self::SELECTION => 'Selección múltiple',
            self::SPATIAL => 'Visoespacial',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::MATRIX => 'Presentación de matriz con opciones de respuesta. 1 matriz de ejemplo, hasta 6 items diana',
            self::SELECTION => 'Tabla de ítems donde seleccionar los que coinciden con el ítem diana.',
            self::SPATIAL => 'Tarea que evalúa la capacidad de percibir, analizar y manipular mentalmente objetos en el espacio.',
        };
    }
}
