<?php

use App\Http\Controllers\Stock\CategoriaController;
use App\Http\Controllers\Stock\MarcaController;
use App\Http\Controllers\Stock\UnidadMedidaController;
use App\Http\Controllers\Stock\AtributoTipoController;
use App\Http\Controllers\Stock\ProductoController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Stock\ReporteController;
use Illuminate\Support\Facades\Route;

Route::prefix('stock')
    ->name('stock.')
    ->middleware(['auth'])
    ->group(function () {

    // CATEGORÍAS
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', [CategoriaController::class, 'index'])->name('index');
        Route::get('/create', [CategoriaController::class, 'create'])->name('create');
        Route::get('/{categoria}/edit', [CategoriaController::class, 'edit'])->name('edit');
    });

    // MARCAS
    Route::prefix('marcas')->name('marcas.')->group(function () {
        Route::get('/', [MarcaController::class, 'index'])->name('index');
        Route::get('/create', [MarcaController::class, 'create'])->name('create');
        Route::get('/{marca}/edit', [MarcaController::class, 'edit'])->name('edit');
    });

    // UNIDADES DE MEDIDA
    Route::prefix('unidades-medida')->name('unidades-medida.')->group(function () {
        Route::get('/', [UnidadMedidaController::class, 'index'])->name('index');
        Route::get('/create', [UnidadMedidaController::class, 'create'])->name('create');
        Route::get('/{unidadMedida}/edit', [UnidadMedidaController::class, 'edit'])->name('edit');
    });

    // ATRIBUTOS TIPO
    Route::prefix('atributos-tipo')->name('atributos-tipo.')->group(function () {
        Route::get('/', [AtributoTipoController::class, 'index'])->name('index');
        Route::get('/create', [AtributoTipoController::class, 'create'])->name('create');
        Route::get('/{atributo}/edit', [AtributoTipoController::class, 'edit'])->name('edit');
    });

    // PRODUCTOS
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('index');
        Route::get('/create', [ProductoController::class, 'create'])->name('create');

        // Exportaciones
        Route::get('/exportar-pdf', [ProductoController::class, 'exportarPDF'])->name('exportar-pdf');
        Route::get('/exportar-excel', [ProductoController::class, 'exportarExcel'])->name('exportar-excel');

        Route::get('/{producto}', [ProductoController::class, 'show'])->name('show');
        Route::get('/{producto}/edit', [ProductoController::class, 'edit'])->name('edit');
        Route::get('/{producto}/kardex', [ProductoController::class, 'kardex'])->name('kardex');
    });

    // GESTIÓN DE STOCK
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/ajuste', [StockController::class, 'ajuste'])->name('ajuste');
        Route::get('/transferencia', [StockController::class, 'transferencia'])->name('transferencia');
        Route::get('/inventario', [StockController::class, 'inventario'])->name('inventario');
    });

    // REPORTES
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/stock-bajo', [ReporteController::class, 'stockBajo'])->name('stock-bajo');
        Route::get('/rotacion', [ReporteController::class, 'rotacion'])->name('rotacion');
        Route::get('/valorizado', [ReporteController::class, 'valorizado'])->name('valorizado');
    });
});