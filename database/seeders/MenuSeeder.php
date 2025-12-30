<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // Menú principal
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'url' => '/admin',
            'icon' => 'heroicon-o-home',
            'order' => 1,
            'permission' => 'view admin'
        ]);

        // Menú de Configuración
        $settings = Menu::create([
            'name' => 'Configuración',
            'url' => '#',
            'icon' => 'heroicon-o-cog',
            'order' => 100,
            'permission' => 'manage settings'
        ]);

        // Submenús de Configuración
        $menus = [
            [
                'name' => 'Usuarios',
                'url' => '/admin/users',
                'icon' => 'heroicon-o-users',
                'order' => 1,
                'permission' => 'manage users'
            ],
            [
                'name' => 'Roles',
                'url' => '/admin/roles',
                'icon' => 'heroicon-o-shield-check',
                'order' => 2,
                'permission' => 'manage roles'
            ],
            [
                'name' => 'Permisos',
                'url' => '/admin/permissions',
                'icon' => 'heroicon-o-key',
                'order' => 3,
                'permission' => 'manage permissions'
            ],
            [
                'name' => 'Menús',
                'url' => '/admin/menus',
                'icon' => 'heroicon-o-view-list',
                'order' => 4,
                'permission' => 'manage menus'
            ]
        ];

        foreach ($menus as $menu) {
            $menu['parent_id'] = $settings->id;
            Menu::create($menu);
        }
    }
}
