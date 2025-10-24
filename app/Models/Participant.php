<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Sex;
use Carbon\Carbon;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'age_months',
        'sex',
        'iug',
        'iuc',
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
     * Generar IUG (Identificador Único Global)
     * Formato: [3_letras_apellido][mes_2_digitos][3_letras_nombre][dia_2_digitos]
     * Ejemplo: García, Juan, 15/03/2010 → GAR03JUA15 → hash
     */
    public static function generateIUG(string $firstName, string $lastName, string $birthDate): string
    {
        $date = Carbon::parse($birthDate);

        // Extraer componentes
        $lastNamePart = strtoupper(substr($lastName, 0, 3));
        $month = $date->format('m');
        $firstNamePart = strtoupper(substr($firstName, 0, 3));
        $day = $date->format('d');

        // Construir el código base
        $baseCode = $lastNamePart . $month . $firstNamePart . $day;

        // Codificar con hash SHA-256 (no reversible)
        return hash('sha256', $baseCode);
    }

    /**
     * Generar IUC (Identificador Único Centro)
     * Formato: [codigo_centro][3_letras_apellido][mes][3_letras_nombre][dia]
     * Ejemplo: AGT3327GAR03JUA15 → hash
     */
    public static function generateIUC(string $accessCode, string $firstName, string $lastName, string $birthDate): string
    {
        $date = Carbon::parse($birthDate);

        // Extraer componentes
        $lastNamePart = strtoupper(substr($lastName, 0, 3));
        $month = $date->format('m');
        $firstNamePart = strtoupper(substr($firstName, 0, 3));
        $day = $date->format('d');

        // Construir el código base con access_code al principio
        $baseCode = $accessCode . $lastNamePart . $month . $firstNamePart . $day;

        // Codificar con hash SHA-256 (no reversible)
        return hash('sha256', $baseCode);
    }

    /**
     * Calcular edad en meses a partir de fecha de nacimiento
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
     * Relación con sesiones de test (comentado hasta implementar)
     */
    // public function testSessions()
    // {
    //     return $this->hasMany(TestSession::class);
    // }
}
