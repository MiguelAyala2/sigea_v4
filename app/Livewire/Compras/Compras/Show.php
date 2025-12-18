<?php

namespace App\Livewire\Compras\Compras;

use App\Models\Compras\Compra;
use Livewire\Component;

class Show extends Component
{
    public $compra;
    public $activeTab = 'detalles';

    public function mount(Compra $compra)
    {
        $this->compra = $compra->load([
            'proveedor',
            'sucursal',
            'deposito',
            'timbrado',
            'detalles.producto.unidadMedida',
            'detalles.producto.marca',
            'recepciones.deposito',
            'recepciones.receptor',
            'aprobaciones.aprobador',
            'creadoPorUsuario',
            'actualizadoPorUsuario',
        ]);
    }

    public function cambiarTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getTabsProperty()
    {
        return [
            'detalles' => [
                'icon' => 'fas fa-info-circle',
                'label' => 'Detalles',
                'badge' => null,
            ],
            'items' => [
                'icon' => 'fas fa-boxes',
                'label' => 'Items',
                'badge' => $this->compra->detalles->count(),
            ],
            'recepciones' => [
                'icon' => 'fas fa-box-open',
                'label' => 'Recepciones',
                'badge' => $this->compra->recepciones->count(),
            ],
            'aprobaciones' => [
                'icon' => 'fas fa-check-double',
                'label' => 'Aprobaciones',
                'badge' => $this->compra->aprobaciones->count(),
            ],
            'trazabilidad' => [
                'icon' => 'fas fa-project-diagram',
                'label' => 'Trazabilidad',
                'badge' => null,
            ],
            'auditoria' => [
                'icon' => 'fas fa-history',
                'label' => 'Auditoría',
                'badge' => null,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.compras.compras.show', [
            'tabs' => $this->tabs,
            'totalDetalles' => $this->compra->detalles->sum('total'),
            'iva10' => $this->compra->detalles->where('iva_porcentaje', 10)->sum('iva_monto'),
            'iva5' => $this->compra->detalles->where('iva_porcentaje', 5)->sum('iva_monto'),
            'exenta' => $this->compra->detalles->where('iva_porcentaje', 0)->sum('subtotal'),
        ]);
    }
}
