<?php

namespace App\Livewire\Compras\Compras;

use App\Models\Compras\Compra;
use App\Models\Compras\CuentaPorPagar;
use App\Models\Compras\Proveedor;
use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;
use App\Models\Stock\Precio;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // ==================== PROPIEDADES DE BÚSQUEDA ====================
    public $buscador = '';
    public $proveedor_id = '';
    public $estado = '';
    public $tipo_factura = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $paginado = 10;

    // ==================== RESETEAR PAGINACIÓN ====================
    public function updating($propertyName): void
    {
        if (in_array($propertyName, [
            'buscador', 'proveedor_id', 'estado', 'tipo_factura', 
            'fecha_desde', 'fecha_hasta', 'paginado'
        ])) {
            $this->resetPage();
        }
    }

    // ==================== ACCIONES ====================

    public function cambiarEstado($compraId, $nuevoEstado)
    {
        $compra = Compra::with('detalles')->findOrFail($compraId);

        // Validaciones según el estado actual
        if ($nuevoEstado === 'ANULADA' && $compra->recepcion_completa) {
            session()->flash('error', 'No se puede anular una compra con recepción completa.');
            return;
        }

        if ($nuevoEstado === 'APROBADO' && !auth()->user()->can('compras.compras.aprobar')) {
            session()->flash('error', 'No tiene permiso para aprobar compras.');
            return;
        }

        try {
            DB::beginTransaction();

            $compra->update([
                'estado' => $nuevoEstado,
                'actualizadoPor' => auth()->id(),
            ]);

            // Si se aprueba la compra, procesar stock y cuentas por pagar
            if ($nuevoEstado === 'APROBADO') {
                $this->crearCuentaPorPagar($compra);
                $this->actualizarStockYPrecios($compra);
            }

            DB::commit();
            session()->flash('success', "Compra {$nuevoEstado} correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Crea un registro en cuentas por pagar cuando se aprueba una compra
     */
    private function crearCuentaPorPagar(Compra $compra)
    {
        // Verificar si ya existe una cuenta por pagar para esta compra
        $cuentaExistente = CuentaPorPagar::where('compra_id', $compra->id)->first();

        if ($cuentaExistente) {
            // Si ya existe, solo actualizar los datos
            $cuentaExistente->update([
                'numero_documento' => $compra->numero_factura,
                'timbrado' => $compra->timbrado,
                'tipo' => $compra->tipo_factura,
                'fecha_emision' => $compra->fecha_emision,
                'fecha_vencimiento' => $compra->fecha_vencimiento,
                'condicion_pago' => $compra->condicion_pago,
                'monto_total' => $compra->total,
                'saldo_pendiente' => $compra->total - $cuentaExistente->monto_pagado,
                'estado' => 'PENDIENTE',
                'actualizadoPor' => auth()->id(),
            ]);
        } else {
            // Crear nueva cuenta por pagar
            // Si es CONTADO y no tiene fecha_vencimiento, usar la fecha de emisión
            $fechaVencimiento = $compra->fecha_vencimiento ?? $compra->fecha_emision;

            CuentaPorPagar::create([
                'compra_id' => $compra->id,
                'proveedor_id' => $compra->proveedor_id,
                'numero_documento' => $compra->numero_factura,
                'timbrado' => $compra->timbrado,
                'tipo' => $compra->tipo_factura,
                'fecha_emision' => $compra->fecha_emision,
                'fecha_vencimiento' => $fechaVencimiento,
                'condicion_pago' => $compra->condicion_pago,
                'monto_total' => $compra->total,
                'monto_pagado' => 0,
                'saldo_pendiente' => $compra->total,
                'estado' => 'PENDIENTE',
                'observaciones' => 'Cuenta por pagar generada automáticamente al aprobar la compra',
                'creadoPor' => auth()->id(),
            ]);
        }
    }

    /**
     * Actualiza el stock y los precios de los productos al aprobar la compra
     */
    private function actualizarStockYPrecios(Compra $compra)
    {
        foreach ($compra->detalles as $detalle) {
            // Obtener o crear el registro de stock
            $stock = Stock::firstOrCreate(
                [
                    'producto_id' => $detalle->producto_id,
                    'deposito_id' => $compra->deposito_id ?? 1, // Depósito por defecto
                ],
                [
                    'stock_actual' => 0,
                    'stock_minimo' => 0,
                    'stock_maximo' => 0,
                    'creadoPor' => auth()->id(),
                ]
            );

            $stockAnterior = $stock->stock_actual;
            $nuevoStock = $stockAnterior + $detalle->cantidad;

            // Actualizar stock
            $stock->update([
                'stock_actual' => $nuevoStock,
                'actualizadoPor' => auth()->id(),
            ]);

            // Registrar movimiento en kardex
            MovimientoStock::create([
                'producto_id' => $detalle->producto_id,
                'deposito_id' => $compra->deposito_id ?? 1,
                'usuario_id' => auth()->id(),
                'tipo' => 'ENTRADA_COMPRA',
                'cantidad' => $detalle->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $nuevoStock,
                'costo_unitario' => $detalle->precio_unitario,
                'costo_total' => $detalle->precio_unitario * $detalle->cantidad,
                'documento_tipo' => 'COMPRA',
                'documento_id' => $compra->id,
                'motivo' => "Entrada por compra #{$compra->numero_factura}",
                'fecha_movimiento' => now(),
            ]);

            // Actualizar o crear precio actual
            $precioActual = Precio::where('producto_id', $detalle->producto_id)
                                  ->where('es_actual', true)
                                  ->first();

            if ($precioActual) {
                // Desactivar el precio anterior
                $precioActual->update(['es_actual' => false]);
            }

            // Convertir IVA porcentaje a formato válido para la restricción CHECK
            $iva = match((int)($detalle->iva_porcentaje ?? 10)) {
                10 => '10',
                5 => '5',
                0 => 'exenta',
                default => '10',
            };

            // Crear nuevo precio actual con el costo de compra
            Precio::create([
                'producto_id' => $detalle->producto_id,
                'precio_compra' => $detalle->precio_unitario,
                'precio_venta' => $precioActual ? $precioActual->precio_venta : ($detalle->precio_unitario * 1.3), // 30% de margen por defecto
                'margen_porcentaje' => $precioActual ? $precioActual->margen_porcentaje : 30,
                'iva' => $iva,
                'moneda' => 'PYG',
                'tipo_cambio' => 1,
                'es_actual' => true,
                'creadoPor' => auth()->id(),
            ]);
        }
    }

    public function duplicarCompra($compraId)
    {
        $compraOriginal = Compra::with('detalles')->findOrFail($compraId);
        
        // Crear nueva compra como pendiente
        $nuevaCompra = $compraOriginal->replicate();
        $nuevaCompra->estado = 'PENDIENTE';
        $nuevaCompra->numero_factura = $this->generarNumeroFactura();
        $nuevaCompra->creadoPor = auth()->id();
        $nuevaCompra->save();

        // Duplicar detalles
        foreach ($compraOriginal->detalles as $detalle) {
            $nuevoDetalle = $detalle->replicate();
            $nuevoDetalle->compra_id = $nuevaCompra->id;
            $nuevoDetalle->creadoPor = auth()->id();
            $nuevoDetalle->save();
        }

        session()->flash('success', 'Compra duplicada correctamente. Redirigiendo...');
        return redirect()->route('compras.compras.edit', $nuevaCompra);
    }

    private function generarNumeroFactura(): string
    {
        $ultimaCompra = Compra::where('numero_factura', 'like', 'DUP-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaCompra) {
            $numero = (int) str_replace('DUP-', '', $ultimaCompra->numero_factura);
            return 'DUP-' . str_pad($numero + 1, 6, '0', STR_PAD_LEFT);
        }

        return 'DUP-000001';
    }

    // ==================== EXPORTACIONES ====================

    public function exportarExcel()
    {
        $compras = $this->getComprasQuery()->get();
        
        // Usar tu exportador existente o crear uno nuevo
        return Excel::download(new ComprasExport($compras), 'compras_' . date('Ymd_His') . '.xlsx');
    }

    public function exportarPDF()
    {
        $compras = $this->getComprasQuery()->get();
        
        $pdf = PDF::loadView('exports.compras.pdf', [
            'compras' => $compras,
            'filtros' => $this->getFiltrosAplicados(),
        ]);
        
        return $pdf->download('compras_' . date('Ymd_His') . '.pdf');
    }

    // ==================== MÉTODOS AUXILIARES ====================

    private function getComprasQuery()
    {
        return Compra::query()
            ->buscador($this->buscador)
            ->when($this->proveedor_id, function ($query) {
                $query->where('proveedor_id', $this->proveedor_id);
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->when($this->tipo_factura, function ($query) {
                $query->where('tipo_factura', $this->tipo_factura);
            })
            ->when($this->fecha_desde, function ($query) {
                $query->whereDate('fecha_emision', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function ($query) {
                $query->whereDate('fecha_emision', '<=', $this->fecha_hasta);
            })
            ->with(['proveedor', 'sucursal', 'timbrado'])
            ->orderBy('fecha_emision', 'desc')
            ->orderBy('id', 'desc');
    }

    private function getFiltrosAplicados(): array
    {
        $filtros = [];

        if ($this->proveedor_id) {
            $proveedor = Proveedor::find($this->proveedor_id);
            $filtros['Proveedor'] = $proveedor->razon_social ?? 'N/A';
        }

        if ($this->estado) {
            $filtros['Estado'] = Compra::ESTADOS[$this->estado] ?? $this->estado;
        }

        if ($this->tipo_factura) {
            $filtros['Tipo Factura'] = Compra::TIPOS_FACTURA[$this->tipo_factura] ?? $this->tipo_factura;
        }

        if ($this->fecha_desde) {
            $filtros['Desde'] = $this->fecha_desde;
        }

        if ($this->fecha_hasta) {
            $filtros['Hasta'] = $this->fecha_hasta;
        }

        return $filtros;
    }

    // ==================== RENDER ====================

    public function render()
    {
        return view('livewire.compras.compras.index', [
            'compras' => $this->getComprasQuery()->paginate($this->paginado),
            'proveedores' => Proveedor::where('activo', true)
                ->orderBy('razon_social')
                ->get(),
            'totalCompras' => $this->getComprasQuery()->count(),
            'totalMonto' => $this->getComprasQuery()->sum('total'),
            'filtrosAplicados' => $this->getFiltrosAplicados(),
        ]);
    }
}
