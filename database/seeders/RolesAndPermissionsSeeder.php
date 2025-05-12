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
            'edit_content',
            'view_notifications',  // Nuevo permiso para ver notificaciones
            'create_content',   // Cambiado de 'create content' para consistencia
            'edit_content',      // Cambiado de 'edit content' para consistencia
            'publish_content',  // Cambiado de 'publish content' para consistencia
            'view_users'  // Cambiado de 'view users' para consistencia
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

        // rol user pueden leer notifications
        $user = Role::firstOrCreate(['name' => 'user']);
        $editor->givePermissionTo(['show_content', 'view_notification']); 

       // Sin permisos especiales
        $user = Role::firstOrCreate(['name' => 'invited']);
 


    }
}