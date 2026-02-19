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
use Illuminate\Http\Request;
Route::get('/', function () {
  return redirect('/login');
});
Route::get('/trasformar',[PruebasController::class,"TransformDataToImport"]);
Route::get('/trasformar2',[PruebasController::class,"TransformDataToImport2"]);
Route::get('/trasformar3',[PruebasController::class,"TransformDataToImport3"]);
Route::get('/trasformar4',[PruebasController::class,"TransformDataToImport4"]);
Route::get('/trasformar5',[PruebasController::class,"TransformDataToImport5"]);
Route::get('/trasformar6',[PruebasController::class,"TransformDataToImport6"]);
Route::get('/trasformar7',[PruebasController::class,"TransformDataToImport7"]);
Route::get('/trasformar8',[PruebasController::class,"TransformDataToImport8"]);
Route::get('/trasformar9',[PruebasController::class,"TransformDataToImport9"]);
Route::get('/trasformar10',[PruebasController::class,"TransformDataToImport10"]);
Route::get('/userid', function (Request $request) {
    return Crypt::encrypt($request->user()->id);
  })->name('userid');
Route::middleware(['auth:sanctum',config('jetstream.auth_session')])->group(function () {
  
});
Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('Dashboard');})->name('dashboard');

    Route::get('/users', function () {return Inertia::render('users');})->name('users');



    Route::get('/Get/Users',[UsersController::class,"ReadUsers"])->name('getusers');
    Route::get('/Get/Permisos/User',[UsersController::class,"GetPermisos"])->name('getpermisosuser');
    Route::get('/Get/Modulos/User',[UsersController::class,"GetModulos"])->name('get.modulos.user');
    Route::post('/Delete/User',[UsersController::class,"DeleteUser"])->name('delete.user');
    Route::post('/Toggle/Modulos/User',[UsersController::class,"ToggleModulo"])->name('toggle.modulo');
    Route::post('/Toggle/Roles/User',[UsersController::class,"ToggleRole"])->name('toggle.role');
    Route::post('/Toggle/Permisos/User',[UsersController::class,"TogglePermiso"])->name('toggle.permiso');
    Route::get('/User/Notifications',[UsersController::class,"GetNotificaciones"])->name('getnotifications');
    Route::get('/User/Read/Notifications',[UsersController::class,"ReadNotification"])->name('readnotification');

    Route::get('/employees',[EmpleadosController::class,'View'])->name('employees');
    Route::get('/employees/read',[EmpleadosController::class,'read'])->name('employees.read');
    Route::post('/employees/create',[EmpleadosController::class,'create'])->name('employees.create');

    Route::get('cortana/presupuestos',[CortanaController::class,'PresupuestosVista'])->name('Cortana.Presupuesto.Vista');
    Route::get('cortana/get/presusupuestos',[CortanaController::class,'GetItems'])->name('Cortana.Presupuesto.Items');

    Route::get('select2/empresas',[select2controller::class,'Empresas'])->name('Select2.Empresas');
    Route::get('select2/regimenes/fiscales',[select2controller::class,'RegimenesFiscales'])->name('Select2.Regimenes.Fiscales');
    
    Route::get('select/niveles/combustible',[selectcontroller::class,'NivelesCombustible'])->name('select.niveles.combustible');
    Route::get('select/modulos/orden',[selectcontroller::class,'ModulosOrden'])->name('select.modulos.disponibles.usuario');

    Route::get('ComboBox/OrdenesServicio',[ComboboxController::class,'GetOrdenesServicio'])->name('Combobox.Ordenes_Servicio');
    Route::get('ComboBox/Ubicacion',[ComboboxController::class,'GetUbicaciones'])->name('Combobox.Ubicaciones');
    Route::get('ComboBox/AdministradoresTrasporte',[ComboboxController::class,'GetAdministradoresTrasporte'])->name('Combobox.Administradores_Trasporte');
    Route::get('ComboBox/JefesProceso',[ComboboxController::class,'GetJefesProceso'])->name('Combobox.Jefes_Procesos');
    Route::get('ComboBox/Trabajadores',[ComboboxController::class,'GetTrabajadores'])->name('Combobox.Trabajadores');
    Route::get('ComboBox/Tecnicos',[ComboboxController::class,'GetTecnicos'])->name('Combobox.Tecnicos');
    Route::get('ComboBox/Vehiculo/Economico',[ComboboxController::class,'GetVehiculoEconomico'])->name('Combobox.Vehiculo.Economico');
    Route::get('ComboBox/Vehiculo/Placas',[ComboboxController::class,'GetVehiculoPlacas'])->name('Combobox.Vehiculo.Placas');

    Route::get('vehiculo/get/datos',[VehiculoController::class,'GetDatos'])->name('Vehiculo.Get.Datos');

    Route::get('presupuesto/get/datos/orden',[PresupuestosController::class,'GetDataPerOrdenServicio'])->name('Presupuesto.Get.Data_Orden');
    Route::post('presupuesto/create',[PresupuestosController::class,'CreatePresupuesto'])->name('Presupuesto.Create');

    Route::get('Admin/Caja',[CajaController::class,'View'])->name('Admin.Caja');
    Route::get('Admin/Caja/Read',[CajaController::class,'Read'])->name('Admin.Caja.Read');
  });
  
  Route::get('migrar/caja',[MigrateDataBaseOld::class,'migrateCaja']);
