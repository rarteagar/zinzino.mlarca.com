<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Spatie\Permission\PermissionRegistrar;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Menú principal (Filament admin)
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'url' => '/admin',
            'icon' => 'heroicon-o-home',
            'order' => 1,
            'permission' => 'view admin',
            'area' => 'filament',
        ]);

        // Menú de Configuración (Filament admin)
        $settings = Menu::create([
            'name' => 'Configuración',
            'url' => '#',
            'icon' => 'heroicon-o-cog',
            'order' => 100,
            'permission' => 'manage settings',
            'area' => 'filament',
        ]);

        // Submenús de Configuración
        $menus = [
            [
                'name' => 'Usuarios',
                'url' => '/admin/users',
                'icon' => 'heroicon-o-users',
                'order' => 1,
                'permission' => 'manage users',
                'area' => 'filament',
            ],
            [
                'name' => 'Roles',
                'url' => '/admin/roles',
                'icon' => 'heroicon-o-shield-check',
                'order' => 2,
                'permission' => 'manage roles',
                'area' => 'filament',
            ],
            [
                'name' => 'Permisos',
                'url' => '/admin/permissions',
                'icon' => 'heroicon-o-key',
                'order' => 3,
                'permission' => 'manage permissions',
                'area' => 'filament',
            ],
            [
                'name' => 'Menús',
                'url' => '/admin/menus',
                'icon' => 'heroicon-o-view-list',
                'order' => 4,
                'permission' => 'manage menus',
                'area' => 'filament',
            ]
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $settings->id;
            Menu::create($menu);
        }

        // ZinoKit / Pruebas (Web)
        // Web dashboard (site)
        $webDashboard = Menu::create([
            'name' => 'Inicio',
            'url' => '/dashboard',
            'icon' => 'heroicon-o-home',
            'order' => 1,
            'permission' => null,
            'area' => 'web',
        ]);

        $testsParent = Menu::create([
            'name' => 'Pruebas',
            'url' => '#',
            'icon' => 'heroicon-o-beaker',
            'order' => 10,
            'permission' => 'view tests',
            'area' => 'web',
        ]);

        $testOptions = [
            [
                'name' => 'Mis Pruebas',
                'url' => '/tests',
                'icon' => 'heroicon-o-collection',
                'order' => 1,
                'permission' => 'view tests',
                'area' => 'web',
            ],
            [
                'name' => 'Registrar Prueba',
                'url' => '/tests/create',
                'icon' => 'heroicon-o-plus-circle',
                'order' => 2,
                'permission' => 'create tests',
                'area' => 'web',
            ],
            [
                'name' => 'Clientes',
                'url' => '/clients',
                'icon' => 'heroicon-o-user-group',
                'order' => 3,
                'permission' => 'manage clients',
                'area' => 'web',
            ],
            [
                'name' => 'Documentos',
                'url' => '/documents',
                'icon' => 'heroicon-o-document-text',
                'order' => 4,
                'permission' => 'view documents',
                'area' => 'web',
            ],
        ];

        foreach ($testOptions as $opt) {
            $opt['parent_id'] = $testsParent->id;
            Menu::create($opt);
        }

        // Crear permisos y asignarlos al role super-admin
        // Limpiar caché de permisos de spatie antes
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view admin',
            'manage settings',
            'manage users',
            'manage roles',
            'manage permissions',
            'manage menus',
            'view tests',
            'create tests',
            'manage clients',
            'view documents',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $super = Role::firstOrCreate(['name' => 'super-admin']);
        $super->syncPermissions($permissions);
    }
}
