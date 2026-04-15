<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chofer_role = Role::create(['name' => 'chofer']);
        $cajero_role = Role::create(['name'=> 'cajero']);
        $gerente_role = Role::create(['name'=> 'gerente']);
        $admin_role = Role::create(['name'=> 'admin']);

        $permissions = [
            // Urbans
            'ver urbans',
            'crear urbans',
            'editar urbans',
            'eliminar urbans',

            // Socios
            'ver socios',
            'crear socios',
            'editar socios',
            'eliminar socios',

            // Rutas
            'ver rutas',
            'crear rutas',
            'editar rutas',
            'eliminar rutas',

            // Corridas
            'ver corridas',
            'crear corridas',
            'editar corridas',
            'eliminar corridas',
            'asignar choferes a corridas',
            'quitar choferes de corridas',
            'agendar corridas',
            'cancelar corridas',
            'cerrar corridas',
            'reabrir corridas',
            'registrar salida de corrida',
            'registrar llegada de corrida',
            'ver pasajeros en una corrida',

            // Sucursales
            'ver sucursales',
            'crear sucursales',
            'editar sucursales',
            'eliminar sucursales',

            // Usuarios
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'restablecer contraseñas',
            'bloquear usuarios',
            'desbloquear usuarios',
            'impersonar usuarios',

            // Roles y permisos
            'ver roles',
            'crear roles',
            'editar roles',
            'eliminar roles',
            'asignar permisos',

            // Cajas
            'ver cajas',
            'crear cajas',
            'editar cajas',
            'eliminar cajas',
            'abrir cajas',
            'cerrar cajas',
            'sacar dinero de cajas',
            'meter dinero a cajas',
            'autorizar retiros de caja',
            'autorizar ajustes de caja',
            'ver historial de movimientos de caja',
            'anular movimientos de caja',
            'descargar cortes de caja',

            // Ventas / Boletos
            'ver ventas',
            'crear ventas',
            'editar ventas',
            'eliminar ventas',
            'vender boletos',
            'ver boletos',
            'crear boletos',
            'editar boletos',
            'eliminar boletos',
            'reimprimir boletos',
            'cancelar boletos',
            'reembolsar boletos',
            'aplicar descuentos',
            'autorizar descuentos',

            // Reportes
            'ver reportes',
            'exportar reportes',

            // Sistema
            'ver auditoria',
            'gestionar configuracion',
            'ver dashboard',
            'gestionar respaldos',
            'importar datos',
            'exportar datos',

            // Alcance (multi-sucursal)
            'ver datos sucursal propia',
            'ver datos todas las sucursales',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $chofer_role->syncPermissions([
            'ver corridas',
            'registrar salida de corrida',
            'registrar llegada de corrida',
            'ver datos sucursal propia',
            'ver pasajeros en una corrida',
        ]);

        $cajero_role->syncPermissions([
            'ver cajas',
            'meter dinero a cajas',
            'sacar dinero de cajas',
            'vender boletos',
            'ver boletos',
            'crear boletos',
            'reimprimir boletos',
            'ver ventas',
            'crear ventas',
            'ver datos sucursal propia',
        ]);

        $gerente_role->syncPermissions([
            'ver urbans', 'crear urbans', 'editar urbans',
            'ver socios', 'crear socios', 'editar socios',
            'ver rutas', 'crear rutas', 'editar rutas',
            'ver corridas', 'crear corridas', 'editar corridas', 'agendar corridas', 'cancelar corridas',
            'asignar choferes a corridas', 'quitar choferes de corridas',
            'ver sucursales', 'crear sucursales', 'editar sucursales',
            'ver usuarios', 'crear usuarios', 'editar usuarios',
            'ver cajas', 'crear cajas', 'editar cajas', 'abrir cajas', 'cerrar cajas',
            'sacar dinero de cajas', 'meter dinero a cajas',
            'ver ventas', 'crear ventas', 'editar ventas',
            'vender boletos', 'ver boletos', 'crear boletos', 'editar boletos',
        ]);

        $admin_role->syncPermissions(Permission::all());
    }
}
