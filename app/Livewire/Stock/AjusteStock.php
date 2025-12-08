<?php

namespace App\Livewire\Stock;

use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;
use App\Models\Stock\Producto;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AjusteStock extends Component
{
    public $productoId = '';
    public $depositoId = '';
    public $tipoAjuste = 'AJUSTE_POSITIVO';
    public $cantidad = '';
    public $motivo = '';

    public $stockActual = null;
    public $productoSeleccionado = null;

    protected $rules = [
        'productoId' => 'required|exists:stock.PRODUCTOS,id',
        'depositoId' => 'required',
        'tipoAjuste' => 'required|in:AJUSTE_POSITIVO,AJUSTE_NEGATIVO',
        'cantidad' => 'required|numeric|min:0.01',
        'motivo' => 'required|min:10',
    ];

    protected $messages = [
        'productoId.required' => 'Debe seleccionar un producto',
        'productoId.exists' => 'El producto seleccionado no existe',
        'depositoId.required' => 'Debe seleccionar un depósito',
        'tipoAjuste.required' => 'Debe seleccionar el tipo de ajuste',
        'cantidad.required' => 'Debe ingresar la cantidad',
        'cantidad.numeric' => 'La cantidad debe ser un número',
        'cantidad.min' => 'La cantidad debe ser mayor a 0',
        'motivo.required' => 'Debe ingresar el motivo del ajuste',
        'motivo.min' => 'El motivo debe tener al menos 10 caracteres',
    ];

    public function updatedProductoId($value)
    {
        if ($value && $this->depositoId) {
            $this->cargarStockActual();
        }
    }

    public function updatedDepositoId($value)
    {
        if ($value && $this->productoId) {
            $this->cargarStockActual();
        }
    }

    private function cargarStockActual()
    {
        $this->productoSeleccionado = Producto::with('unidadMedida')->find($this->productoId);

        $stock = Stock::where('producto_id', $this->productoId)
                      ->where('deposito_id', $this->depositoId)
                      ->first();

        $this->stockActual = $stock ? $stock->stock_actual : 0;
    }

    public function realizarAjuste()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Obtener o crear el registro de stock
            $stock = Stock::firstOrCreate(
                [
                    'producto_id' => $this->productoId,
                    'deposito_id' => $this->depositoId,
                ],
                [
                    'stock_actual' => 0,
                    'stock_minimo' => 0,
                    'creadoPor' => Auth::id(),
                ]
            );

            $stockAnterior = $stock->stock_actual;

            // Calcular nuevo stock
            if ($this->tipoAjuste === 'AJUSTE_POSITIVO') {
                $nuevoStock = $stockAnterior + $this->cantidad;
            } else {
                $nuevoStock = $stockAnterior - $this->cantidad;

                // Validar que no quede negativo
                if ($nuevoStock < 0) {
                    $this->addError('cantidad', 'El ajuste negativo no puede ser mayor al stock actual');
                    return;
                }
            }

            // Actualizar stock
            $stock->stock_actual = $nuevoStock;
            $stock->actualizadoPor = Auth::id();
            $stock->save();

            // Registrar movimiento en kardex
            MovimientoStock::create([
                'producto_id' => $this->productoId,
                'deposito_id' => $this->depositoId,
                'tipo' => $this->tipoAjuste,
                'cantidad' => $this->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $nuevoStock,
                'motivo' => $this->motivo,
                'usuario_id' => Auth::id(),
                'fecha_movimiento' => now(),
                'creadoPor' => Auth::id(),
            ]);

            DB::commit();

            session()->flash('success', 'Ajuste de stock realizado correctamente');

            // Limpiar formulario
            $this->reset(['productoId', 'depositoId', 'cantidad', 'motivo', 'stockActual', 'productoSeleccionado']);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al realizar el ajuste: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $productos = Producto::where('activo', true)
                             ->where('maneja_stock', true)
                             ->orderBy('nombre')
                             ->get();

        $depositos = collect(); // Pendiente de módulo Empresa

        return view('livewire.stock.ajuste-stock', [
            'productos' => $productos,
            'depositos' => $depositos,
        ]);
    }
}
