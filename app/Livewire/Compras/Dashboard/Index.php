<?php

namespace App\Livewire\Compras\Dashboard;

use App\Models\Compras\Compra;
use App\Models\Compras\PedidoCompra;
use App\Models\Compras\Presupuesto;
use App\Models\Compras\OrdenCompra;
use Livewire\Component;

class Index extends Component
{
    // ==================== PEDIDOS DE COMPRA ====================

    public function getPedidosPendientesProperty()
    {
        return PedidoCompra::where('estado', 'PENDIENTE')->count();
    }

    public function getPedidosPendientesTotalProperty()
    {
        return PedidoCompra::where('estado', 'PENDIENTE')->sum('total_estimado') ?? 0;
    }

    public function getPedidosAprobadosProperty()
    {
        return PedidoCompra::where('estado', 'APROBADO')->count();
    }

    public function getPedidosAprobadosTotalProperty()
    {
        return PedidoCompra::where('estado', 'APROBADO')->sum('total_estimado') ?? 0;
    }

    public function getPedidosRechazadosProperty()
    {
        return PedidoCompra::where('estado', 'RECHAZADO')->count();
    }

    public function getPedidosRechazadosTotalProperty()
    {
        return PedidoCompra::where('estado', 'RECHAZADO')->sum('total_estimado') ?? 0;
    }

    // ==================== PRESUPUESTOS ====================

    public function getPresupuestosPendientesProperty()
    {
        return Presupuesto::where('estado', 'PENDIENTE')->count();
    }

    public function getPresupuestosPendientesTotalProperty()
    {
        return Presupuesto::where('estado', 'PENDIENTE')->sum('total') ?? 0;
    }

    public function getPresupuestosAprobadosProperty()
    {
        return Presupuesto::where('estado', 'APROBADO')->count();
    }

    public function getPresupuestosAprobadosTotalProperty()
    {
        return Presupuesto::where('estado', 'APROBADO')->sum('total') ?? 0;
    }

    public function getPresupuestosRechazadosProperty()
    {
        return Presupuesto::where('estado', 'RECHAZADO')->count();
    }

    public function getPresupuestosRechazadosTotalProperty()
    {
        return Presupuesto::where('estado', 'RECHAZADO')->sum('total') ?? 0;
    }

    // ==================== ÓRDENES DE COMPRA ====================

    public function getOrdenesPendientesProperty()
    {
        return OrdenCompra::where('estado', 'PENDIENTE')->count();
    }

    public function getOrdenesPendientesTotalProperty()
    {
        return OrdenCompra::where('estado', 'PENDIENTE')->sum('total') ?? 0;
    }

    public function getOrdenesAprobadosProperty()
    {
        return OrdenCompra::where('estado', 'APROBADO')->count();
    }

    public function getOrdenesAprobadosTotalProperty()
    {
        return OrdenCompra::where('estado', 'APROBADO')->sum('total') ?? 0;
    }

    public function getOrdenesRechazadosProperty()
    {
        return OrdenCompra::where('estado', 'RECHAZADO')->count();
    }

    public function getOrdenesRechazadosTotalProperty()
    {
        return OrdenCompra::where('estado', 'RECHAZADO')->sum('total') ?? 0;
    }

    // ==================== COMPRAS/FACTURAS ====================

    public function getComprasPendientesProperty()
    {
        return Compra::where('estado', 'PENDIENTE')->count();
    }

    public function getComprasPendientesTotalProperty()
    {
        return Compra::where('estado', 'PENDIENTE')->sum('total') ?? 0;
    }

    public function getComprasAprobadosProperty()
    {
        return Compra::where('estado', 'APROBADO')->count();
    }

    public function getComprasAprobadosTotalProperty()
    {
        return Compra::where('estado', 'APROBADO')->sum('total') ?? 0;
    }

    public function getComprasRechazadosProperty()
    {
        return Compra::where('estado', 'RECHAZADO')->count();
    }

    public function getComprasRechazadosTotalProperty()
    {
        return Compra::where('estado', 'RECHAZADO')->sum('total') ?? 0;
    }

    // ==================== RENDER ====================

    public function render()
    {
        return view('livewire.compras.dashboard.index', [
            'pedidos' => [
                'pendientes' => $this->pedidosPendientes,
                'pendientes_total' => $this->pedidosPendientesTotal,
                'aprobados' => $this->pedidosAprobados,
                'aprobados_total' => $this->pedidosAprobadosTotal,
                'rechazados' => $this->pedidosRechazados,
                'rechazados_total' => $this->pedidosRechazadosTotal,
            ],
            'presupuestos' => [
                'pendientes' => $this->presupuestosPendientes,
                'pendientes_total' => $this->presupuestosPendientesTotal,
                'aprobados' => $this->presupuestosAprobados,
                'aprobados_total' => $this->presupuestosAprobadosTotal,
                'rechazados' => $this->presupuestosRechazados,
                'rechazados_total' => $this->presupuestosRechazadosTotal,
            ],
            'ordenes' => [
                'pendientes' => $this->ordenesPendientes,
                'pendientes_total' => $this->ordenesPendientesTotal,
                'aprobados' => $this->ordenesAprobados,
                'aprobados_total' => $this->ordenesAprobadosTotal,
                'rechazados' => $this->ordenesRechazados,
                'rechazados_total' => $this->ordenesRechazadosTotal,
            ],
            'compras' => [
                'pendientes' => $this->comprasPendientes,
                'pendientes_total' => $this->comprasPendientesTotal,
                'aprobados' => $this->comprasAprobados,
                'aprobados_total' => $this->comprasAprobadosTotal,
                'rechazados' => $this->comprasRechazados,
                'rechazados_total' => $this->comprasRechazadosTotal,
            ],
        ]);
    }
}
