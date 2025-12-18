<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

include_once __DIR__.'/admin.php'; // rutas de admin
include_once __DIR__.'/empresa.php'; // rutas de empresa
include_once __DIR__.'/stocks.php'; // Módulo Stocks
include_once __DIR__.'/compras.php'; // Módulo Compras
include_once __DIR__.'/servicios.php'; // Módulo Servicios (Plantillas visuales)
include_once __DIR__.'/ventas.php'; // Módulo Ventas y Cobros (Plantillas visuales)


Route::get('/', function () {
    return view('welcome');
});

// Auth::routes();

// Rutas de Autenticacion
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
