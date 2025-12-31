<?php

namespace App\Livewire\Servicios\Clientes;

use App\Models\Servicios\Cliente;
use App\Models\Servicios\SolicitudServicio;
use App\Models\Servicios\Recepcion;
use App\Models\Servicios\OrdenServicio;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialServicios extends Component
{
    use WithPagination;

    public $buscador = '';
    public $cliente_id = null;
    public $tipo_servicio = '';
    public $estado = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $paginado = 15;

    // Cliente seleccionado
    public $clienteSeleccionado = null;

    // Resultados de búsqueda de clientes
    public $resultadosBusqueda = [];
    public $mostrarResultados = false;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Por defecto mostrar el mes actual
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingBuscador()
    {
        $this->resetPage();
    }

    public function buscarClientes()
    {
        if (strlen($this->buscador) >= 2) {
            $this->resultadosBusqueda = Cliente::query()
                ->where(function ($q) {
                    $q->where('nombre', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('documento', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('telefono', 'ilike', '%' . $this->buscador . '%')
                        ->orWhere('celular', 'ilike', '%' . $this->buscador . '%');
                })
                ->where('activo', true)
                ->limit(10)
                ->get();

            $this->mostrarResultados = true;
        } else {
            $this->resultadosBusqueda = [];
            $this->mostrarResultados = false;
        }
    }

    public function seleccionarCliente($clienteId)
    {
        $this->cliente_id = $clienteId;
        $this->clienteSeleccionado = Cliente::find($clienteId);
        $this->mostrarResultados = false;
        $this->buscador = $this->clienteSeleccionado->nombre;
        $this->resetPage();
    }

    public function limpiarCliente()
    {
        $this->cliente_id = null;
        $this->clienteSeleccionado = null;
        $this->buscador = '';
        $this->resultadosBusqueda = [];
        $this->mostrarResultados = false;
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->tipo_servicio = '';
        $this->estado = '';
        $this->fecha_desde = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_hasta = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $servicios = collect();
        $stats = [
            'total_servicios' => 0,
            'completados' => 0,
            'en_proceso' => 0,
            'pendientes' => 0,
            'total_facturado' => 0,
            'ultima_visita' => null,
        ];

        if ($this->clienteSeleccionado) {
            // Obtener solicitudes de servicio del cliente
            $solicitudes = SolicitudServicio::query()
                ->with(['producto', 'creador'])
                ->where('cliente_id', $this->cliente_id)
                ->when($this->tipo_servicio, function ($query) {
                    $query->where('tipo_servicio', $this->tipo_servicio);
                })
                ->when($this->estado, function ($query) {
                    $query->where('estado', $this->estado);
                })
                ->when($this->fecha_desde, function ($query) {
                    $query->whereDate('fecha', '>=', $this->fecha_desde);
                })
                ->when($this->fecha_hasta, function ($query) {
                    $query->whereDate('fecha', '<=', $this->fecha_hasta);
                })
                ->orderBy('fecha', 'desc')
                ->paginate($this->paginado);

            $servicios = $solicitudes;

            // Calcular estadísticas
            $todasSolicitudes = SolicitudServicio::where('cliente_id', $this->cliente_id)->get();

            $stats = [
                'total_servicios' => $todasSolicitudes->count(),
                'completados' => $todasSolicitudes->where('estado', 'completado')->count(),
                'en_proceso' => $todasSolicitudes->where('estado', 'en_proceso')->count(),
                'pendientes' => $todasSolicitudes->where('estado', 'pendiente')->count(),
                'total_facturado' => 0, // Se puede calcular si hay relación con facturación
                'ultima_visita' => $todasSolicitudes->max('fecha'),
            ];
        }

        return view('livewire.servicios.clientes.historial-servicios', [
            'servicios' => $servicios,
            'stats' => $stats,
        ]);
    }
}
