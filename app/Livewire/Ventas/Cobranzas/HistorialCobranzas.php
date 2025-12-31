<?php

namespace App\Livewire\Ventas\Cobranzas;

use App\Models\Ventas\MovimientoCaja;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialCobranzas extends Component
{
    use WithPagination;

    public $buscador = '';
    public $forma_pago = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $paginado = 15;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Por defecto mostrar el mes actual
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function updatingFormaPago()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->buscador = '';
        $this->forma_pago = '';
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $cobranzas = MovimientoCaja::query()
            ->with(['factura.cliente', 'usuarioResponsable', 'aperturaCaja'])
            ->where('tipo_movimiento', 'INGRESO')
            ->whereNotNull('venta_id')
            ->when($this->buscador, function ($query) {
                $query->where(function ($q) {
                    $q->where('comprobante_numero', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('referencia', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('concepto', 'ilike', '%' . $this->buscador . '%')
                        ->orWhereHas('factura', function ($qf) {
                            $qf->where('numero_factura', 'ilike', '%' . $this->buscador . '%');
                        })
                        ->orWhereHas('factura.cliente', function ($qc) {
                            $qc->where('nombre', 'ilike', '%' . $this->buscador . '%');
                        });
                });
            })
            ->when($this->forma_pago, function ($query) {
                $query->where('forma_pago', $this->forma_pago);
            })
            ->when($this->fecha_desde, function ($query) {
                $query->whereDate('fecha_movimiento', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function ($query) {
                $query->whereDate('fecha_movimiento', '<=', $this->fecha_hasta);
            })
            ->orderBy('fecha_movimiento', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->paginado);

        // Estadísticas del período
        $stats = [
            'total_cobrado' => MovimientoCaja::where('tipo_movimiento', 'INGRESO')
                ->whereNotNull('venta_id')
                ->when($this->fecha_desde, fn($q) => $q->whereDate('fecha_movimiento', '>=', $this->fecha_desde))
                ->when($this->fecha_hasta, fn($q) => $q->whereDate('fecha_movimiento', '<=', $this->fecha_hasta))
                ->sum('monto'),
            'cantidad' => MovimientoCaja::where('tipo_movimiento', 'INGRESO')
                ->whereNotNull('venta_id')
                ->when($this->fecha_desde, fn($q) => $q->whereDate('fecha_movimiento', '>=', $this->fecha_desde))
                ->when($this->fecha_hasta, fn($q) => $q->whereDate('fecha_movimiento', '<=', $this->fecha_hasta))
                ->count(),
            'efectivo' => MovimientoCaja::where('tipo_movimiento', 'INGRESO')
                ->whereNotNull('venta_id')
                ->where('forma_pago', 'EFECTIVO')
                ->when($this->fecha_desde, fn($q) => $q->whereDate('fecha_movimiento', '>=', $this->fecha_desde))
                ->when($this->fecha_hasta, fn($q) => $q->whereDate('fecha_movimiento', '<=', $this->fecha_hasta))
                ->sum('monto'),
            'otros' => MovimientoCaja::where('tipo_movimiento', 'INGRESO')
                ->whereNotNull('venta_id')
                ->where('forma_pago', '!=', 'EFECTIVO')
                ->when($this->fecha_desde, fn($q) => $q->whereDate('fecha_movimiento', '>=', $this->fecha_desde))
                ->when($this->fecha_hasta, fn($q) => $q->whereDate('fecha_movimiento', '<=', $this->fecha_hasta))
                ->sum('monto'),
        ];

        return view('livewire.ventas.cobranzas.historial-cobranzas', compact('cobranzas', 'stats'));
    }
}
