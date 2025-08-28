<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CortanaController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\UsersController;
Route::get('/', function () {
  return redirect('/login');

});

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('Dashboard');})->name('dashboard');

    Route::get('/users', function () {return Inertia::render('users');})->name('users');

    Route::get('/Get/Users',[UsersController::class,"ReadUsers"])->name('getusers');
    Route::get('/Get/Permisos/User',[UsersController::class,"GetPermisos"])->name('getpermisosuser');
    Route::post('/Toggle/Roles/User',[UsersController::class,"ToggleRole"])->name('toggle.role');
    Route::post('/Toggle/Permisos/User',[UsersController::class,"TogglePermiso"])->name('toggle.permiso');
    Route::get('/User/Notifications',[UsersController::class,"GetNotificaciones"])->name('getnotifications');
    Route::get('/User/Read/Notifications',[UsersController::class,"ReadNotification"])->name('readnotification');

    Route::get('/employees',[EmpleadosController::class,'View'])->name('employees');
    Route::post('/employees/create',[EmpleadosController::class,'create'])->name('employees.create');


    Route::get('cortana/vista/modulo',[CortanaController::class,'PresupuestosVista'])->name('Cortana.Presupuesto.Vista');
});
