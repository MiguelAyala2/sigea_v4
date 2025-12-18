<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ventas\VentasController;

/*
|--------------------------------------------------------------------------
| Rutas del Módulo de VENTAS Y COBROS
|--------------------------------------------------------------------------
| Plantillas visuales de ejemplo - Sin funcionalidad backend
*/

Route::middleware(['auth'])->prefix('ventas')->name('ventas.')->group(function () {

    // Caja
    Route::get('/caja/apertura', [VentasController::class, 'cajaApertura'])->name('caja.apertura');
    Route::get('/caja/movimientos', [VentasController::class, 'cajaMovimientos'])->name('caja.movimientos');
    Route::get('/caja/cierre', [VentasController::class, 'cajaCierre'])->name('caja.cierre');
    Route::get('/caja/arqueo', [VentasController::class, 'cajaArqueo'])->name('caja.arqueo');
    Route::get('/caja/recaudaciones', [VentasController::class, 'cajaRecaudaciones'])->name('caja.recaudaciones');

    // Pedidos de Clientes
    Route::get('/pedidos/registrar', [VentasController::class, 'pedidosRegistrar'])->name('pedidos.registrar');
    Route::get('/pedidos/historial', [VentasController::class, 'pedidosHistorial'])->name('pedidos.historial');

    // Ventas y Facturación
    Route::get('/facturacion', [VentasController::class, 'facturacion'])->name('facturacion.index');
    Route::get('/cuentas-cobrar', [VentasController::class, 'cuentasCobrar'])->name('cuentas-cobrar.index');
    Route::get('/remisiones', [VentasController::class, 'remisiones'])->name('remisiones.index');
    Route::get('/notas-credito', [VentasController::class, 'notasCredito'])->name('notas-credito.index');
    Route::get('/notas-debito', [VentasController::class, 'notasDebito'])->name('notas-debito.index');

    // Cobranzas
    Route::get('/cobranzas/registrar', [VentasController::class, 'cobranzasRegistrar'])->name('cobranzas.registrar');
    Route::get('/cobranzas/forma-pago', [VentasController::class, 'cobranzasFormaPago'])->name('cobranzas.forma-pago');
    Route::get('/cobranzas/historial', [VentasController::class, 'cobranzasHistorial'])->name('cobranzas.historial');

    // Libro de Ventas
    Route::get('/libro-ventas', [VentasController::class, 'libroVentas'])->name('libro-ventas.index');

    // Informes
    Route::get('/informes', [VentasController::class, 'informes'])->name('informes.index');
});
