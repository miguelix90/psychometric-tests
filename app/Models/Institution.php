<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\InstitutionType;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'contact_name',
        'email',
        'available_uses', // Corregido el typo de 'avaliable_uses'
        'access_code',
    ];

    protected function casts(): array
    {
        return [
            'type' => InstitutionType::class,
        ];
    }

    /**
     * Boot method para generar access_code automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($institution) {
            if (empty($institution->access_code)) {
                $institution->access_code = self::generateUniqueAccessCode();
            }
        });
    }

    /**
     * Generar código de acceso único de 7 caracteres alfanuméricos
     */
    public static function generateUniqueAccessCode(): string
    {
        do {
            // Generar código de 7 caracteres alfanuméricos en mayúsculas
            $code = strtoupper(Str::random(7));
            // Asegurar que sea alfanumérico (sin caracteres especiales)
            $code = preg_replace('/[^A-Z0-9]/', '', $code);

            // Si quedó con menos de 7 caracteres, completar
            while (strlen($code) < 7) {
                $code .= strtoupper(Str::random(1));
                $code = preg_replace('/[^A-Z0-9]/', '', $code);
            }

            $code = substr($code, 0, 7);
        } while (self::where('access_code', $code)->exists());

        return $code;
    }

    /**
     * Relación con usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación con participantes
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
