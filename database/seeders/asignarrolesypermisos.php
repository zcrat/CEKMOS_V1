<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
class asignarrolesypermisos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Crear permisos
        $permissions = [
            'Administrar_Roles_Permisos_User',
            'Administrar_Permisos_Roles',
            'Ver_Presupuestos',
            'Ver_Presupuestos_Admin',
            'Ver_Ordenes_Servicio',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $roles = [
            'Super Admin',
            'Administrador 1',
        ];

        $rolePermissions = [
            'Super Admin' => $permissions,
            'Administrador 1' => $permissions,
        ];

        foreach ($roles as $roleName) {
            $role = Role::create(['name' => $roleName]);

            if (isset($rolePermissions[$roleName])) {
                $role->givePermissionTo($rolePermissions[$roleName]);
            }
        }
    }
}
