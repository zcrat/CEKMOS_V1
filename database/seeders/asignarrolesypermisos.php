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
            'administrar_roles_permisos_user',
            'administrar_permisos_roles',
            'ver_usuarios_sitema',
            'ver_empleados',
            'ver_presupuestos',
            'ver_ordenes_servicio',
            'administrar_caja',
            'crear_ordenes_servicio'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Super Admin',
            'Administrador 1',
        ];

        $rolePermissions = [
            'Super Admin' => [],
            'Administrador 1' => $permissions,
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if (isset($rolePermissions[$roleName])) {
                $role->givePermissionTo($rolePermissions[$roleName]);
            }
        }
    }
}
