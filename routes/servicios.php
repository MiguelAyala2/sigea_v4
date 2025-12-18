<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Servicios\ServiciosController;

/*
|--------------------------------------------------------------------------
| Rutas del Módulo de SERVICIOS
|--------------------------------------------------------------------------
| Plantillas visuales de ejemplo - Sin funcionalidad backend
*/

Route::middleware(['auth'])->prefix('servicios')->name('servicios.')->group(function () {

    // Clientes
    Route::get('/clientes/registrar', [ServiciosController::class, 'clientesRegistrar'])->name('clientes.registrar');
    Route::get('/clientes/historial', [ServiciosController::class, 'clientesHistorial'])->name('clientes.historial');

    // Gestión de Servicios Técnicos
    Route::get('/solicitudes', [ServiciosController::class, 'solicitudes'])->name('solicitudes.index');
    Route::get('/recepcion', [ServiciosController::class, 'recepcion'])->name('recepcion.index');
    Route::get('/diagnostico', [ServiciosController::class, 'diagnostico'])->name('diagnostico.index');
    Route::get('/presupuestos', [ServiciosController::class, 'presupuestos'])->name('presupuestos.index');
    Route::get('/ordenes', [ServiciosController::class, 'ordenes'])->name('ordenes.index');
    Route::get('/entrega', [ServiciosController::class, 'entrega'])->name('entrega.index');

    // Promociones y Descuentos
    Route::get('/promociones', [ServiciosController::class, 'promociones'])->name('promociones.index');
    Route::get('/descuentos', [ServiciosController::class, 'descuentos'])->name('descuentos.index');

    // Reclamos
    Route::get('/reclamos/registrar', [ServiciosController::class, 'reclamosRegistrar'])->name('reclamos.registrar');
    Route::get('/reclamos/seguimiento', [ServiciosController::class, 'reclamosSeguimiento'])->name('reclamos.seguimiento');

    // Informes
    Route::get('/informes', [ServiciosController::class, 'informes'])->name('informes.index');
});
