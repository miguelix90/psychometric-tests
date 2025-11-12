<?php

namespace App\Models;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /**
     * Los atributos que NO son asignables masivamente.
     * Como esta tabla solo se modifica mediante seeders/migraciones,
     * protegemos todos los campos.
     */
    protected $guarded = ['id'];

    /**
     * Casts
     */
    protected $casts = [
        'type' => TaskType::class,
    ];

    /**
     * Relación: Una tarea tiene muchos ítems
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get only active items for the task.
     */
    public function activeItems(): HasMany
    {
        return $this->hasMany(Item::class)->where('is_active', true);
    }

    /**
     * Métodos helper para verificar tipo de tarea
     */
    public function isMatrix(): bool
    {
        return $this->type === TaskType::MATRIX;
    }

    public function isSelection(): bool
    {
        return $this->type === TaskType::SELECTION;
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeByType($query, TaskType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para obtener solo tareas de tipo matriz
     */
    public function scopeMatrixType($query)
    {
        return $query->where('type', TaskType::MATRIX);
    }

    /**
     * Scope para obtener solo tareas de tipo selección
     */
    public function scopeSelectionType($query)
    {
        return $query->where('type', TaskType::SELECTION);
    }
}
