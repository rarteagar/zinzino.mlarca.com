<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view admin',
            'manage users',
            'manage roles',
            'manage permissions',
            'manage menus',
            'manage settings',
            // Test related
            'view tests',
            'create tests',
            'edit tests',
            'delete tests',
            'export tests',
            'compare tests',
            // Clients & documents
            'manage clients',
            'view documents',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Ensure super-admin role exists and receives all these permissions
        $super = Role::firstOrCreate(['name' => 'super-admin']);
        $super->syncPermissions($permissions);
    }
}
