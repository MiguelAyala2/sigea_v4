<?php

namespace App\Livewire\Stock;

use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;
use App\Models\Stock\AjusteStock as AjusteStockModel;
use App\Models\Stock\Producto;
use App\Models\Empresa\Deposito;
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
    public $motivoAjuste = ''; // Motivo predefinido seleccionado

    public $stockActual = null;
    public $productoSeleccionado = null;

    // Buscador de productos
    public $searchProducto = '';
    public $productosEncontrados = [];
    public $mostrarResultados = false;

    protected function rules()
    {
        return [
            'productoId' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = \DB::table('stock.PRODUCTOS')->where('id', $value)->exists();
                    if (!$exists) {
                        $fail('El producto seleccionado no es válido.');
                    }
                },
            ],
            'depositoId' => 'required',
            'tipoAjuste' => 'required|in:AJUSTE_POSITIVO,AJUSTE_NEGATIVO',
            'cantidad' => 'required|numeric|min:0.01',
            'motivoAjuste' => 'required',
            'motivo' => 'required|min:10',
        ];
    }

    protected $messages = [
        'productoId.required' => 'Debe seleccionar un producto',
        'productoId.exists' => 'El producto seleccionado no existe',
        'depositoId.required' => 'Debe seleccionar un depósito',
        'tipoAjuste.required' => 'Debe seleccionar el tipo de ajuste',
        'cantidad.required' => 'Debe ingresar la cantidad',
        'cantidad.numeric' => 'La cantidad debe ser un número',
        'cantidad.min' => 'La cantidad debe ser mayor a 0',
        'motivoAjuste.required' => 'Debe seleccionar un motivo de ajuste',
        'motivo.required' => 'Debe ingresar observaciones adicionales',
        'motivo.min' => 'Las observaciones deben tener al menos 10 caracteres',
    ];

    // Motivos de ajuste positivo
    public function getMotivosPositivosProperty()
    {
        return [
            'Sobrantes de Inventario' => 'Sobrantes de Inventario - Diferencias encontradas en conteos físicos',
            'Devoluciones de Clientes' => 'Devoluciones de Clientes - Productos devueltos',
            'Recuperación de Productos' => 'Recuperación de Productos - Productos reparados, muestras devueltas',
            'Correcciones de Errores' => 'Correcciones de Errores - Salidas mal registradas',
            'Producción/Transformación' => 'Producción/Transformación - Productos terminados, ensamblajes',
        ];
    }

    // Motivos de ajuste negativo
    public function getMotivosNegativosProperty()
    {
        return [
            'Faltantes de Inventario' => 'Faltantes de Inventario - Diferencias en conteos físicos, hurtos',
            'Mermas y Deterioros' => 'Mermas y Deterioros - Vencidos, dañados, obsoletos, roturas',
            'Consumo Interno' => 'Consumo Interno - Uso en oficina, muestras, demostraciones',
            'Correcciones de Errores' => 'Correcciones de Errores - Ingresos mal registrados',
            'Donaciones' => 'Donaciones - Productos donados',
            'Mermas por Proceso' => 'Mermas por Proceso - Evaporación, pérdida de peso natural',
        ];
    }

    public function updatedSearchProducto($value)
    {
        if (strlen($value) >= 2) {
            $this->productosEncontrados = Producto::where('activo', true)
                ->where('maneja_stock', true)
                ->where(function($query) use ($value) {
                    $query->where('nombre', 'ILIKE', '%' . $value . '%')
                          ->orWhere('codigo', 'ILIKE', '%' . $value . '%');
                })
                ->with('unidadMedida')
                ->limit(10)
                ->get();
            $this->mostrarResultados = true;
        } else {
            $this->productosEncontrados = [];
            $this->mostrarResultados = false;
        }
    }

    public function seleccionarProducto($productoId)
    {
        $producto = Producto::with('unidadMedida')->find($productoId);
        if ($producto) {
            $this->productoId = $productoId;
            $this->productoSeleccionado = $producto;
            $this->searchProducto = $producto->codigo . ' - ' . $producto->nombre;
            $this->mostrarResultados = false;
            $this->productosEncontrados = [];

            // Cargar stock si ya hay depósito seleccionado
            if ($this->depositoId) {
                $this->cargarStockActual();
            }
        }
    }

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

            // Registrar movimiento en kardex con motivo completo
            $motivoCompleto = $this->motivoAjuste . ' | ' . $this->motivo;

            $movimiento = MovimientoStock::create([
                'producto_id' => $this->productoId,
                'deposito_id' => $this->depositoId,
                'tipo' => $this->tipoAjuste,
                'cantidad' => $this->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $nuevoStock,
                'motivo' => $motivoCompleto,
                'usuario_id' => Auth::id(),
                'fecha_movimiento' => now(),
                'creadoPor' => Auth::id(),
            ]);

            // Registrar en tabla de ajustes_stock
            AjusteStockModel::create([
                'producto_id' => $this->productoId,
                'deposito_id' => $this->depositoId,
                'usuario_id' => Auth::id(),
                'tipo_ajuste' => $this->tipoAjuste,
                'motivo_ajuste' => $this->motivoAjuste,
                'observaciones' => $this->motivo,
                'cantidad' => $this->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $nuevoStock,
                'fecha_ajuste' => now(),
                'movimiento_stock_id' => $movimiento->id,
                'creadoPor' => Auth::id(),
            ]);

            DB::commit();

            session()->flash('success', 'Ajuste de stock realizado correctamente');

            // Limpiar formulario
            $this->reset(['productoId', 'depositoId', 'cantidad', 'motivo', 'motivoAjuste', 'stockActual', 'productoSeleccionado', 'searchProducto', 'productosEncontrados', 'mostrarResultados']);

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

        $depositos = Deposito::where('activo', true)
                             ->orderBy('nombre')
                             ->get();

        return view('livewire.stock.ajuste-stock', [
            'productos' => $productos,
            'depositos' => $depositos,
        ]);
    }
}
