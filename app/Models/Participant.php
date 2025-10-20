<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Sex;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Participant extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'age_months',
        'sex',
        'institution_id',
        'created_by_user_id'
    ];

    protected function casts(): array
    {
        return [
            'sex' => Sex::class,
        ];
    }

    /**
     * Generar código único para el participante
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Calcular edad en meses a partir de fecha de nacimiento
     * (útil para cuando se registra el participante)
     */
    public static function calculateAgeInMonths(string $birthDate): int
    {
        return Carbon::parse($birthDate)->diffInMonths(Carbon::now());
    }

    /**
     * Obtener edad en años y meses (para mostrar)
     */
    public function getAgeInYearsAndMonths(): array
    {
        $years = floor($this->age_months / 12);
        $months = $this->age_months % 12;

        return [
            'years' => $years,
            'months' => $months,
        ];
    }

    /**
     * Obtener edad formateada como string
     */
    public function getFormattedAge(): string
    {
        $age = $this->getAgeInYearsAndMonths();
        return "{$age['years']} años y {$age['months']} meses";
    }

    /**
     * Relación con institución
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Relación con el usuario que lo creó
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Relación con sesiones de test
     */
    public function testSessions()
    {
        return $this->hasMany(TestSession::class);
    }
}
