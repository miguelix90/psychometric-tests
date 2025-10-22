<?php

namespace App\Models;

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
}
