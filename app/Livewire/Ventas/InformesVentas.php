<?php

namespace App\Livewire\Ventas;

use App\Models\Ventas\Factura;
use App\Models\Ventas\CuentaPorCobrar;
use App\Models\Ventas\MovimientoCaja;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InformesVentas extends Component
{
    public $fecha_desde;
    public $fecha_hasta;
    public $tipo_reporte = 'ventas_generales';
    public $periodo = 'mes_actual';

    // Datos del dashboard
    public $ventas_mes = 0;
    public $total_cobrado = 0;
    public $pendiente_cobro = 0;
    public $facturas_emitidas = 0;

    // Datos para gráficos
    public $ventas_por_mes = [];
    public $cobranzas_por_forma_pago = [];
    public $productos_mas_vendidos = [];
    public $ventas_por_cliente = [];

    public function mount()
    {
        $this->setPeriodo('mes_actual');
        $this->cargarDatos();
    }

    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;

        switch ($periodo) {
            case 'hoy':
                $this->fecha_desde = Carbon::today()->format('Y-m-d');
                $this->fecha_hasta = Carbon::today()->format('Y-m-d');
                break;
            case 'semana_actual':
                $this->fecha_desde = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->fecha_hasta = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'mes_actual':
                $this->fecha_desde = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->fecha_hasta = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'trimestre_actual':
                $this->fecha_desde = Carbon::now()->startOfQuarter()->format('Y-m-d');
                $this->fecha_hasta = Carbon::now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'anio_actual':
                $this->fecha_desde = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->fecha_hasta = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
        }
    }

    public function actualizarPeriodo()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $this->cargarResumen();
        $this->cargarVentasPorMes();
        $this->cargarCobranzasPorFormaPago();
        $this->cargarProductosMasVendidos();
        $this->cargarVentasPorCliente();
    }

    protected function cargarResumen()
    {
        // Ventas del período
        $this->ventas_mes = Factura::whereBetween('fecha_emision', [$this->fecha_desde, $this->fecha_hasta])
            ->where('estado', '!=', 'ANULADA')
            ->sum('total');

        // Total cobrado (movimientos de caja por ventas)
        $this->total_cobrado = MovimientoCaja::whereBetween('fecha_movimiento', [$this->fecha_desde, $this->fecha_hasta])
            ->where('tipo_movimiento', 'INGRESO')
            ->whereNotNull('venta_id')
            ->sum('monto');

        // Pendiente de cobro (cuentas por cobrar)
        $this->pendiente_cobro = CuentaPorCobrar::where('estado', '!=', 'COBRADA')
            ->where('estado', '!=', 'ANULADA')
            ->sum('saldo_pendiente');

        // Facturas emitidas
        $this->facturas_emitidas = Factura::whereBetween('fecha_emision', [$this->fecha_desde, $this->fecha_hasta])
            ->where('estado', '!=', 'ANULADA')
            ->count();
    }

    protected function cargarVentasPorMes()
    {
        // Obtener ventas de los últimos 12 meses
        $ventasMensuales = Factura::select(
            DB::raw("TO_CHAR(fecha_emision, 'YYYY-MM') as mes"),
            DB::raw("SUM(total) as total")
        )
            ->where('estado', '!=', 'ANULADA')
            ->where('fecha_emision', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw("TO_CHAR(fecha_emision, 'YYYY-MM')"))
            ->orderBy('mes')
            ->get();

        $this->ventas_por_mes = $ventasMensuales->map(function ($item) {
            return [
                'mes' => Carbon::createFromFormat('Y-m', $item->mes)->format('M Y'),
                'total' => $item->total,
            ];
        })->toArray();
    }

    protected function cargarCobranzasPorFormaPago()
    {
        $cobranzas = MovimientoCaja::select('forma_pago', DB::raw('SUM(monto) as total'))
            ->whereBetween('fecha_movimiento', [$this->fecha_desde, $this->fecha_hasta])
            ->where('tipo_movimiento', 'INGRESO')
            ->whereNotNull('venta_id')
            ->groupBy('forma_pago')
            ->get();

        $this->cobranzas_por_forma_pago = $cobranzas->map(function ($item) {
            return [
                'forma_pago' => $this->traducirFormaPago($item->forma_pago),
                'total' => $item->total,
            ];
        })->toArray();
    }

    protected function cargarProductosMasVendidos()
    {
        $productos = DB::table('ventas.FACTURAS_DETALLE as fd')
            ->join('ventas.FACTURAS as f', 'fd.factura_id', '=', 'f.id')
            ->join('stock.PRODUCTOS as p', 'fd.producto_id', '=', 'p.id')
            ->select(
                'p.nombre',
                DB::raw('SUM(fd.cantidad) as cantidad_total'),
                DB::raw('SUM(fd.subtotal) as monto_total')
            )
            ->whereBetween('f.fecha_emision', [$this->fecha_desde, $this->fecha_hasta])
            ->where('f.estado', '!=', 'ANULADA')
            ->groupBy('p.id', 'p.nombre')
            ->orderBy('cantidad_total', 'desc')
            ->limit(10)
            ->get();

        $this->productos_mas_vendidos = $productos->toArray();
    }

    protected function cargarVentasPorCliente()
    {
        $clientes = Factura::select(
            'cliente_id',
            DB::raw('COUNT(*) as cantidad_facturas'),
            DB::raw('SUM(total) as total_ventas')
        )
            ->with('cliente:id,nombre')
            ->whereBetween('fecha_emision', [$this->fecha_desde, $this->fecha_hasta])
            ->where('estado', '!=', 'ANULADA')
            ->groupBy('cliente_id')
            ->orderBy('total_ventas', 'desc')
            ->limit(10)
            ->get();

        $this->ventas_por_cliente = $clientes->map(function ($item) {
            return [
                'cliente' => $item->cliente->nombre ?? 'Sin nombre',
                'cantidad_facturas' => $item->cantidad_facturas,
                'total_ventas' => $item->total_ventas,
            ];
        })->toArray();
    }

    protected function traducirFormaPago($formaPago)
    {
        return match ($formaPago) {
            'EFECTIVO' => 'Efectivo',
            'CHEQUE' => 'Cheque',
            'TRANSFERENCIA' => 'Transferencia',
            'TARJETA_CREDITO' => 'Tarjeta de Crédito',
            'TARJETA_DEBITO' => 'Tarjeta de Débito',
            default => $formaPago,
        };
    }

    public function exportarPDF()
    {
        // TODO: Implementar exportación a PDF
        session()->flash('info', 'Exportación a PDF en desarrollo');
    }

    public function exportarExcel()
    {
        // TODO: Implementar exportación a Excel
        session()->flash('info', 'Exportación a Excel en desarrollo');
    }

    public function render()
    {
        return view('livewire.ventas.informes-ventas');
    }
}
