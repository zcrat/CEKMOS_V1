<?php

use App\Http\Controllers\CajaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CortanaController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\select2controller;
use App\Http\Controllers\selectcontroller;
use App\Http\Controllers\ComboboxController;
use App\Http\Controllers\MigrateDataBaseOld;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\presupuestosController;
use App\Http\Controllers\PruebasController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
Route::get('/', function () {
  return redirect('/login');
});

Route::get('/userid', function (Request $request) {
    return $request->user()->id;
  })->name('userid');
Route::middleware(['auth:sanctum',config('jetstream.auth_session')])->group(function () {
  
});
Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    
  Route::get('/Image/Tipo/Vehiculo/General/{type}', function ($type) {
      if(!in_array($type,[8,9,10,11,12,13])){
        return response()->json(['message'=>'Tipo Invalido'],404);
      }
      $realtype=$type-7;
      $path='VehiculosRecepcionVehicular/Vehiculo'.$realtype.'.png';
      $file = Storage::disk('local')->path($path);
      if (!file_exists($file)) {
        return response()->json(['message'=>'No existe la Imagen Del Tipo '.$realtype],404);
      }
      return Response::file($file, [
          'Content-Type' => mime_content_type($file)
      ]);
  })->where('path', '.*')->name('image.tipo.vehiculo');

    Route::middleware(['permission:ver_usuarios_sitema'])->group(function () {
      Route::get('/users', function () {return Inertia::render('users');})->name('users');
      Route::get('/Get/Users',[UsersController::class,"ReadUsers"])->name('getusers');
      Route::post('/Toggle/User',[UsersController::class,"ToggleActive"])->name('toggle.user');
      Route::get('/Get/User',[UsersController::class,"ReadUser"])->name('user.read');
      Route::post('/Create/User',[UsersController::class,"CreateUser"])->name('user.create');
      Route::post('/Update/User',[UsersController::class,"UpdateUser"])->name('user.update');
    });
    Route::middleware(['permission:ver_presupuestos'])->group(function () {
      Route::get('cortana/presupuestos',[CortanaController::class,'PresupuestosVista'])->name('Cortana.Presupuesto.Vista');
      Route::get('cortana/get/presusupuestos',[CortanaController::class,'GetItems'])->name('Cortana.Presupuesto.Items');
      Route::get('cortana/get/orden-servicio',[CortanaController::class,'GetOrdenServicio'])->name('Cortana.OrdenServicio.Items');
      Route::post('cortana/orden/servicio/create',[CortanaController::class,'CreateOrdenServico'])->name('Cortana.OrdenServicio.Create');
      Route::get('presupuesto/get/datos/orden',[PresupuestosController::class,'GetDataPerOrdenServicio'])->name('Presupuesto.Get.Data_Orden');
      Route::post('presupuesto/create',[PresupuestosController::class,'CreatePresupuesto'])->name('Presupuesto.Create');
    });

    Route::get('cortana/recepciones/vehiculares',[CortanaController::class,'RecepcionVehicularVista'])->name('Cortana.OrdenesServicio.Vista');

    Route::get('/dashboard', function () { return Inertia::render('Dashboard');})->name('dashboard');

    Route::get('/Get/Permisos/User',[UsersController::class,"GetPermisos"])->name('getpermisosuser');
    Route::get('/Get/Modulos/User',[UsersController::class,"GetModulos"])->name('get.modulos.user');
    
    Route::post('/Toggle/Modulos/User',[UsersController::class,"ToggleModulo"])->name('toggle.modulo');
    Route::post('/Toggle/Roles/User',[UsersController::class,"ToggleRole"])->name('toggle.role');
    Route::post('/Toggle/Permisos/User',[UsersController::class,"TogglePermiso"])->name('toggle.permiso');
    Route::get('/User/Notifications',[UsersController::class,"GetNotificaciones"])->name('getnotifications');
    Route::get('/User/Read/Notifications',[UsersController::class,"ReadNotification"])->name('readnotification');
    Route::get('/employees',[EmpleadosController::class,'View'])->name('employees');
    Route::get('/employees/read',[EmpleadosController::class,'read'])->name('employees.read');
    Route::post('/employees/create',[EmpleadosController::class,'create'])->name('employees.create');
    
    
    Route::get('select2/empresas',[select2controller::class,'Empresas'])->name('Select2.Empresas');
    Route::get('select2/clientes',[select2controller::class,'Clientes'])->name('Select2.Clientes');
    Route::get('select2/economicos',[select2controller::class,'Economicos'])->name('Select2.Economico');
    Route::get('select2/vehiculos/conceptos/disponibles',[select2controller::class,'VehiculosConceptosPorModulo'])->name('Select2.Vehiculos.Conceptos.Modulos');
    Route::get('select2/regimenes/fiscales',[select2controller::class,'RegimenesFiscales'])->name('Select2.Regimenes.Fiscales');
    
    Route::get('select/niveles/combustible',[selectcontroller::class,'NivelesCombustible'])->name('select.niveles.combustible');
    Route::get('select/modulos/orden',[selectcontroller::class,'ModulosOrden'])->name('select.modulos.disponibles.usuario');
    Route::get('select/estatus',[selectcontroller::class,'EstatusIdsPerCategory'])->name('select.status');
    Route::get('select/tipos/vehiculos',[selectcontroller::class,'TiposVehiculosGeneral'])->name('select.tipos.vehiculos');
    
    Route::get('ComboBox/OrdenesServicio',[ComboboxController::class,'GetOrdenesServicio'])->name('Combobox.Ordenes_Servicio');
    Route::get('ComboBox/Ubicacion',[ComboboxController::class,'GetUbicaciones'])->name('Combobox.Ubicaciones');
    Route::get('ComboBox/AdministradoresTrasporte',[ComboboxController::class,'GetAdministradoresTrasporte'])->name('Combobox.Administradores_Trasporte');
    Route::get('ComboBox/JefesProceso',[ComboboxController::class,'GetJefesProceso'])->name('Combobox.Jefes_Procesos');
    Route::get('ComboBox/Trabajadores',[ComboboxController::class,'GetTrabajadores'])->name('Combobox.Trabajadores');
    Route::get('ComboBox/Tecnicos',[ComboboxController::class,'GetTecnicos'])->name('Combobox.Tecnicos');
    Route::get('ComboBox/Vehiculo/Economico',[ComboboxController::class,'GetVehiculoEconomico'])->name('Combobox.Vehiculo.Economico');
    Route::get('ComboBox/Vehiculo/Placas',[ComboboxController::class,'GetVehiculoPlacas'])->name('Combobox.Vehiculo.Placas');
    Route::get('ComboBox/ubicaciones',[ComboboxController::class,'GetUbicaciones'])->name('Combobox.ubicaciones');

    Route::get('vehiculo/get/datos',[VehiculoController::class,'GetDatos'])->name('Vehiculo.Get.Datos');
    Route::get('vehiculo/find/datos',[VehiculoController::class,'FindDatos'])->name('Vehiculo.Find');
    Route::post('vehiculo/Create/update',[VehiculoController::class,'CreateOrUpdate'])->name('Vehiculo.CreateOrUpdate');
    Route::get('Admin/Caja',[CajaController::class,'View'])->name('Admin.Caja');
    Route::get('Admin/Caja/Read',[CajaController::class,'Read'])->name('Admin.Caja.Read');
  });
  
  Route::get('migrar/caja',[MigrateDataBaseOld::class,'migrateCaja']);
