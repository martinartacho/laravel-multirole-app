<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'manage_config',
            'manage_all_users',
            'manage_limited_users',
            'send_notifications',
            'publish_content',
            'edit_content'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $gestor = Role::firstOrCreate(['name' => 'gestor']);
        $gestor->givePermissionTo(['manage_limited_users', 'send_notifications']);

        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->givePermissionTo(['publish_content', 'edit_content']);

        $user = Role::firstOrCreate(['name' => 'user']);
        // Sin permisos especiales
    }
}