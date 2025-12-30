<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Crear roles y permisos básicos
        $this->createRolesAndPermissions();

        // Crear menús
        $this->call(MenuSeeder::class);

        // Crear usuario administrador
        $this->call(AdminUserSeeder::class);
    }

    protected function createRolesAndPermissions()
    {
        // Crear roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Crear permisos
        $permissions = [
            'view admin',
            'manage users',
            'manage roles',
            'manage permissions',
            'manage menus',
            'manage settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar todos los permisos al rol super-admin
        $superAdminRole->syncPermissions(Permission::all());
    }
}
