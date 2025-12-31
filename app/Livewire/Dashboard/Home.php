<?php

namespace App\Livewire\Dashboard;

use App\Models\Ventas\Factura;
use App\Models\Ventas\CuentaPorCobrar;
use App\Models\Compras\Compra;
use App\Models\Compras\CuentaPorPagar;
use App\Models\Servicios\OrdenServicio;
use App\Models\Servicios\SolicitudServicio;
use App\Models\Stock\Producto;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Home extends Component
{
    public function render()
    {
        // Estadísticas de Ventas
        $ventasHoy = Factura::whereDate('fecha_emision', now())->sum('total') ?? 0;
        $ventasMes = Factura::whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->sum('total') ?? 0;

        $cuentasPorCobrar = CuentaPorCobrar::where('saldo_pendiente', '>', 0)->sum('saldo_pendiente') ?? 0;
        $facturasPendientes = CuentaPorCobrar::where('saldo_pendiente', '>', 0)->count();

        // Estadísticas de Compras
        $comprasMes = Compra::whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->sum('total') ?? 0;

        $cuentasPorPagar = CuentaPorPagar::where('saldo_pendiente', '>', 0)->sum('saldo_pendiente') ?? 0;

        // Estadísticas de Servicios
        $serviciosPendientes = OrdenServicio::where('estado', 'pendiente')->count();
        $serviciosEnProceso = OrdenServicio::where('estado', 'en_proceso')->count();
        $serviciosFinalizados = OrdenServicio::where('estado', 'finalizada')->count();
        $solicitudesPendientes = SolicitudServicio::where('estado', 'pendiente')->count();

        // Estadísticas de Stock
        $productosStockBajo = DB::table('stock.STOCK as s')
            ->join('stock.PRODUCTOS as p', 's.producto_id', '=', 'p.id')
            ->whereRaw('s.stock_actual <= s.stock_minimo')
            ->where('p.activo', true)
            ->whereNull('p.deleted_at')
            ->whereNull('s.deleted_at')
            ->distinct('p.id')
            ->count();

        // Ventas por mes (últimos 6 meses)
        $ventasPorMes = Factura::select(
                DB::raw('EXTRACT(MONTH FROM fecha_emision) as mes'),
                DB::raw('SUM(total) as total')
            )
            ->where('fecha_emision', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM fecha_emision)'))
            ->orderBy('mes')
            ->get();

        // Últimas ventas
        $ultimasVentas = Factura::with(['cliente'])
            ->orderBy('fecha_emision', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Productos más vendidos (últimos 30 días)
        $productosMasVendidos = DB::table('ventas.FACTURAS_DETALLE as fd')
            ->join('ventas.FACTURAS as f', 'fd.factura_id', '=', 'f.id')
            ->join('stock.PRODUCTOS as p', 'fd.producto_id', '=', 'p.id')
            ->select(
                'p.nombre',
                DB::raw('SUM(fd.cantidad) as total_vendido'),
                DB::raw('SUM(fd.subtotal) as total_monto')
            )
            ->where('f.fecha_emision', '>=', now()->subDays(30))
            ->whereNull('f.deleted_at')
            ->whereNull('p.deleted_at')
            ->groupBy('p.id', 'p.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        return view('livewire.dashboard.home', [
            'ventasHoy' => $ventasHoy,
            'ventasMes' => $ventasMes,
            'cuentasPorCobrar' => $cuentasPorCobrar,
            'facturasPendientes' => $facturasPendientes,
            'comprasMes' => $comprasMes,
            'cuentasPorPagar' => $cuentasPorPagar,
            'serviciosPendientes' => $serviciosPendientes,
            'serviciosEnProceso' => $serviciosEnProceso,
            'serviciosFinalizados' => $serviciosFinalizados,
            'solicitudesPendientes' => $solicitudesPendientes,
            'productosStockBajo' => $productosStockBajo,
            'ventasPorMes' => $ventasPorMes,
            'ultimasVentas' => $ultimasVentas,
            'productosMasVendidos' => $productosMasVendidos,
        ]);
    }
}
