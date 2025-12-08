<?php

namespace App\Livewire\Stock\Productos;

use App\Models\Stock\Producto;
use App\Models\Stock\MovimientoStock;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Kardex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $productoId;
    public $producto;

    // Filtros
    public $fechaDesde;
    public $fechaHasta;
    public $depositoId = '';
    public $tipoMovimiento = '';

    public function mount(Producto $producto)
    {
        $this->productoId = $producto->id;
        $this->producto = $producto->load(['unidadMedida']);

        // Establecer fechas por defecto (último mes)
        $this->fechaHasta = now()->format('Y-m-d');
        $this->fechaDesde = now()->subMonth()->format('Y-m-d');
    }

    public function aplicarFiltros()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->fechaDesde = now()->subMonth()->format('Y-m-d');
        $this->fechaHasta = now()->format('Y-m-d');
        $this->depositoId = '';
        $this->tipoMovimiento = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = MovimientoStock::where('producto_id', $this->productoId)
                                ->with(['deposito', 'usuario']);

        // Aplicar filtros
        if ($this->fechaDesde) {
            $query->whereDate('fecha_movimiento', '>=', $this->fechaDesde);
        }

        if ($this->fechaHasta) {
            $query->whereDate('fecha_movimiento', '<=', $this->fechaHasta);
        }

        if ($this->depositoId) {
            $query->where('deposito_id', $this->depositoId);
        }

        if ($this->tipoMovimiento) {
            $query->where('tipo', $this->tipoMovimiento);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')
                             ->orderBy('id', 'desc')
                             ->paginate(20);

        // Calcular totales
        $totales = $this->calcularTotales();

        // Obtener lista de depósitos (desde empresa cuando esté disponible)
        $depositos = collect(); // Por ahora vacío hasta que se implemente empresa.DEPOSITOS

        return view('livewire.stock.productos.kardex', [
            'movimientos' => $movimientos,
            'depositos' => $depositos,
            'totales' => $totales,
        ]);
    }

    private function calcularTotales()
    {
        $query = MovimientoStock::where('producto_id', $this->productoId);

        // Aplicar los mismos filtros
        if ($this->fechaDesde) {
            $query->whereDate('fecha_movimiento', '>=', $this->fechaDesde);
        }

        if ($this->fechaHasta) {
            $query->whereDate('fecha_movimiento', '<=', $this->fechaHasta);
        }

        if ($this->depositoId) {
            $query->where('deposito_id', $this->depositoId);
        }

        if ($this->tipoMovimiento) {
            $query->where('tipo', $this->tipoMovimiento);
        }

        $movimientos = $query->get();

        $totalEntradas = $movimientos->filter->es_entrada->sum('cantidad');
        $totalSalidas = $movimientos->filter->es_salida->sum('cantidad');
        $saldoNeto = $totalEntradas - $totalSalidas;

        return [
            'entradas' => $totalEntradas,
            'salidas' => $totalSalidas,
            'saldo' => $saldoNeto,
        ];
    }
}
