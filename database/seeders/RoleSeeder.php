<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir permisos por módulo
        $permissions = [
            // Gestión de usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Gestión de instituciones
            'institutions.view',
            'institutions.create',
            'institutions.edit',
            'institutions.delete',
            'institutions.manage-uses',

            // Gestión de baterías
            'batteries.view',
            'batteries.create',
            'batteries.edit',
            'batteries.delete',

            // Gestión de ítems
            'items.view',
            'items.create',
            'items.edit',
            'items.delete',

            // Gestión de participantes
            'participants.view-own',
            'participants.view-institution',
            'participants.create',
            'participants.edit',
            'participants.delete',

            // Gestión de sesiones de test
            'test-sessions.view-own',
            'test-sessions.view-institution',
            'test-sessions.manage-own',
            'test-sessions.manage-institution',

            // Gestión de resultados
            'results.view-own',
            'results.view-institution',
            'results.export',
        ];

        // Crear permisos
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ===== ROL: ADMINISTRADOR =====
        $adminRole = Role::create(['name' => 'Administrador']);
        $adminRole->givePermissionTo(Permission::all());

        // ===== ROL: RESPONSABLE =====
        $responsableRole = Role::create(['name' => 'Responsable']);
        $responsableRole->givePermissionTo([
            // Baterías
            'batteries.view',

            // Participantes de su institución
            'participants.view-institution',
            'participants.create',
            'participants.edit',
            'participants.delete',

            // Sesiones de su institución
            'test-sessions.view-institution',
            'test-sessions.manage-institution',

            // Resultados de su institución
            'results.view-institution',
            'results.export',
        ]);

        // ===== ROL: PROFESOR =====
        $profesorRole = Role::create(['name' => 'Profesor']);
        $profesorRole->givePermissionTo([
            // Baterías (solo ver)
            'batteries.view',

            // Solo sus participantes
            'participants.view-own',
            'participants.create',
            'participants.edit',

            // Solo sus sesiones
            'test-sessions.view-own',
            'test-sessions.manage-own',

            // Solo sus resultados
            'results.view-own',
            'results.export',
        ]);
    }
}
