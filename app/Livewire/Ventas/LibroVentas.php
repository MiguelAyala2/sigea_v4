<?php

namespace App\Livewire\Ventas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ventas\Factura;
use App\Models\Empresa\Timbrado;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LibroVentas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtros
    public $mes;
    public $anio;
    public $timbrado_id = '';
    public $estado_factura = '';

    // Totales
    public $total_gravada_10 = 0;
    public $total_gravada_5 = 0;
    public $total_exenta = 0;
    public $total_general = 0;
    public $total_iva_10 = 0;
    public $total_iva_5 = 0;
    public $total_iva = 0;

    public function mount()
    {
        $this->mes = now()->month;
        $this->anio = now()->year;
    }

    public function updatedMes()
    {
        $this->resetPage();
    }

    public function updatedAnio()
    {
        $this->resetPage();
    }

    public function updatedTimbradoId()
    {
        $this->resetPage();
    }

    public function updatedEstadoFactura()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Factura::with([
                'cliente',
                'timbrado',
                'puntoExpedicion.sucursal'
            ])
            ->where('activo', true)
            ->whereIn('estado', ['EMITIDA', 'ANULADA']);

        // Filtro por mes y año
        if ($this->mes && $this->anio) {
            $query->whereYear('fecha_emision', $this->anio)
                  ->whereMonth('fecha_emision', $this->mes);
        }

        // Filtro por timbrado
        if ($this->timbrado_id) {
            $query->where('timbrado_id', $this->timbrado_id);
        }

        // Filtro por estado
        if ($this->estado_factura) {
            $query->where('estado', $this->estado_factura);
        }

        $facturas = $query->orderBy('fecha_emision', 'asc')
            ->orderBy('numero_factura', 'asc')
            ->paginate(50);

        // Calcular totales del período (sin paginar)
        $totalesQuery = Factura::where('activo', true)
            ->where('estado', 'EMITIDA'); // Solo facturas emitidas para totales

        if ($this->mes && $this->anio) {
            $totalesQuery->whereYear('fecha_emision', $this->anio)
                         ->whereMonth('fecha_emision', $this->mes);
        }

        if ($this->timbrado_id) {
            $totalesQuery->where('timbrado_id', $this->timbrado_id);
        }

        $totales = $totalesQuery->selectRaw('
            SUM(CASE WHEN iva_10 > 0 THEN subtotal * (iva_10 / (subtotal + iva_10)) ELSE 0 END) as total_gravada_10,
            SUM(CASE WHEN iva_5 > 0 THEN subtotal * (iva_5 / (subtotal + iva_5)) ELSE 0 END) as total_gravada_5,
            SUM(exenta) as total_exenta,
            SUM(total) as total_general,
            SUM(iva_10) as total_iva_10,
            SUM(iva_5) as total_iva_5,
            SUM(total_iva) as total_iva
        ')->first();

        $this->total_gravada_10 = $totales->total_gravada_10 ?? 0;
        $this->total_gravada_5 = $totales->total_gravada_5 ?? 0;
        $this->total_exenta = $totales->total_exenta ?? 0;
        $this->total_general = $totales->total_general ?? 0;
        $this->total_iva_10 = $totales->total_iva_10 ?? 0;
        $this->total_iva_5 = $totales->total_iva_5 ?? 0;
        $this->total_iva = $totales->total_iva ?? 0;

        // Obtener timbrados disponibles
        $timbrados = Timbrado::where('activo', true)
            ->orderBy('numero_timbrado', 'desc')
            ->get();

        // Generar lista de meses
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        // Generar lista de años (últimos 5 años)
        $anios = range(now()->year, now()->year - 4);

        return view('livewire.ventas.libro-ventas', [
            'facturas' => $facturas,
            'timbrados' => $timbrados,
            'meses' => $meses,
            'anios' => $anios,
        ]);
    }

    public function exportarExcel()
    {
        // Implementar exportación a Excel
        session()->flash('info', 'Funcionalidad de exportación a Excel próximamente.');
    }
}
