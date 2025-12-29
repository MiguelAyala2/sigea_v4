<?php

namespace App\Livewire\Ventas;

use App\Models\Servicios\Cliente;
use App\Models\Ventas\Factura;
use App\Models\Ventas\Remision;
use App\Models\Ventas\RemisionDetalle;
use App\Models\Empresa\Sucursal;
use App\Models\Empresa\Deposito;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RemisionForm extends Component
{
    public $cliente_id;
    public $factura_id;
    public $fecha_emision;
    public $fecha_entrega;
    public $sucursal_id;
    public $deposito_id;
    public $responsable_id;
    public $direccion_entrega;
    public $observaciones;
    public $detalles = [];

    public $buscar_cliente = '';
    public $clientes_encontrados = [];
    public $mostrar_resultados_cliente = false;
    public $facturas = [];
    public $sucursales = [];
    public $depositos = [];
    public $cliente_seleccionado;
    public $factura_seleccionada;

    public function mount()
    {
        $this->fecha_emision = now()->format('Y-m-d');
        $this->fecha_entrega = now()->addDays(1)->format('Y-m-d');
        $this->sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();

        $primeraSucursal = $this->sucursales->first();
        $this->sucursal_id = $primeraSucursal ? $primeraSucursal->id : null;

        $this->cargarDepositos();
        $this->responsable_id = auth()->id();
    }

    public function updatedBuscarCliente()
    {
        if (strlen($this->buscar_cliente) >= 2) {
            $this->clientes_encontrados = Cliente::where('activo', true)
                ->where(function($query) {
                    $query->where('nombre', 'ILIKE', '%' . $this->buscar_cliente . '%')
                          ->orWhere('documento', 'ILIKE', '%' . $this->buscar_cliente . '%');
                })
                ->orderBy('nombre')
                ->limit(10)
                ->get();
            $this->mostrar_resultados_cliente = true;
        } else {
            $this->clientes_encontrados = [];
            $this->mostrar_resultados_cliente = false;
        }
    }

    public function seleccionarCliente($clienteId)
    {
        $this->cliente_id = $clienteId;
        $this->cliente_seleccionado = Cliente::find($clienteId);
        $this->buscar_cliente = $this->cliente_seleccionado->nombre_razon_social;
        $this->direccion_entrega = $this->cliente_seleccionado->direccion ?? '';
        $this->mostrar_resultados_cliente = false;
        $this->clientes_encontrados = [];
        $this->cargarFacturas();
    }

    public function limpiarCliente()
    {
        $this->cliente_id = null;
        $this->cliente_seleccionado = null;
        $this->buscar_cliente = '';
        $this->facturas = [];
        $this->factura_id = null;
        $this->factura_seleccionada = null;
        $this->direccion_entrega = '';
        $this->detalles = [];
        $this->clientes_encontrados = [];
        $this->mostrar_resultados_cliente = false;
    }

    public function updatedSucursalId()
    {
        $this->cargarDepositos();
    }

    public function cargarDepositos()
    {
        if ($this->sucursal_id) {
            $this->depositos = Deposito::where('sucursal_id', $this->sucursal_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $primerDeposito = $this->depositos->first();
            $this->deposito_id = $primerDeposito ? $primerDeposito->id : null;
        }
    }

    public function cargarFacturas()
    {
        if ($this->cliente_id) {
            $this->facturas = Factura::where('cliente_id', $this->cliente_id)
                ->whereIn('estado', ['EMITIDA', 'PAGADA', 'PARCIALMENTE_PAGADA'])
                ->with(['detalles.producto.unidadMedida'])
                ->orderBy('fecha_emision', 'desc')
                ->get();
        } else {
            $this->facturas = [];
        }
    }

    public function updatedFacturaId()
    {
        if ($this->factura_id) {
            $this->factura_seleccionada = Factura::with(['detalles.producto.unidadMedida'])->find($this->factura_id);
            $this->cargarDetallesDesdeFactura();
        } else {
            $this->factura_seleccionada = null;
            $this->detalles = [];
        }
    }

    public function cargarDetallesDesdeFactura()
    {
        if (!$this->factura_seleccionada) {
            return;
        }

        $this->detalles = [];
        foreach ($this->factura_seleccionada->detalles as $detalle) {
            // Obtener descripción del producto
            $descripcion = '';
            $unidad = 'UND';

            if ($detalle->producto) {
                // Preferir descripción, si no existe usar nombre
                $descripcion = $detalle->producto->descripcion ?: $detalle->producto->nombre;

                // Obtener unidad de medida desde la relación
                if ($detalle->producto->unidadMedida) {
                    $unidad = $detalle->producto->unidadMedida->codigo ?? $detalle->producto->unidadMedida->nombre ?? 'UND';
                }
            }

            // Si tiene descripción adicional, agregarla
            if ($detalle->descripcion_adicional) {
                $descripcion = $descripcion ? $descripcion . ' - ' . $detalle->descripcion_adicional : $detalle->descripcion_adicional;
            }

            $this->detalles[] = [
                'producto_id' => $detalle->producto_id,
                'producto_descripcion' => $descripcion,
                'cantidad' => $detalle->cantidad,
                'unidad_medida' => $unidad,
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal' => $detalle->subtotal,
                'factura_detalle_id' => $detalle->id,
                'observaciones' => '',
            ];
        }
    }

    public function agregarDetalle()
    {
        $this->detalles[] = [
            'producto_id' => null,
            'producto_descripcion' => '',
            'cantidad' => 1,
            'unidad_medida' => 'UND',
            'precio_unitario' => 0,
            'subtotal' => 0,
            'factura_detalle_id' => null,
            'observaciones' => '',
        ];
    }

    public function eliminarDetalle($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
    }

    public function calcularSubtotal($index)
    {
        $detalle = &$this->detalles[$index];
        $detalle['subtotal'] = round($detalle['cantidad'] * $detalle['precio_unitario'], 2);
    }

    public function guardar()
    {
        $this->validate([
            'cliente_id' => 'required|exists:App\Models\Servicios\Cliente,id',
            'fecha_emision' => 'required|date',
            'sucursal_id' => 'required|exists:App\Models\Empresa\Sucursal,id',
            'deposito_id' => 'required|exists:App\Models\Empresa\Deposito,id',
            'direccion_entrega' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_descripcion' => 'required|string',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
        ], [
            'cliente_id.required' => 'Debe seleccionar un cliente',
            'detalles.required' => 'Debe agregar al menos un detalle',
            'detalles.*.producto_descripcion.required' => 'La descripción del producto es requerida',
            'detalles.*.cantidad.required' => 'La cantidad es requerida',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
        ]);

        try {
            DB::beginTransaction();

            $remision = new Remision();
            $remision->numero_remision = $remision->generarNumeroRemision();
            $remision->fecha_emision = $this->fecha_emision;
            $remision->fecha_entrega = $this->fecha_entrega;
            $remision->cliente_id = $this->cliente_id;
            $remision->factura_id = $this->factura_id;
            $remision->sucursal_id = $this->sucursal_id;
            $remision->deposito_id = $this->deposito_id;
            $remision->responsable_id = $this->responsable_id;
            $remision->direccion_entrega = $this->direccion_entrega;
            $remision->observaciones = $this->observaciones;
            $remision->estado = 'BORRADOR';
            $remision->creado_por = auth()->id();
            $remision->activo = true;
            $remision->save();

            foreach ($this->detalles as $detalle) {
                RemisionDetalle::create([
                    'remision_id' => $remision->id,
                    'producto_id' => $detalle['producto_id'],
                    'producto_descripcion' => $detalle['producto_descripcion'],
                    'cantidad' => $detalle['cantidad'],
                    'unidad_medida' => $detalle['unidad_medida'] ?? 'UND',
                    'precio_unitario' => $detalle['precio_unitario'] ?? 0,
                    'subtotal' => $detalle['subtotal'] ?? 0,
                    'factura_detalle_id' => $detalle['factura_detalle_id'] ?? null,
                    'observaciones' => $detalle['observaciones'] ?? '',
                    'activo' => true,
                ]);
            }

            DB::commit();

            session()->flash('success', 'Remisión creada correctamente con número: ' . $remision->numero_remision);
            return redirect()->route('ventas.remisiones.show', $remision);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear la remisión: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ventas.remision-form');
    }
}
