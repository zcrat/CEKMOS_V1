<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            'ver_ordenes_servicio_modulos',
            'ver_ordenes_servicio_todos',
            'autorizar_presupuestos',
            'aprobar_presupuestos',
            'pagar_presupuestos',
            'terminar_presupuestos',
            'facturar_presupuestos',
            'cambiar_modulo_presupuestos',
            'ver_venta_presupuestos',
            'ver_recepciones_vehiculares',
            'administrar_caja',
            'crear_ordenes_servicio',
            'iniciar_terminar_diagnostico',
            'terminar_mano_obra',
            'revisar_mano_obra',
            'aprobar_denegar_mano_obra',
            'entregar_unidad',
            'reingresar_unidad',
            'retroceder_seguimiento',
            'ver_catalogo_conceptos',
            'crear_catalogo_conceptos',
            'editar_catalogo_conceptos',
            'eliminar_catalogo_conceptos',
            'ver_importacion_conceptos',
            'ver.vales.almacen',
            'ver.vales.subcontratos',
            'crear.vales.almacen',
            'editar.vales.almacen',
            'eliminar.vales.almacen',
        ];

        Permission::query()
            ->whereIn('name', [
                'iniciar_diagnostico_seguimiento',
                'terminar_diagnostico_seguimiento',
                'terminar_mano_obra_seguimiento',
                'aprobar_mano_obra_seguimiento',
                'denegar_mano_obra_seguimiento',
                'iniciar_terminar_seguimiento',
                'revisar_mano_obra_seguimiento',
                'aprobar_denegar_seguimiento',
                'entregar_unidad_seguimiento',
                'reingresar_unidad_seguimiento',
                'retroceder_diagnostico_mano_obra',
            ])
            ->get()
            ->each
            ->delete();

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
