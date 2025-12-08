<?php

namespace App\Livewire\Stock;

use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;
use App\Models\Stock\Producto;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransferenciaStock extends Component
{
    public $productoId = '';
    public $depositoOrigenId = '';
    public $depositoDestinoId = '';
    public $cantidad = '';
    public $motivo = '';

    public $stockOrigen = null;
    public $stockDestino = null;
    public $productoSeleccionado = null;

    protected $rules = [
        'productoId' => 'required|exists:stock.PRODUCTOS,id',
        'depositoOrigenId' => 'required',
        'depositoDestinoId' => 'required|different:depositoOrigenId',
        'cantidad' => 'required|numeric|min:0.01',
        'motivo' => 'required|min:10',
    ];

    protected $messages = [
        'productoId.required' => 'Debe seleccionar un producto',
        'depositoOrigenId.required' => 'Debe seleccionar el depósito de origen',
        'depositoDestinoId.required' => 'Debe seleccionar el depósito de destino',
        'depositoDestinoId.different' => 'El depósito de destino debe ser diferente al de origen',
        'cantidad.required' => 'Debe ingresar la cantidad',
        'cantidad.numeric' => 'La cantidad debe ser un número',
        'cantidad.min' => 'La cantidad debe ser mayor a 0',
        'motivo.required' => 'Debe ingresar el motivo de la transferencia',
        'motivo.min' => 'El motivo debe tener al menos 10 caracteres',
    ];

    public function updatedProductoId($value)
    {
        $this->cargarStocks();
    }

    public function updatedDepositoOrigenId($value)
    {
        $this->cargarStocks();
    }

    public function updatedDepositoDestinoId($value)
    {
        $this->cargarStocks();
    }

    private function cargarStocks()
    {
        if (!$this->productoId) {
            return;
        }

        $this->productoSeleccionado = Producto::with('unidadMedida')->find($this->productoId);

        if ($this->depositoOrigenId) {
            $stockOrigen = Stock::where('producto_id', $this->productoId)
                                ->where('deposito_id', $this->depositoOrigenId)
                                ->first();
            $this->stockOrigen = $stockOrigen ? $stockOrigen->stock_actual : 0;
        }

        if ($this->depositoDestinoId) {
            $stockDestino = Stock::where('producto_id', $this->productoId)
                                 ->where('deposito_id', $this->depositoDestinoId)
                                 ->first();
            $this->stockDestino = $stockDestino ? $stockDestino->stock_actual : 0;
        }
    }

    public function realizarTransferencia()
    {
        $this->validate();

        // Validar que haya stock suficiente en origen
        if ($this->cantidad > $this->stockOrigen) {
            $this->addError('cantidad', 'No hay stock suficiente en el depósito de origen');
            return;
        }

        try {
            DB::beginTransaction();

            // 1. Actualizar stock en depósito origen (SALIDA)
            $stockOrigen = Stock::where('producto_id', $this->productoId)
                                ->where('deposito_id', $this->depositoOrigenId)
                                ->firstOrFail();

            $stockAnteriorOrigen = $stockOrigen->stock_actual;
            $stockOrigen->stock_actual -= $this->cantidad;
            $stockOrigen->actualizadoPor = Auth::id();
            $stockOrigen->save();

            // Registrar movimiento de salida
            MovimientoStock::create([
                'producto_id' => $this->productoId,
                'deposito_id' => $this->depositoOrigenId,
                'tipo' => 'TRANSFERENCIA_ORIGEN',
                'cantidad' => $this->cantidad,
                'stock_anterior' => $stockAnteriorOrigen,
                'stock_posterior' => $stockOrigen->stock_actual,
                'motivo' => $this->motivo,
                'usuario_id' => Auth::id(),
                'fecha_movimiento' => now(),
                'creadoPor' => Auth::id(),
            ]);

            // 2. Actualizar stock en depósito destino (ENTRADA)
            $stockDestino = Stock::firstOrCreate(
                [
                    'producto_id' => $this->productoId,
                    'deposito_id' => $this->depositoDestinoId,
                ],
                [
                    'stock_actual' => 0,
                    'stock_minimo' => 0,
                    'creadoPor' => Auth::id(),
                ]
            );

            $stockAnteriorDestino = $stockDestino->stock_actual;
            $stockDestino->stock_actual += $this->cantidad;
            $stockDestino->actualizadoPor = Auth::id();
            $stockDestino->save();

            // Registrar movimiento de entrada
            MovimientoStock::create([
                'producto_id' => $this->productoId,
                'deposito_id' => $this->depositoDestinoId,
                'tipo' => 'TRANSFERENCIA_DESTINO',
                'cantidad' => $this->cantidad,
                'stock_anterior' => $stockAnteriorDestino,
                'stock_posterior' => $stockDestino->stock_actual,
                'motivo' => $this->motivo,
                'usuario_id' => Auth::id(),
                'fecha_movimiento' => now(),
                'creadoPor' => Auth::id(),
            ]);

            DB::commit();

            session()->flash('success', 'Transferencia de stock realizada correctamente');

            // Limpiar formulario
            $this->reset([
                'productoId',
                'depositoOrigenId',
                'depositoDestinoId',
                'cantidad',
                'motivo',
                'stockOrigen',
                'stockDestino',
                'productoSeleccionado'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al realizar la transferencia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $productos = Producto::where('activo', true)
                             ->where('maneja_stock', true)
                             ->orderBy('nombre')
                             ->get();

        $depositos = collect(); // Pendiente de módulo Empresa

        return view('livewire.stock.transferencia-stock', [
            'productos' => $productos,
            'depositos' => $depositos,
        ]);
    }
}
