<?php

namespace App\Livewire\Compras;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Compras\CuentaPorPagar;

class PagoLista extends Component
{
    use WithPagination;

    public $search = '';
    public $proveedor_filter = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->proveedor_filter = '';
        $this->fecha_desde = '';
        $this->fecha_hasta = '';
        $this->resetPage();
    }

    public function render()
    {
        // Obtener todas las cuentas por pagar (incluye facturas y notas de crédito)
        $cuentas = CuentaPorPagar::with(['proveedor', 'compra', 'creador'])
            ->whereIn('estado', ['PENDIENTE', 'PARCIALMENTE_PAGADO', 'APLICADA'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('numero_documento', 'ILIKE', '%' . $this->search . '%')
                      ->orWhereHas('proveedor', function($prov) {
                          $prov->where('razon_social', 'ILIKE', '%' . $this->search . '%')
                               ->orWhere('ruc', 'ILIKE', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->fecha_desde, function($query) {
                $query->whereDate('fecha_emision', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function($query) {
                $query->whereDate('fecha_emision', '<=', $this->fecha_hasta);
            })
            ->orderBy('fecha_emision', 'desc')
            ->paginate(15);

        // Calcular el total neto (facturas positivas - notas de crédito negativas)
        $totalGeneral = CuentaPorPagar::whereIn('estado', ['PENDIENTE', 'PARCIALMENTE_PAGADO', 'APLICADA'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('numero_documento', 'ILIKE', '%' . $this->search . '%')
                      ->orWhereHas('proveedor', function($prov) {
                          $prov->where('razon_social', 'ILIKE', '%' . $this->search . '%')
                               ->orWhere('ruc', 'ILIKE', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->fecha_desde, function($query) {
                $query->whereDate('fecha_emision', '>=', $this->fecha_desde);
            })
            ->when($this->fecha_hasta, function($query) {
                $query->whereDate('fecha_emision', '<=', $this->fecha_hasta);
            })
            ->sum('saldo_pendiente');

        return view('livewire.compras.pago-lista', [
            'cuentas' => $cuentas,
            'totalGeneral' => $totalGeneral,
        ]);
    }
}
