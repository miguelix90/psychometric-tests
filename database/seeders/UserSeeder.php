<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sociescuela = Institution::where('name', 'Sociescuela')->first();
        $iesDemo = Institution::where('name', 'IES Demo')->first();
        $centroProfesional = Institution::where('name', 'Centro Psicológico Test')->first();

        // ===== ADMINISTRADOR (Sociescuela) =====
        $admin = User::create([
            'name' => 'Administrador General',
            'email' => 'admin@psytest.com',
            'password' => Hash::make('password'),
            'institution_id' => $sociescuela->id,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Administrador');
        $mah = User::create([
            'name' => 'Miguel A. Huete',
            'email' => 'miguelix90@hotmail.com',
            'password' => Hash::make('password'),
            'institution_id' => $sociescuela->id,
            'email_verified_at' => now(),
        ]);
        $mah->assignRole('Administrador');

        // ===== RESPONSABLE (IES Demo) =====
        $responsable = User::create([
            'name' => 'Responsable IES Demo',
            'email' => 'responsable@psytest.com',
            'password' => Hash::make('password'),
            'institution_id' => $iesDemo->id,
            'email_verified_at' => now(),
        ]);
        $responsable->assignRole('Responsable');

        // ===== PROFESORES =====
        // Profesor 1 (IES Demo)
        $profesor1 = User::create([
            'name' => 'Profesor Demo 1',
            'email' => 'profesor1@psytest.com',
            'password' => Hash::make('password'),
            'institution_id' => $iesDemo->id,
            'email_verified_at' => now(),
        ]);
        $profesor1->assignRole('Profesor');

        // Profesor 2 (Centro Profesional)
        $profesor2 = User::create([
            'name' => 'Profesor Demo 2',
            'email' => 'profesor2@psytest.com',
            'password' => Hash::make('password'),
            'institution_id' => $centroProfesional->id,
            'email_verified_at' => now(),
        ]);
        $profesor2->assignRole('Profesor');
    }
}
