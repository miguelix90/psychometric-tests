<?php

namespace App\Models;

use App\Enums\BatteryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Battery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'has_scoring',
        'is_active',
    ];

    protected $casts = [
        'type' => BatteryType::class,
        'has_scoring' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relación muchos a muchos con Task a través de battery_tasks
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'battery_tasks')
            ->withPivot('order')
            ->orderBy('battery_tasks.order');
    }

    /**
     * Scope para obtener solo baterías activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeByType($query, BatteryType $type)
    {
        return $query->where('type', $type);
    }
}
