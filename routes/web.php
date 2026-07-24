<?php

use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ComboboxController;
use App\Http\Controllers\ConceptosPresupuestosController;
use App\Http\Controllers\CortanaController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\InspeccionVehicularController;
use App\Http\Controllers\MigrateDataBaseOld;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\presupuestosController;
use App\Http\Controllers\RecepcionVehicularController;
use App\Http\Controllers\select2controller;
use App\Http\Controllers\selectcontroller;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/userid', function (Request $request) {
    return $request->user()->id;
})->name('userid');
Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {});
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/catalogos/conceptos', function () {
        return Inertia::render('Catalogos/Conceptos');
    })->name('catalogos.conceptos');
    Route::get('/catalogos/conceptos-contratos', function () {
        return Inertia::render('Catalogos/ConceptosContratos');
    })->name('catalogos.conceptos-contratos');
    Route::get('/catalogos/conceptos/read', [ConceptosPresupuestosController::class, 'read'])
        ->name('catalogos.conceptos.read');
    Route::get('/catalogos/conceptos/catalogos', [ConceptosPresupuestosController::class, 'catalogos'])
        ->name('catalogos.conceptos.catalogos');
    Route::get('/catalogos/conceptos/opciones/categorias-sat', [ConceptosPresupuestosController::class, 'categoriasSatOptions'])
        ->name('catalogos.conceptos.opciones.categorias-sat');
    Route::get('/catalogos/conceptos/opciones/unidades-sat', [ConceptosPresupuestosController::class, 'unidadesSatOptions'])
        ->name('catalogos.conceptos.opciones.unidades-sat');
    Route::get('/catalogos/conceptos/opciones/vehiculos', [ConceptosPresupuestosController::class, 'vehiculosOptions'])
        ->name('catalogos.conceptos.opciones.vehiculos');
    Route::get('/catalogos/conceptos/opciones/categorias', [ConceptosPresupuestosController::class, 'categoriasOptions'])
        ->name('catalogos.conceptos.opciones.categorias');
    Route::get('/catalogos/conceptos/plantilla', [ConceptosPresupuestosController::class, 'plantilla'])
        ->name('catalogos.conceptos.plantilla');
    Route::post('/catalogos/conceptos/importar', [ConceptosPresupuestosController::class, 'encolarImportacion'])
        ->name('catalogos.conceptos.importar');
    Route::get('/catalogos/conceptos/importaciones', [ConceptosPresupuestosController::class, 'importaciones'])
        ->name('catalogos.conceptos.importaciones');
    Route::get('/catalogos/conceptos/importaciones/{archivoSistema}/progreso', [ConceptosPresupuestosController::class, 'progresoImportacion'])
        ->name('catalogos.conceptos.importaciones.progreso');
    Route::get('/catalogos/conceptos/importaciones/{archivoSistema}/resultado', [ConceptosPresupuestosController::class, 'descargarResultado'])
        ->name('catalogos.conceptos.importaciones.resultado');
    Route::delete('/catalogos/conceptos/importaciones/{archivoSistema}', [ConceptosPresupuestosController::class, 'destroyImportacion'])
        ->name('catalogos.conceptos.importaciones.destroy');
    Route::post('/catalogos/conceptos', [ConceptosPresupuestosController::class, 'store'])
        ->name('catalogos.conceptos.store');
    Route::get('/catalogos/conceptos/{costo}', [ConceptosPresupuestosController::class, 'show'])
        ->name('catalogos.conceptos.show');
    Route::put('/catalogos/conceptos/{costo}', [ConceptosPresupuestosController::class, 'update'])
        ->name('catalogos.conceptos.update');
    Route::delete('/catalogos/conceptos/{costo}', [ConceptosPresupuestosController::class, 'destroy'])
        ->name('catalogos.conceptos.destroy');

    Route::middleware(['permission:ver_usuarios_sitema'])->group(function () {
        Route::get('/users', function () {
            return Inertia::render('users');
        })->name('users');
        Route::get('/get/users', [UsersController::class, 'ReadUsers'])->name('getusers');
        Route::post('/toggle/user', [UsersController::class, 'ToggleActive'])->name('toggle.user');
        Route::get('/get/user', [UsersController::class, 'ReadUser'])->name('user.read');
        Route::post('/create/user', [UsersController::class, 'CreateUser'])->name('user.create');
        Route::post('/update/user', [UsersController::class, 'UpdateUser'])->name('user.update');
    });
    Route::delete('imagenes/delete', [ArchivosController::class, 'Delete'])->name('cortana.imagenes.delete');

    Route::middleware(['permission:ver_presupuestos'])->group(function () {
        Route::get('cortana/presupuestos', [CortanaController::class, 'PresupuestosVista'])->name('cortana.presupuesto.vista');
        Route::get('cortana/get/presusupuestos', [CortanaController::class, 'GetItems'])->name('cortana.presupuesto.items');
        Route::get('cortana/get/ordenes-servicio', [CortanaController::class, 'GetOrdenesServicio'])->name('cortana.ordenservicio.items');
        Route::get('presupuesto/get/datos/orden', [PresupuestosController::class, 'GetDataPerOrdenServicio'])->name('presupuesto.get.data_orden');
        Route::post('presupuesto/create', [PresupuestosController::class, 'CreatePresupuesto'])->name('presupuesto.create');
    });

    Route::middleware(['permission:ver_recepciones_vehiculares'])->group(function () {
        Route::get('/recepciones/vehiculares', [RecepcionVehicularController::class, 'view'])->name('recepcionesvehiculares.vista');
        Route::get('/recepciones/vehiculares/read', [RecepcionVehicularController::class, 'Read'])->name('recepcionesvehiculares.read');
        Route::get('/recepciones/vehiculares/read/one', [RecepcionVehicularController::class, 'ReadOne'])->name('recepcionvehicular.read');
        Route::get('/pdf/recepciones/vehiculares/{id}', [PdfController::class, 'RecepcionVehicular'])->name('pdf.cortana.recepcionvehicular');
        Route::get('/pdf/inspeccion/vehicular/{id}', [PdfController::class, 'InspeccionVehicular'])->name('pdf.cortana.inspeccion.vehicular');
        Route::get('/inspeccion/vehicular/{ordenServicio}', [InspeccionVehicularController::class, 'read'])->name('inspeccionvehicular.read');
        Route::patch('/recepciones/vehiculares/toggle/files_upload', [RecepcionVehicularController::class, 'ToggleFilesRecepcionVehicular'])->name('recepcionvehicular.toggle.upload.files');

    });
    Route::middleware(['permission:recepciones_vehiculares_crear'])->group(function () {
        Route::post('/recepciones/vehiculares/update', [RecepcionVehicularController::class, 'Update'])->name('recepcionesvehiculares.update');
        Route::post('/recepciones/vehiculares/create', [RecepcionVehicularController::class, 'Create'])->name('recepcionesvehiculares.create');
        Route::post('/inspeccion/vehicular/save', [InspeccionVehicularController::class, 'save'])->name('inspeccionvehicular.save');
    });

    Route::get('/get/permisos/user', [UsersController::class, 'GetPermisos'])->name('getpermisosuser');
    Route::get('/get/modulos/user', [UsersController::class, 'GetModulos'])->name('get.modulos.user');

    Route::post('/toggle/modulos/user', [UsersController::class, 'ToggleModulo'])->name('toggle.modulo');
    Route::post('/toggle/roles/user', [UsersController::class, 'ToggleRole'])->name('toggle.role');
    Route::post('/toggle/permisos/user', [UsersController::class, 'TogglePermiso'])->name('toggle.permiso');
    Route::get('/user/notifications', [UsersController::class, 'GetNotificaciones'])->name('getnotifications');
    Route::get('/user/read/notifications', [UsersController::class, 'ReadNotification'])->name('readnotification');
    Route::get('/employees', [EmpleadosController::class, 'View'])->name('employees');
    Route::get('/employees/read', [EmpleadosController::class, 'read'])->name('employees.read');
    Route::post('/employees/create', [EmpleadosController::class, 'create'])->name('employees.create');

    Route::get('select2/empresas', [select2controller::class, 'Empresas'])->name('select2.empresas');
    Route::get('select2/clientes', [select2controller::class, 'Clientes'])->name('select2.clientes');
    Route::get('select2/economicos', [select2controller::class, 'Economicos'])->name('select2.economico');
    Route::get('select2/vehiculos/conceptos/disponibles', [select2controller::class, 'VehiculosConceptosPorModulo'])->name('select2.vehiculos.conceptos.modulos');
    Route::get('select2/regimenes/fiscales', [select2controller::class, 'RegimenesFiscales'])->name('select2.regimenes.fiscales');
    Route::get('select2/catalogo/marcas', [select2controller::class, 'MarcasCatalogo'])->name('select2.catalogo.marcas');
    Route::get('select2/catalogo/motores', [select2controller::class, 'MotoresCatalogo'])->name('select2.catalogo.motores');
    Route::get('select2/catalogo/modelos', [select2controller::class, 'ModelosCatalogo'])->name('select2.catalogo.modelos');

    Route::get('select/niveles/combustible', [selectcontroller::class, 'NivelesCombustible'])->name('select.niveles.combustible');
    Route::get('select/modulos/orden', [selectcontroller::class, 'ModulosOrden'])->name('select.modulos.disponibles.usuario');
    Route::get('select/estatus', [selectcontroller::class, 'EstatusIdsPerCategory'])->name('select.status');
    Route::get('select/tipos/vehiculos', [selectcontroller::class, 'TiposVehiculosGeneral'])->name('select.tipos.vehiculos');

    Route::get('combobox/ordenesservicio', [ComboboxController::class, 'GetOrdenesServicio'])->name('combobox.ordenes_servicio');
    Route::get('combobox/ubicacion', [ComboboxController::class, 'GetUbicaciones'])->name('combobox.ubicaciones');
    Route::get('combobox/administradorestrasporte', [ComboboxController::class, 'GetAdministradoresTrasporte'])->name('combobox.administradores_trasporte');
    Route::get('combobox/jefesproceso', [ComboboxController::class, 'GetJefesProceso'])->name('combobox.jefes_procesos');
    Route::get('combobox/trabajadores', [ComboboxController::class, 'GetTrabajadores'])->name('combobox.trabajadores');
    Route::get('combobox/tecnicos', [ComboboxController::class, 'GetTecnicos'])->name('combobox.tecnicos');
    Route::get('combobox/vehiculo/economico', [ComboboxController::class, 'GetVehiculoEconomico'])->name('combobox.vehiculo.economico');
    Route::get('combobox/vehiculo/placas', [ComboboxController::class, 'GetVehiculoPlacas'])->name('combobox.vehiculo.placas');
    Route::get('combobox/motores', [ComboboxController::class, 'GetMotores'])->name('combobox.motores');
    Route::get('combobox/ubicaciones', [ComboboxController::class, 'GetUbicaciones'])->name('combobox.ubicaciones.lista');

    Route::get('vehiculo/get/datos', [VehiculoController::class, 'GetDatos'])->name('vehiculo.get.datos');
    Route::get('vehiculo/get/image', [VehiculoController::class, 'GetImage'])->name('vehiculo.get.image');
    Route::get('vehiculo/find/datos', [VehiculoController::class, 'FindDatos'])->name('vehiculo.find');
    Route::post('vehiculo/create/update', [VehiculoController::class, 'CreateOrUpdate'])->name('vehiculo.createorupdate');
    Route::post('vehiculo/catalogo/create', [VehiculoController::class, 'CreateCatalog'])->name('vehiculo.catalogo.create');
    Route::get('admin/caja', [CajaController::class, 'View'])->name('admin.caja');
    Route::get('admin/caja/read', [CajaController::class, 'Read'])->name('admin.caja.read');
});

Route::get('migrar/caja', [MigrateDataBaseOld::class, 'migrateCaja']);

use Spatie\LaravelPdf\Facades\Pdf;

Route::get('/test-browser', function () {

    try {

        Pdf::html('<h1>Hello world</h1>')
            ->save(public_path('my-a3-pdf.pdf'));

        return 'funciona';

    } catch (\Throwable $e) {

        return response()->json([
            'message' => mb_convert_encoding(
                $e->getMessage(),
                'UTF-8',
                'UTF-8'
            ),
        ]);

    }

});

// Ruta temporal para previsualizar directamente la Blade del PDF.
Route::get('/pdf/recepcion-demo', function () {
    return app(PdfController::class)->RecepcionVehicular(11, true);
})->name('pdf.recepcion.demo');
