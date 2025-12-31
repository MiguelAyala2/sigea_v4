<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ventas\PedidoClienteController;
use App\Http\Controllers\Ventas\CajaController;
use App\Http\Controllers\Ventas\FacturaController;
use App\Http\Controllers\Ventas\CuentaPorCobrarController;
use App\Http\Controllers\Ventas\RemisionController;
use App\Http\Controllers\Ventas\NotaCreditoController;
use App\Http\Controllers\Ventas\NotaDebitoController;
use App\Http\Controllers\Ventas\InformesController;

/*
|--------------------------------------------------------------------------
| Rutas del Módulo de VENTAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('ventas')->name('ventas.')->group(function () {

    // Dashboard de Ventas
    Route::get('/', function () {
        return view('ventas.index');
    })->name('index');

    // Pedidos de Clientes
    Route::prefix('pedidos')->name('pedidos.')->group(function () {
        Route::get('/historial', [PedidoClienteController::class, 'index'])->name('historial');
        Route::get('/registrar', [PedidoClienteController::class, 'create'])->name('registrar');
        Route::get('/{pedido}', [PedidoClienteController::class, 'show'])->name('show');
        Route::get('/{pedido}/editar', [PedidoClienteController::class, 'edit'])->name('edit');
        Route::post('/{pedido}/confirmar', [PedidoClienteController::class, 'confirmar'])->name('confirmar');
        Route::post('/{pedido}/preparar', [PedidoClienteController::class, 'preparar'])->name('preparar');
        Route::post('/{pedido}/listo-entregar', [PedidoClienteController::class, 'listoEntregar'])->name('listoEntregar');
        Route::post('/{pedido}/cancelar', [PedidoClienteController::class, 'cancelar'])->name('cancelar');
        Route::post('/{pedido}/anular', [PedidoClienteController::class, 'anular'])->name('anular');
    });

    // Caja
    Route::prefix('caja')->name('caja.')->group(function () {
        Route::get('/apertura', [CajaController::class, 'apertura'])->name('apertura');
        Route::get('/movimientos', [CajaController::class, 'movimientos'])->name('movimientos');
        Route::get('/cierre', [CajaController::class, 'cierre'])->name('cierre');
        Route::get('/arqueo', [CajaController::class, 'arqueo'])->name('arqueo');
        Route::get('/recaudaciones', [CajaController::class, 'recaudaciones'])->name('recaudaciones');
    });

    // Cotizaciones - Rutas temporales (placeholder)
    Route::get('/cotizaciones', function () {
        return view('ventas.cotizaciones.index');
    })->name('cotizaciones.index');

    Route::get('/cotizaciones/crear', function () {
        return view('ventas.cotizaciones.crear');
    })->name('cotizaciones.crear');

    // Facturas
    Route::prefix('facturas')->name('facturas.')->group(function () {
        Route::get('/', [FacturaController::class, 'index'])->name('index');
        Route::get('/crear', [FacturaController::class, 'create'])->name('crear');
        Route::get('/{factura}', [FacturaController::class, 'show'])->name('show');
        Route::get('/{factura}/editar', [FacturaController::class, 'edit'])->name('edit');
        Route::post('/{factura}/emitir', [FacturaController::class, 'emitir'])->name('emitir');
        Route::post('/{factura}/anular', [FacturaController::class, 'anular'])->name('anular');
        Route::get('/{factura}/pdf', [FacturaController::class, 'pdf'])->name('pdf');
        Route::post('/{factura}/email', [FacturaController::class, 'enviarEmail'])->name('email');
        Route::post('/{factura}/set', [FacturaController::class, 'enviarSET'])->name('set');
    });

    // Clientes - Rutas temporales (placeholder)
    Route::get('/clientes', function () {
        return view('ventas.clientes.index');
    })->name('clientes.index');

    Route::get('/clientes/crear', function () {
        return view('ventas.clientes.crear');
    })->name('clientes.crear');

    // Facturación - Rutas temporales (placeholder)
    Route::get('/facturacion', function () {
        return view('ventas.facturacion.index');
    })->name('facturacion.index');

    // Cuentas por Cobrar
    Route::prefix('cuentas-cobrar')->name('cuentas-cobrar.')->group(function () {
        Route::get('/', [CuentaPorCobrarController::class, 'index'])->name('index');
        Route::get('/{cuenta}/cobrar', [CuentaPorCobrarController::class, 'cobrar'])->name('cobrar');
        Route::post('/{cuenta}/pago', [CuentaPorCobrarController::class, 'registrarPago'])->name('pago');
    });

    // Remisiones
    Route::prefix('remisiones')->name('remisiones.')->group(function () {
        Route::get('/', [RemisionController::class, 'index'])->name('index');
        Route::get('/crear', [RemisionController::class, 'crear'])->name('crear');
        Route::get('/{remision}', [RemisionController::class, 'show'])->name('show');
        Route::post('/{remision}/emitir', [RemisionController::class, 'emitir'])->name('emitir');
        Route::post('/{remision}/marcar-entregada', [RemisionController::class, 'marcarEntregada'])->name('marcarEntregada');
        Route::post('/{remision}/anular', [RemisionController::class, 'anular'])->name('anular');
        Route::get('/{remision}/pdf', [RemisionController::class, 'pdf'])->name('pdf');
        Route::get('/api/buscar-facturas', [RemisionController::class, 'buscarFacturas'])->name('buscarFacturas');
    });

    // Notas de Crédito
    Route::prefix('notas-credito')->name('notas-credito.')->group(function () {
        Route::get('/', [NotaCreditoController::class, 'index'])->name('index');
        Route::get('/crear', [NotaCreditoController::class, 'create'])->name('crear');
        Route::get('/{notaCredito}', [NotaCreditoController::class, 'show'])->name('show');
        Route::get('/{notaCredito}/editar', [NotaCreditoController::class, 'edit'])->name('editar');
        Route::post('/{notaCredito}/emitir', [NotaCreditoController::class, 'emitir'])->name('emitir');
        Route::post('/{notaCredito}/anular', [NotaCreditoController::class, 'anular'])->name('anular');
        Route::get('/{notaCredito}/pdf', [NotaCreditoController::class, 'pdf'])->name('pdf');
    });

    // Notas de Débito
    Route::prefix('notas-debito')->name('notas-debito.')->group(function () {
        Route::get('/', [NotaDebitoController::class, 'index'])->name('index');
        Route::get('/crear', [NotaDebitoController::class, 'create'])->name('crear');
        Route::get('/{notaDebito}', [NotaDebitoController::class, 'show'])->name('show');
        Route::get('/{notaDebito}/editar', [NotaDebitoController::class, 'edit'])->name('editar');
        Route::post('/{notaDebito}/emitir', [NotaDebitoController::class, 'emitir'])->name('emitir');
        Route::post('/{notaDebito}/anular', [NotaDebitoController::class, 'anular'])->name('anular');
        Route::get('/{notaDebito}/pdf', [NotaDebitoController::class, 'pdf'])->name('pdf');
    });

    // Cobranzas - Rutas temporales (placeholder)
    Route::get('/cobranzas/registrar', function () {
        return view('ventas.cobranzas.registrar');
    })->name('cobranzas.registrar');

    Route::get('/cobranzas/forma-pago', function () {
        return view('ventas.cobranzas.forma-pago');
    })->name('cobranzas.forma-pago');

    Route::get('/cobranzas/historial', function () {
        return view('ventas.cobranzas.historial');
    })->name('cobranzas.historial');

    // Libro de Ventas - Rutas temporales (placeholder)
    Route::get('/libro-ventas', function () {
        return view('ventas.libro-ventas.index');
    })->name('libro-ventas.index');

    // Informes
    Route::get('/informes', [InformesController::class, 'index'])->name('informes.index');

});
