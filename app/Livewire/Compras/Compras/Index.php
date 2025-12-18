<?php

namespace App\Livewire\Compras\Compras;

use App\Models\Compras\Compra;
use App\Models\Compras\Proveedor;
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
        $compra = Compra::findOrFail($compraId);
        
        // Validaciones según el estado actual
        if ($nuevoEstado === 'ANULADA' && $compra->recepcion_completa) {
            session()->flash('error', 'No se puede anular una compra con recepción completa.');
            return;
        }

        if ($nuevoEstado === 'APROBADO' && !auth()->user()->can('compras.compras.aprobar')) {
            session()->flash('error', 'No tiene permiso para aprobar compras.');
            return;
        }

        $compra->update([
            'estado' => $nuevoEstado,
            'actualizadoPor' => auth()->id(),
        ]);

        session()->flash('success', "Compra {$nuevoEstado} correctamente.");
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
