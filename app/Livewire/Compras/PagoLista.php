<?php

namespace App\Livewire\Compras;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Compras\Compra;

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
        // Obtener solo las compras APROBADAS
        $compras = Compra::with(['proveedor', 'creadoPorUsuario'])
            ->where('estado', 'APROBADO')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('numero_factura', 'ILIKE', '%' . $this->search . '%')
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

        // Calcular el total de todas las compras aprobadas (sin paginación para el total general)
        $totalGeneral = Compra::where('estado', 'APROBADO')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('numero_factura', 'ILIKE', '%' . $this->search . '%')
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
            ->sum('total');

        return view('livewire.compras.pago-lista', [
            'compras' => $compras,
            'totalGeneral' => $totalGeneral,
        ]);
    }
}
