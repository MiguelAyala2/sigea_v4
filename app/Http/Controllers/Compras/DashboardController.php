<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\PedidoCompra;
use App\Models\Compras\Presupuesto;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\Compra;
use App\Models\Empresa\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardComprasExport;

class DashboardController extends Controller
{
    public function exportarPDF()
    {
        $datos = $this->obtenerDatos();
        $empresa = Empresa::first();

        $pdf = PDF::loadView('compras.dashboard.pdf', [
            'datos' => $datos,
            'empresa' => $empresa,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('dashboard-compras-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportarExcel()
    {
        return Excel::download(
            new DashboardComprasExport($this->obtenerDatos()),
            'dashboard-compras-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function obtenerDatos()
    {
        return [
            'pedidos' => [
                'pendientes' => [
                    'cantidad' => PedidoCompra::where('estado', 'PENDIENTE')->count(),
                    'total' => PedidoCompra::where('estado', 'PENDIENTE')->sum('total_estimado') ?? 0,
                ],
                'aprobados' => [
                    'cantidad' => PedidoCompra::where('estado', 'APROBADO')->count(),
                    'total' => PedidoCompra::where('estado', 'APROBADO')->sum('total_estimado') ?? 0,
                ],
                'rechazados' => [
                    'cantidad' => PedidoCompra::where('estado', 'RECHAZADO')->count(),
                    'total' => PedidoCompra::where('estado', 'RECHAZADO')->sum('total_estimado') ?? 0,
                ],
            ],
            'presupuestos' => [
                'pendientes' => [
                    'cantidad' => Presupuesto::where('estado', 'PENDIENTE')->count(),
                    'total' => Presupuesto::where('estado', 'PENDIENTE')->sum('total') ?? 0,
                ],
                'aprobados' => [
                    'cantidad' => Presupuesto::where('estado', 'APROBADO')->count(),
                    'total' => Presupuesto::where('estado', 'APROBADO')->sum('total') ?? 0,
                ],
                'rechazados' => [
                    'cantidad' => Presupuesto::where('estado', 'RECHAZADO')->count(),
                    'total' => Presupuesto::where('estado', 'RECHAZADO')->sum('total') ?? 0,
                ],
            ],
            'ordenes' => [
                'pendientes' => [
                    'cantidad' => OrdenCompra::where('estado', 'PENDIENTE')->count(),
                    'total' => OrdenCompra::where('estado', 'PENDIENTE')->sum('total') ?? 0,
                ],
                'aprobados' => [
                    'cantidad' => OrdenCompra::where('estado', 'APROBADO')->count(),
                    'total' => OrdenCompra::where('estado', 'APROBADO')->sum('total') ?? 0,
                ],
                'rechazados' => [
                    'cantidad' => OrdenCompra::where('estado', 'RECHAZADO')->count(),
                    'total' => OrdenCompra::where('estado', 'RECHAZADO')->sum('total') ?? 0,
                ],
            ],
            'compras' => [
                'pendientes' => [
                    'cantidad' => Compra::where('estado', 'PENDIENTE')->count(),
                    'total' => Compra::where('estado', 'PENDIENTE')->sum('total') ?? 0,
                ],
                'aprobados' => [
                    'cantidad' => Compra::where('estado', 'APROBADO')->count(),
                    'total' => Compra::where('estado', 'APROBADO')->sum('total') ?? 0,
                ],
                'rechazados' => [
                    'cantidad' => Compra::where('estado', 'RECHAZADO')->count(),
                    'total' => Compra::where('estado', 'RECHAZADO')->sum('total') ?? 0,
                ],
            ],
        ];
    }
}
