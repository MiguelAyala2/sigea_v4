<?php

namespace App\Livewire\Servicios;

use App\Models\Servicios\Reclamo;
use App\Models\Servicios\Cliente;
use App\Models\Servicios\OrdenServicio;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ReclamosManager extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarEstado = '';
    public $buscarPrioridad = '';
    public $paginado = 10;

    public $mostrarModal = false;
    public $reclamoSeleccionado = null;

    // Para el formulario de registro
    public $cliente_id;
    public $buscarCliente = '';
    public $clienteSeleccionado = null;
    public $mostrarListaClientes = false;
    public $orden_servicio_id;
    public $tipo_reclamo = 'calidad_servicio';
    public $prioridad = 'media';
    public $fecha_reclamo;
    public $descripcion = '';
    public $responsable_id;
    public $canal_recepcion = 'presencial';

    // Para edición
    public $estado = 'pendiente';
    public $solucion = '';
    public $fecha_resolucion;
    public $fecha_cierre;

    public function mount()
    {
        $this->fecha_reclamo = now()->format('Y-m-d');
    }

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarEstado', 'buscarPrioridad', 'paginado'])) {
            $this->resetPage();
        }

        if ($key === 'buscarCliente') {
            // Mostrar lista si hay al menos 2 caracteres
            $this->mostrarListaClientes = strlen($this->buscarCliente) >= 2;
            // Si se limpia el campo, ocultar lista
            if (strlen($this->buscarCliente) === 0) {
                $this->mostrarListaClientes = false;
            }
        }
    }

    public function seleccionarCliente($clienteId)
    {
        $cliente = Cliente::find($clienteId);
        if ($cliente) {
            $this->cliente_id = $cliente->id;
            $this->clienteSeleccionado = $cliente;
            $this->buscarCliente = $cliente->nombre;
            $this->mostrarListaClientes = false;
            // Limpiar la orden de servicio seleccionada al cambiar de cliente
            $this->orden_servicio_id = null;
        }
    }

    public function limpiarCliente()
    {
        $this->cliente_id = null;
        $this->clienteSeleccionado = null;
        $this->buscarCliente = '';
        $this->mostrarListaClientes = false;
        // Limpiar también la orden de servicio
        $this->orden_servicio_id = null;
    }

    public function render()
    {
        $reclamos = Reclamo::with(['cliente', 'ordenServicio', 'responsable'])
            ->when($this->buscador, function ($query) {
                $query->where('codigo', 'ILIKE', "%{$this->buscador}%")
                    ->orWhereHas('cliente', function ($q) {
                        $q->where('nombre', 'ILIKE', "%{$this->buscador}%");
                    });
            })
            ->when($this->buscarEstado, function ($query) {
                $query->where('estado', $this->buscarEstado);
            })
            ->when($this->buscarPrioridad, function ($query) {
                $query->where('prioridad', $this->buscarPrioridad);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->paginado);

        // Buscar clientes cuando se está escribiendo (al menos 2 caracteres)
        $clientesEncontrados = [];
        if ($this->mostrarListaClientes && strlen($this->buscarCliente) >= 2) {
            $clientesEncontrados = Cliente::where(function($query) {
                    $query->where('nombre', 'ILIKE', "%{$this->buscarCliente}%")
                          ->orWhere('documento', 'ILIKE', "%{$this->buscarCliente}%");
                })
                ->orderBy('nombre')
                ->limit(10)
                ->get();
        }

        // Filtrar órdenes de servicio solo del cliente seleccionado
        $ordenes = [];
        if ($this->cliente_id) {
            $ordenes = OrdenServicio::with(['presupuesto.diagnostico.recepcion.solicitud.cliente'])
                ->whereHas('presupuesto.diagnostico.recepcion.solicitud', function ($query) {
                    $query->where('cliente_id', $this->cliente_id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $responsables = User::orderBy('name')->get();

        return view('livewire.servicios.reclamos-manager', compact('reclamos', 'clientesEncontrados', 'ordenes', 'responsables'));
    }

    public function registrarReclamo()
    {
        $this->validate([
            'cliente_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Cliente::find($value)) {
                        $fail('El cliente seleccionado no existe.');
                    }
                }
            ],
            'orden_servicio_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !OrdenServicio::find($value)) {
                        $fail('La orden de servicio seleccionada no existe.');
                    }
                }
            ],
            'tipo_reclamo' => 'required|in:calidad_servicio,demora_entrega,falla_post_servicio,atencion_cliente,costo_facturacion,otro',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'fecha_reclamo' => 'required|date',
            'descripcion' => 'required|string|min:10',
            'responsable_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !User::find($value)) {
                        $fail('El responsable seleccionado no existe.');
                    }
                }
            ],
            'canal_recepcion' => 'required|in:presencial,telefono,email,whatsapp,web',
        ], [
            'cliente_id.required' => 'Debe seleccionar un cliente',
            'descripcion.required' => 'La descripción del reclamo es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
        ]);

        Reclamo::create([
            'codigo' => Reclamo::generarCodigo(),
            'cliente_id' => $this->cliente_id,
            'orden_servicio_id' => $this->orden_servicio_id,
            'tipo_reclamo' => $this->tipo_reclamo,
            'prioridad' => $this->prioridad,
            'fecha_reclamo' => $this->fecha_reclamo,
            'descripcion' => $this->descripcion,
            'responsable_id' => $this->responsable_id,
            'canal_recepcion' => $this->canal_recepcion,
            'creadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Reclamo registrado exitosamente!');
        $this->resetFormulario();
        return redirect()->route('servicios.reclamos.seguimiento');
    }

    public function verReclamo($id)
    {
        $this->reclamoSeleccionado = Reclamo::with(['cliente', 'ordenServicio', 'responsable', 'creador'])
            ->findOrFail($id);
        $this->mostrarModal = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->reclamoSeleccionado = null;
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        $reclamo = Reclamo::findOrFail($id);
        $reclamo->update([
            'estado' => $nuevoEstado,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Estado actualizado correctamente!');
    }

    public function resetFormulario()
    {
        $this->cliente_id = null;
        $this->buscarCliente = '';
        $this->clienteSeleccionado = null;
        $this->mostrarListaClientes = false;
        $this->orden_servicio_id = null;
        $this->tipo_reclamo = 'calidad_servicio';
        $this->prioridad = 'media';
        $this->fecha_reclamo = now()->format('Y-m-d');
        $this->descripcion = '';
        $this->responsable_id = null;
        $this->canal_recepcion = 'presencial';
    }
}
