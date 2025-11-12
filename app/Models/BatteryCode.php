<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BatteryCode extends Model
{
    protected $fillable = [
        'code',
        'battery_id',
        'created_by_user_id',
        'institution_id',
        'max_uses',
        'current_uses',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function battery()
    {
        return $this->belongsTo(Battery::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function testSessions()
    {
        return $this->hasMany(TestSession::class);
    }

    // Métodos
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(7));
            $code = preg_replace('/[^A-Z0-9]/', '', $code);
            while (strlen($code) < 7) {
                $code .= strtoupper(Str::random(1));
                $code = preg_replace('/[^A-Z0-9]/', '', $code);
            }
            $code = substr($code, 0, 7);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->current_uses < $this->max_uses
            && $this->expires_at->isFuture();
    }

    public function hasUsesAvailable(): bool
    {
        return $this->current_uses < $this->max_uses;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function incrementUses(): void
    {
        $this->increment('current_uses');
    }

    public function decrementUses(): void
    {
        if ($this->current_uses > 0) {
            $this->decrement('current_uses');
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeValid($query)
    {
        return $query->active()->notExpired()->whereRaw('current_uses < max_uses');
    }
}
