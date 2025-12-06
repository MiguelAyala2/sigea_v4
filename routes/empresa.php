<?php

use App\Http\Controllers\Empresa\DepositoController;
use App\Http\Controllers\Empresa\EmpresaController;
use App\Http\Controllers\Empresa\PuntoExpedicionController;
use App\Http\Controllers\Empresa\SucursalController;
use App\Http\Controllers\Empresa\TimbradoController;
use Illuminate\Support\Facades\Route;

Route::prefix('empresa')
    ->name('empresa.')
    ->middleware(['auth'])
    ->group(function () {

        // Empresa (singular - configuración única)
        Route::controller(EmpresaController::class)->group(function () {
            Route::get('/', 'index')->name('empresa.index');
            Route::get('/editar', 'edit')->name('empresa.edit');
        });

        // Sucursales
        Route::controller(SucursalController::class)->group(function () {
            Route::get('/sucursales', 'index')->name('sucursales.index');
            Route::get('/sucursales/create', 'create')->name('sucursales.create');
            Route::get('/sucursales/{sucursal}/edit', 'edit')->name('sucursales.edit');
        });

        // Depósitos
        Route::controller(DepositoController::class)->group(function () {
            Route::get('/depositos', 'index')->name('depositos.index');
            Route::get('/depositos/create', 'create')->name('depositos.create');
            Route::get('/depositos/{deposito}/edit', 'edit')->name('depositos.edit');
        });

        // Puntos de Expedición
        Route::controller(PuntoExpedicionController::class)->group(function () {
            Route::get('/puntos-expedicion', 'index')->name('puntos-expedicion.index');
            Route::get('/puntos-expedicion/create', 'create')->name('puntos-expedicion.create');
            Route::get('/puntos-expedicion/{puntoExpedicion}/edit', 'edit')->name('puntos-expedicion.edit');
        });

        // Timbrados
        Route::controller(TimbradoController::class)->group(function () {
            Route::get('/timbrados', 'index')->name('timbrados.index');
            Route::get('/timbrados/create', 'create')->name('timbrados.create');
            Route::get('/timbrados/{timbrado}/edit', 'edit')->name('timbrados.edit');
        });
    });
