<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Servicios\ServiciosController;
use App\Http\Controllers\Servicios\ClienteController;
use App\Http\Controllers\Servicios\SolicitudServicioController;
use App\Http\Controllers\Servicios\RecepcionController;
use App\Http\Controllers\Servicios\DiagnosticoController;
use App\Http\Controllers\Servicios\TipoServicioController;
use App\Http\Controllers\Servicios\PromocionController;
use App\Http\Controllers\Servicios\DescuentoController;
use App\Http\Controllers\Servicios\PresupuestoController;

/*
|--------------------------------------------------------------------------
| Rutas del Módulo de SERVICIOS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('servicios')->name('servicios.')->group(function () {

    // Tipos de Servicio - CRUD Funcional con Livewire
    Route::controller(TipoServicioController::class)->group(function () {
        Route::get('/tipos-servicio', 'index')->name('tipos-servicio.index');
        Route::get('/tipos-servicio/create', 'create')->name('tipos-servicio.create');
        Route::get('/tipos-servicio/{tipoServicio}/edit', 'edit')->name('tipos-servicio.edit');
    });

    // Clientes - CRUD Funcional con Livewire
    Route::controller(ClienteController::class)->group(function () {
        Route::get('/clientes', 'index')->name('clientes.index');
        Route::get('/clientes/create', 'create')->name('clientes.create');
        Route::get('/clientes/registrar', 'create')->name('clientes.registrar'); // Alias para compatibilidad
        Route::get('/clientes/{cliente}/edit', 'edit')->name('clientes.edit');
    });

    // Clientes - Otras vistas
    Route::get('/clientes/historial', [ServiciosController::class, 'clientesHistorial'])->name('clientes.historial');

    // Solicitudes de Servicio - CRUD Funcional con Livewire
    Route::controller(SolicitudServicioController::class)->group(function () {
        Route::get('/solicitudes', 'index')->name('solicitudes.index');
        Route::get('/solicitudes/create', 'create')->name('solicitudes.create');
        Route::get('/solicitudes/{solicitud}/edit', 'edit')->name('solicitudes.edit');
    });

    // Recepciones - CRUD Funcional con Livewire
    Route::controller(RecepcionController::class)->group(function () {
        Route::get('/recepciones', 'index')->name('recepciones.index');
        Route::get('/recepcion', 'index')->name('recepcion.index'); // Alias para compatibilidad con menú
        Route::get('/recepciones/create', 'create')->name('recepciones.create');
        Route::get('/recepciones/{recepcion}/edit', 'edit')->name('recepciones.edit');
    });

    // Diagnósticos - CRUD Funcional con Livewire
    Route::controller(DiagnosticoController::class)->group(function () {
        Route::get('/diagnosticos', 'index')->name('diagnosticos.index');
        Route::get('/diagnostico', 'index')->name('diagnostico.index'); // Alias para compatibilidad con menú
        Route::get('/diagnosticos/create', 'create')->name('diagnosticos.create');
        Route::get('/diagnosticos/{diagnostico}/edit', 'edit')->name('diagnosticos.edit');
    });

    // Presupuestos - CRUD Funcional con Livewire
    Route::controller(PresupuestoController::class)->group(function () {
        Route::get('/presupuestos', 'index')->name('presupuestos.index');
        Route::get('/presupuestos/create', 'create')->name('presupuestos.create');
        Route::get('/presupuestos/{presupuesto}/edit', 'edit')->name('presupuestos.edit');
    });

    // Gestión de Servicios Técnicos - Otras vistas
    Route::get('/ordenes', [ServiciosController::class, 'ordenes'])->name('ordenes.index');
    Route::get('/entrega', [ServiciosController::class, 'entrega'])->name('entrega.index');

    // Promociones - CRUD Funcional con Livewire
    Route::controller(PromocionController::class)->group(function () {
        Route::get('/promociones', 'index')->name('promociones.index');
        Route::get('/promociones/create', 'create')->name('promociones.create');
        Route::get('/promociones/{promocion}/edit', 'edit')->name('promociones.edit');
    });

    // Descuentos - CRUD Funcional con Livewire
    Route::controller(DescuentoController::class)->group(function () {
        Route::get('/descuentos', 'index')->name('descuentos.index');
        Route::get('/descuentos/create', 'create')->name('descuentos.create');
        Route::get('/descuentos/{descuento}/edit', 'edit')->name('descuentos.edit');
    });

    // Reclamos
    Route::get('/reclamos/registrar', [ServiciosController::class, 'reclamosRegistrar'])->name('reclamos.registrar');
    Route::get('/reclamos/seguimiento', [ServiciosController::class, 'reclamosSeguimiento'])->name('reclamos.seguimiento');

    // Informes
    Route::get('/informes', [ServiciosController::class, 'informes'])->name('informes.index');
});
