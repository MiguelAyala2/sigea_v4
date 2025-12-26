<?php

use App\Http\Controllers\Compras\CompraController;
use App\Http\Controllers\Compras\RecepcionController;
use App\Http\Controllers\Compras\ReporteController;
use App\Http\Controllers\Compras\ProveedorController;
use App\Http\Controllers\Compras\PagoController;
use App\Http\Controllers\Compras\NotaCreditoController;
use App\Http\Controllers\Compras\NotaDebitoController;
use App\Http\Controllers\Compras\RemisionController;
use App\Http\Controllers\Compras\LibroComprasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('compras')->name('compras.')->group(function () {

    // ==================== DASHBOARD ====================
    Route::get('/dashboard', function () {
        return view('compras.dashboard.index');
    })->name('dashboard')->middleware('can:compras.dashboard');

    Route::get('/dashboard/exportar-pdf', [App\Http\Controllers\Compras\DashboardController::class, 'exportarPDF'])
        ->name('dashboard.exportar-pdf')->middleware('can:compras.dashboard');

    Route::get('/dashboard/exportar-excel', [App\Http\Controllers\Compras\DashboardController::class, 'exportarExcel'])
        ->name('dashboard.exportar-excel')->middleware('can:compras.dashboard');

    Route::get('/dashboard/flujo', function () {
        return view('compras.dashboard.flujo');
    })->name('dashboard.flujo')->middleware('can:compras.dashboard');

    Route::get('/dashboard/flujo/{compra}', function ($compra) {
        return view('compras.dashboard.flujo', compact('compra'));
    })->name('dashboard.flujo.show')->middleware('can:compras.dashboard');

    // ==================== COMPRAS/FACTURAS ====================
    Route::prefix('compras')->name('compras.')->group(function () {
        // Listado
        Route::get('/', function () {
            return view('compras.compras.index');
        })->name('index')->middleware('can:compras.compras.ver');

        // Exportaciones
        Route::get('/exportar-pdf', [CompraController::class, 'exportarPDF'])
            ->name('exportar-pdf')->middleware('can:compras.compras.ver');

        Route::get('/exportar-excel', [CompraController::class, 'exportarExcel'])
            ->name('exportar-excel')->middleware('can:compras.compras.ver');

        // Crear
        Route::get('/crear', function () {
            return view('compras.compras.create');
        })->name('create')->middleware('can:compras.compras.crear');

        // Mostrar
        Route::get('/{compra}', function (App\Models\Compras\Compra $compra) {
            return view('compras.compras.show', compact('compra'));
        })->name('show')->middleware('can:compras.compras.ver');

        // Imprimir PDF
        Route::get('/{compra}/imprimir-pdf', [CompraController::class, 'imprimirPDF'])
            ->name('imprimir-pdf')->middleware('can:compras.compras.ver');

        // Editar
        Route::get('/{compra}/editar', function (App\Models\Compras\Compra $compra) {
            return view('compras.compras.edit', compact('compra'));
        })->name('edit')->middleware('can:compras.compras.editar');

        // Acciones especiales
        Route::post('/{compra}/aprobar', [CompraController::class, 'aprobar'])
            ->name('aprobar')->middleware('can:compras.compras.aprobar');

        Route::post('/{compra}/anular', [CompraController::class, 'anular'])
            ->name('anular')->middleware('can:compras.compras.anular');

        Route::post('/{compra}/duplicar', [CompraController::class, 'duplicar'])
            ->name('duplicar')->middleware('can:compras.compras.crear');
    });

    // ==================== RECEPCIONES ====================
    Route::prefix('recepciones')->name('recepciones.')->group(function () {
        // Listado
        Route::get('/', function () {
            return view('compras.recepciones.index');
        })->name('index')->middleware('can:compras.recepciones.ver');

        // Crear (con o sin compra específica)
        Route::get('/crear', function () {
            return view('compras.recepciones.create');
        })->name('create')->middleware('can:compras.recepciones.crear');

        Route::get('/crear/{compra_id}', function ($compra_id) {
            return view('compras.recepciones.create', compact('compra_id'));
        })->name('create.compra')->middleware('can:compras.recepciones.crear');

        // Mostrar
        Route::get('/{recepcion}', function ($recepcion) {
            return view('compras.recepciones.show', compact('recepcion'));
        })->name('show')->middleware('can:compras.recepciones.ver');

        // Editar
        Route::get('/{recepcion}/editar', [RecepcionController::class, 'edit'])
            ->name('edit')->middleware('can:compras.recepciones.editar');

        // Acciones especiales
        Route::post('/{recepcion}/completar', [RecepcionController::class, 'completar'])
            ->name('completar')->middleware('can:compras.recepciones.editar');

        Route::post('/{recepcion}/marcar-parcial', [RecepcionController::class, 'marcarParcial'])
            ->name('marcar.parcial')->middleware('can:compras.recepciones.editar');
    });

    // ==================== PROVEEDORES ====================
    Route::prefix('proveedores')->name('proveedores.')->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');

        Route::get('/crear', [ProveedorController::class, 'create'])
            ->name('create')->middleware('can:Proveedores Crear');

        Route::get('/{proveedor}', [ProveedorController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');

        Route::get('/{proveedor}/editar', [ProveedorController::class, 'edit'])
            ->name('edit')->middleware('can:Proveedores Editar');

        Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])
            ->name('destroy')->middleware('can:Proveedores Eliminar');

        Route::post('/{proveedor}/toggle-activo', [ProveedorController::class, 'toggleActivo'])
            ->name('toggle-activo')->middleware('can:Proveedores Editar');
    });

    // ==================== APROBACIONES ====================
    Route::prefix('aprobaciones')->name('aprobaciones.')->group(function () {
        // Listado general
        Route::get('/', function () {
            return view('compras.aprobaciones.index');
        })->name('index')->middleware('can:compras.aprobaciones.ver');

        // Pendientes del usuario actual
        Route::get('/pendientes', function () {
            return view('compras.aprobaciones.pendientes');
        })->name('pendientes')->middleware('can:compras.aprobaciones.ver');

        // Mis aprobaciones
        Route::get('/mis-aprobaciones', function () {
            return view('compras.aprobaciones.mis-aprobaciones');
        })->name('mis-aprobaciones')->middleware('can:compras.aprobaciones.ver');

        // Acciones
        Route::post('/{aprobacion}/aprobar', [CompraController::class, 'aprobarDocumento'])
            ->name('aprobar.documento')->middleware('can:compras.aprobaciones.aprobar');

        Route::post('/{aprobacion}/rechazar', [CompraController::class, 'rechazarDocumento'])
            ->name('rechazar.documento')->middleware('can:compras.aprobaciones.rechazar');
    });

    // ==================== NOTAS DE CRÉDITO ====================
    Route::prefix('notas-credito')->name('notas-credito.')->group(function () {
        Route::get('/', [NotaCreditoController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');
        Route::get('/crear', [NotaCreditoController::class, 'create'])
            ->name('create')->middleware('can:Proveedores Crear');
        Route::post('/', [NotaCreditoController::class, 'store'])
            ->name('store')->middleware('can:Proveedores Crear');

        // API para cargar compras por proveedor y fecha
        Route::get('/api/compras-por-proveedor-fecha', [NotaCreditoController::class, 'getComprasByProveedorFecha'])
            ->name('api.compras-por-proveedor-fecha');

        // API para obtener detalles de una compra
        Route::get('/api/compra/{compraId}/detalles', [NotaCreditoController::class, 'getDetallesCompra'])
            ->name('api.compra.detalles');

        Route::get('/{notaCredito}', [NotaCreditoController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');
        Route::get('/{notaCredito}/editar', [NotaCreditoController::class, 'edit'])
            ->name('edit')->middleware('can:Proveedores Editar');
        Route::put('/{notaCredito}', [NotaCreditoController::class, 'update'])
            ->name('update')->middleware('can:Proveedores Editar');
        Route::post('/{notaCredito}/aplicar', [NotaCreditoController::class, 'aplicar'])
            ->name('aplicar')->middleware('can:Proveedores Editar');
        Route::post('/{notaCredito}/anular', [NotaCreditoController::class, 'anular'])
            ->name('anular')->middleware('can:Proveedores Editar');
        Route::delete('/{notaCredito}', [NotaCreditoController::class, 'destroy'])
            ->name('destroy')->middleware('can:Proveedores Eliminar');
    });

    // ==================== NOTAS DE DÉBITO ====================
    Route::prefix('notas-debito')->name('notas-debito.')->group(function () {
        Route::get('/', [NotaDebitoController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');
        Route::get('/crear', [NotaDebitoController::class, 'create'])
            ->name('create')->middleware('can:Proveedores Crear');
        Route::post('/', [NotaDebitoController::class, 'store'])
            ->name('store')->middleware('can:Proveedores Crear');

        // API para cargar compras por proveedor y fecha
        Route::get('/api/compras-por-proveedor-fecha', [NotaDebitoController::class, 'getComprasByProveedorFecha'])
            ->name('api.compras-por-proveedor-fecha');

        // API para obtener detalles de una compra
        Route::get('/api/compra/{compraId}/detalles', [NotaDebitoController::class, 'getDetallesCompra'])
            ->name('api.compra.detalles');

        Route::get('/{notaDebito}', [NotaDebitoController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');
        Route::get('/{notaDebito}/editar', [NotaDebitoController::class, 'edit'])
            ->name('edit')->middleware('can:Proveedores Editar');
        Route::put('/{notaDebito}', [NotaDebitoController::class, 'update'])
            ->name('update')->middleware('can:Proveedores Editar');
        Route::post('/{notaDebito}/aplicar', [NotaDebitoController::class, 'aplicar'])
            ->name('aplicar')->middleware('can:Proveedores Editar');
        Route::post('/{notaDebito}/anular', [NotaDebitoController::class, 'anular'])
            ->name('anular')->middleware('can:Proveedores Editar');
        Route::delete('/{notaDebito}', [NotaDebitoController::class, 'destroy'])
            ->name('destroy')->middleware('can:Proveedores Eliminar');
    });

    // ==================== REMISIONES ====================
    Route::prefix('remisiones')->name('remisiones.')->group(function () {
        Route::get('/', [RemisionController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');
        Route::get('/crear', [RemisionController::class, 'create'])
            ->name('create')->middleware('can:Proveedores Crear');
        Route::post('/', [RemisionController::class, 'store'])
            ->name('store')->middleware('can:Proveedores Crear');

        // API endpoints para sucursales
        Route::get('/sucursal/{sucursalId}/depositos', [RemisionController::class, 'getDepositosPorSucursal'])
            ->name('sucursal.depositos');
        Route::get('/sucursal/{sucursalId}/datos', [RemisionController::class, 'getSucursalDatos'])
            ->name('sucursal.datos');

        Route::get('/{remision}', [RemisionController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');
        Route::get('/{remision}/editar', [RemisionController::class, 'edit'])
            ->name('edit')->middleware('can:Proveedores Editar');
        Route::post('/{remision}/recibir', [RemisionController::class, 'recibir'])
            ->name('recibir')->middleware('can:Proveedores Editar');
        Route::post('/{remision}/anular', [RemisionController::class, 'anular'])
            ->name('anular')->middleware('can:Proveedores Editar');
        Route::delete('/{remision}', [RemisionController::class, 'destroy'])
            ->name('destroy')->middleware('can:Proveedores Eliminar');
    });

    // ==================== REPORTES ====================
    Route::prefix('reportes')->name('reportes.')->group(function () {
        // Libro de Compras (SET)
        Route::get('/libro-compras', [LibroComprasController::class, 'index'])
            ->name('libro-compras')->middleware('can:compras.reportes.libro');

        Route::get('/libro-compras/exportar-pdf', [LibroComprasController::class, 'exportarPDF'])
            ->name('libro-compras.exportar-pdf')->middleware('can:compras.reportes.libro');

        Route::get('/libro-compras/exportar-excel', [LibroComprasController::class, 'exportarExcel'])
            ->name('libro-compras.exportar-excel')->middleware('can:compras.reportes.libro');

        // Análisis de Proveedores
        Route::get('/analisis-proveedores', function () {
            return view('compras.reportes.analisis-proveedores');
        })->name('analisis-proveedores')->middleware('can:compras.reportes.analisis');

        // Flujo de Aprobaciones
        Route::get('/flujo-aprobaciones', function () {
            return view('compras.reportes.flujo-aprobaciones');
        })->name('flujo-aprobaciones')->middleware('can:compras.reportes.flujo');

        // Compras por Período
        Route::get('/compras-periodo', [ReporteController::class, 'comprasPeriodo'])
            ->name('compras-periodo')->middleware('can:compras.reportes.ver');

        // Recepciones vs Compras
        Route::get('/recepciones-vs-compras', [ReporteController::class, 'recepcionesVsCompras'])
            ->name('recepciones-vs-compras')->middleware('can:compras.reportes.ver');

        // Exportaciones
        Route::get('/exportar-libro-compras', [ReporteController::class, 'exportarLibroCompras'])
            ->name('exportar.libro-compras')->middleware('can:compras.reportes.libro');

        Route::get('/exportar-analisis-proveedores', [ReporteController::class, 'exportarAnalisisProveedores'])
            ->name('exportar.analisis-proveedores')->middleware('can:compras.reportes.analisis');
    });

    // ==================== CONFIGURACIÓN ====================
    Route::prefix('configuracion')->name('configuracion.')->group(function () {
        // Configuración general del módulo
        Route::get('/', function () {
            return view('compras.configuracion.index');
        })->name('index')->middleware('can:compras.configuracion.ver');

        // Flujos de aprobación
        Route::get('/flujos-aprobacion', function () {
            return view('compras.configuracion.flujos-aprobacion');
        })->name('flujos-aprobacion')->middleware('can:compras.configuracion.editar');

        // Tipos de documento
        Route::get('/tipos-documento', function () {
            return view('compras.configuracion.tipos-documento');
        })->name('tipos-documento')->middleware('can:compras.configuracion.editar');
    });

    // ==================== PEDIDOS DE COMPRA ====================
    Route::prefix('pedidos')->name('pedidos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Compras\PedidoCompraController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');

        Route::get('/exportar-pdf', [App\Http\Controllers\Compras\PedidoCompraController::class, 'exportarPDF'])
            ->name('exportar-pdf')->middleware('can:Proveedores Ver');

        Route::get('/exportar-excel', [App\Http\Controllers\Compras\PedidoCompraController::class, 'exportarExcel'])
            ->name('exportar-excel')->middleware('can:Proveedores Ver');

        Route::get('/crear', [App\Http\Controllers\Compras\PedidoCompraController::class, 'create'])
            ->name('create')->middleware('can:Proveedores Crear');

        Route::get('/{pedido}', [App\Http\Controllers\Compras\PedidoCompraController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');

        Route::get('/{pedido}/imprimir-pdf', [App\Http\Controllers\Compras\PedidoCompraController::class, 'imprimirPDF'])
            ->name('imprimir-pdf')->middleware('can:Proveedores Ver');

        Route::get('/{pedido}/editar', [App\Http\Controllers\Compras\PedidoCompraController::class, 'edit'])
            ->name('edit')->middleware('can:Proveedores Editar');

        Route::post('/{pedido}/aprobar', [App\Http\Controllers\Compras\PedidoCompraController::class, 'aprobar'])
            ->name('aprobar')->middleware('can:Proveedores Editar');

        Route::post('/{pedido}/rechazar', [App\Http\Controllers\Compras\PedidoCompraController::class, 'rechazar'])
            ->name('rechazar')->middleware('can:Proveedores Editar');
    });

    // ==================== PRESUPUESTOS ====================
    Route::prefix('presupuestos')->name('presupuestos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Compras\PresupuestoController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');

        // Exportaciones
        Route::get('/exportar-pdf', [App\Http\Controllers\Compras\PresupuestoController::class, 'exportarPDF'])
            ->name('exportar-pdf')->middleware('can:Proveedores Ver');

        Route::get('/exportar-excel', [App\Http\Controllers\Compras\PresupuestoController::class, 'exportarExcel'])
            ->name('exportar-excel')->middleware('can:Proveedores Ver');

        Route::get('/crear', [App\Http\Controllers\Compras\PresupuestoController::class, 'create'])
            ->name('create')->middleware('can:Proveedores Crear');

        Route::get('/comparar', [App\Http\Controllers\Compras\PresupuestoController::class, 'comparar'])
            ->name('comparar')->middleware('can:Proveedores Ver');

        Route::get('/{presupuesto}', [App\Http\Controllers\Compras\PresupuestoController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');

        Route::get('/{presupuesto}/imprimir-pdf', [App\Http\Controllers\Compras\PresupuestoController::class, 'imprimirPDF'])
            ->name('imprimir-pdf')->middleware('can:Proveedores Ver');

        Route::get('/{presupuesto}/editar', [App\Http\Controllers\Compras\PresupuestoController::class, 'edit'])
            ->name('edit')->middleware('can:Proveedores Editar');

        Route::post('/{presupuesto}/seleccionar', [App\Http\Controllers\Compras\PresupuestoController::class, 'seleccionar'])
            ->name('seleccionar')->middleware('can:Proveedores Editar');
    });

    // ==================== ÓRDENES DE COMPRA ====================
    Route::prefix('ordenes')->name('ordenes.')->group(function () {
        Route::get('/', [App\Http\Controllers\Compras\OrdenCompraController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');

        // Exportaciones
        Route::get('/exportar-pdf', [App\Http\Controllers\Compras\OrdenCompraController::class, 'exportarPDF'])
            ->name('exportar-pdf')->middleware('can:Proveedores Ver');

        Route::get('/exportar-excel', [App\Http\Controllers\Compras\OrdenCompraController::class, 'exportarExcel'])
            ->name('exportar-excel')->middleware('can:Proveedores Ver');

        Route::get('/crear', [App\Http\Controllers\Compras\OrdenCompraController::class, 'create'])
            ->name('create')->middleware('can:Proveedores Crear');

        Route::get('/{orden}', [App\Http\Controllers\Compras\OrdenCompraController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');

        Route::get('/{orden}/imprimir-pdf', [App\Http\Controllers\Compras\OrdenCompraController::class, 'imprimirPDF'])
            ->name('imprimir-pdf')->middleware('can:Proveedores Ver');

        Route::get('/{orden}/editar', [App\Http\Controllers\Compras\OrdenCompraController::class, 'edit'])
            ->name('edit')->middleware('can:Proveedores Editar');

        Route::get('/{orden}/imprimir', [App\Http\Controllers\Compras\OrdenCompraController::class, 'imprimir'])
            ->name('imprimir')->middleware('can:Proveedores Ver');

        Route::post('/{orden}/emitir', [App\Http\Controllers\Compras\OrdenCompraController::class, 'emitir'])
            ->name('emitir')->middleware('can:Proveedores Editar');

        Route::post('/{orden}/enviar', [App\Http\Controllers\Compras\OrdenCompraController::class, 'enviar'])
            ->name('enviar')->middleware('can:Proveedores Editar');

        Route::post('/{orden}/confirmar', [App\Http\Controllers\Compras\OrdenCompraController::class, 'confirmar'])
            ->name('confirmar')->middleware('can:Proveedores Editar');

        Route::post('/{orden}/cancelar', [App\Http\Controllers\Compras\OrdenCompraController::class, 'cancelar'])
            ->name('cancelar')->middleware('can:Proveedores Editar');
    });

    // ==================== CUENTAS POR PAGAR / PAGOS ====================
    Route::prefix('pagos')->name('pagos.')->group(function () {
        Route::get('/', [PagoController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');

        // Exportaciones
        Route::get('/exportar-pdf', [PagoController::class, 'exportarPDF'])
            ->name('exportar-pdf')->middleware('can:Proveedores Ver');

        Route::get('/exportar-excel', [PagoController::class, 'exportarExcel'])
            ->name('exportar-excel')->middleware('can:Proveedores Ver');

        Route::get('/{cuenta}', [PagoController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');
    });

    Route::prefix('cuentas-pagar')->name('cuentas-pagar.')->group(function () {
        Route::get('/', [PagoController::class, 'index'])
            ->name('index')->middleware('can:Proveedores Ver');

        Route::get('/{cuenta}', [PagoController::class, 'show'])
            ->name('show')->middleware('can:Proveedores Ver');
    });

    // Redirecciones eliminadas - Los módulos ya existen

    // ==================== RUTAS DE API PARA COMPONENTES ====================
    Route::prefix('api')->name('api.')->group(function () {
        // Búsqueda de compras para select2
        Route::get('/buscar-compras', [CompraController::class, 'buscarCompras'])
            ->name('buscar.compras');

        // Búsqueda de proveedores
        Route::get('/buscar-proveedores', [CompraController::class, 'buscarProveedores'])
            ->name('buscar.proveedores');

        // Obtener detalles de compra
        Route::get('/compra/{compra}/detalles', [CompraController::class, 'getDetallesCompra'])
            ->name('compra.detalles');

        // Estadísticas para dashboard
        Route::get('/estadisticas/dashboard', [CompraController::class, 'getEstadisticasDashboard'])
            ->name('estadisticas.dashboard');
    });
});