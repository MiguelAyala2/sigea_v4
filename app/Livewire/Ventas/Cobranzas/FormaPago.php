<?php

namespace App\Livewire\Ventas\Cobranzas;

use App\Models\Ventas\MovimientoCaja;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FormaPago extends Component
{
    public $fecha_desde;
    public $fecha_hasta;
    public $periodo = 'mes_actual';

    public function mount()
    {
        $this->setPeriodo('mes_actual');
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
        // Método para forzar actualización cuando se cambian fechas manualmente
    }

    public function render()
    {
        // Obtener cobranzas por forma de pago
        $cobranzasPorFormaPago = MovimientoCaja::select('forma_pago', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(monto) as total'))
            ->where('tipo_movimiento', 'INGRESO')
            ->whereNotNull('venta_id')
            ->whereBetween('fecha_movimiento', [$this->fecha_desde, $this->fecha_hasta])
            ->groupBy('forma_pago')
            ->get();

        // Calcular totales
        $totalGeneral = $cobranzasPorFormaPago->sum('total');
        $cantidadTotal = $cobranzasPorFormaPago->sum('cantidad');

        // Agregar porcentajes
        $cobranzasPorFormaPago = $cobranzasPorFormaPago->map(function ($item) use ($totalGeneral) {
            $item->porcentaje = $totalGeneral > 0 ? ($item->total / $totalGeneral) * 100 : 0;
            $item->forma_pago_texto = MovimientoCaja::FORMAS_PAGO[$item->forma_pago] ?? $item->forma_pago;
            return $item;
        })->sortByDesc('total');

        // Estadísticas adicionales
        $stats = [
            'total_cobrado' => $totalGeneral,
            'cantidad_cobranzas' => $cantidadTotal,
            'promedio_cobranza' => $cantidadTotal > 0 ? $totalGeneral / $cantidadTotal : 0,
            'forma_mas_usada' => $cobranzasPorFormaPago->first()->forma_pago_texto ?? 'N/A',
        ];

        return view('livewire.ventas.cobranzas.forma-pago', compact('cobranzasPorFormaPago', 'totalGeneral', 'cantidadTotal', 'stats'));
    }
}
