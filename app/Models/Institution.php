<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\InstitutionType;


class Institution extends Model
{
    /** @use HasFactory<\Database\Factories\InstitutionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'contact_name',
        'email',
        'avaliable_uses',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => InstitutionType::class,
        ];
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
