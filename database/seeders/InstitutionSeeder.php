<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Institution;
use App\Enums\InstitutionType;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Institución Sociescuela (para administradores)
        Institution::create([
            'name' => 'Sociescuela',
            'type' => InstitutionType::SOCIESCUELA,
            'contact_name' => 'Administrador General',
            'email' => 'admin@sociescuela.com',
            'available_uses' => 999999,
        ]);

        // Centro Educativo de prueba
        Institution::create([
            'name' => 'IES Demo',
            'type' => InstitutionType::EDUCATIVO,
            'contact_name' => 'Director Demo',
            'email' => 'contacto@iesdemo.edu',
            'available_uses' => 100,
        ]);

        // Centro Profesional de prueba
        Institution::create([
            'name' => 'Centro Psicológico Test',
            'type' => InstitutionType::PROFESIONAL,
            'contact_name' => 'Psicólogo Responsable',
            'email' => 'info@centropsicologico.com',
            'available_uses' => 50,
        ]);

        // Asociación de prueba
        Institution::create([
            'name' => 'Asociación de Orientadores',
            'type' => InstitutionType::ASOCIACION,
            'contact_name' => 'Presidente Asociación',
            'email' => 'contacto@asociacionorientadores.org',
            'available_uses' => 75,
        ]);
    }
}
