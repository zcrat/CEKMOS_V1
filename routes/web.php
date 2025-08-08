<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmpleadosController;

Route::get('/', function () {
  return redirect('/login');

});

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('Dashboard');})->name('dashboard');

    Route::get('/users', function () {return Inertia::render('users');})->name('users');
    
    Route::get('/employees',[EmpleadosController::class,'View'])->name('employees');
    Route::post('/employees/create',[EmpleadosController::class,'create'])->name('employees.create');
});
