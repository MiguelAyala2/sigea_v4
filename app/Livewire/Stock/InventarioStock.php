<?php

namespace App\Livewire\Stock;

use App\Models\Stock\Stock;
use App\Models\Stock\MovimientoStock;
use App\Models\Stock\Producto;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventarioStock extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $depositoId = '';
    public $categoriaId = '';
    public $busqueda = '';
    public $soloConDiferencias = false;

    public $conteos = []; // Array para almacenar los conteos físicos
    public $observaciones = '';

    public function mount()
    {
        // Inicializar arrays
        $this->conteos = [];
    }

    public function updatingDepositoId()
    {
        $this->resetPage();
        $this->conteos = [];
    }

    public function updatingCategoriaId()
    {
        $this->resetPage();
        $this->conteos = [];
    }

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function actualizarConteo($stockId, $cantidad)
    {
        $this->conteos[$stockId] = $cantidad;
    }

    public function procesarInventario()
    {
        if (empty($this->conteos)) {
            session()->flash('error', 'Debe realizar al menos un conteo para procesar el inventario');
            return;
        }

        if (!$this->depositoId) {
            session()->flash('error', 'Debe seleccionar un depósito');
            return;
        }

        try {
            DB::beginTransaction();

            $ajustesRealizados = 0;

            foreach ($this->conteos as $stockId => $conteoFisico) {
                if ($conteoFisico === '' || $conteoFisico === null) {
                    continue;
                }

                $stock = Stock::find($stockId);
                if (!$stock) {
                    continue;
                }

                $diferencia = floatval($conteoFisico) - $stock->stock_actual;

                // Solo procesar si hay diferencia
                if ($diferencia != 0) {
                    $stockAnterior = $stock->stock_actual;
                    $stock->stock_actual = floatval($conteoFisico);
                    $stock->actualizadoPor = Auth::id();
                    $stock->save();

                    // Determinar tipo de movimiento
                    $tipo = $diferencia > 0 ? 'AJUSTE_POSITIVO' : 'AJUSTE_NEGATIVO';

                    // Registrar movimiento
                    MovimientoStock::create([
                        'producto_id' => $stock->producto_id,
                        'deposito_id' => $stock->deposito_id,
                        'tipo' => $tipo,
                        'cantidad' => abs($diferencia),
                        'stock_anterior' => $stockAnterior,
                        'stock_posterior' => $stock->stock_actual,
                        'documento_tipo' => 'INVENTARIO',
                        'motivo' => 'Ajuste por inventario físico. ' . ($this->observaciones ?: 'Sin observaciones'),
                        'usuario_id' => Auth::id(),
                        'fecha_movimiento' => now(),
                        'creadoPor' => Auth::id(),
                    ]);

                    $ajustesRealizados++;
                }
            }

            DB::commit();

            session()->flash('success', "Inventario procesado correctamente. Se realizaron {$ajustesRealizados} ajuste(s).");

            // Limpiar conteos
            $this->conteos = [];
            $this->observaciones = '';

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al procesar el inventario: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Stock::with(['producto.unidadMedida', 'producto.categoria', 'deposito'])
                      ->whereHas('producto', function($q) {
                          $q->where('activo', true)
                            ->where('maneja_stock', true);
                      });

        // Filtrar por depósito
        if ($this->depositoId) {
            $query->where('deposito_id', $this->depositoId);
        }

        // Filtrar por categoría
        if ($this->categoriaId) {
            $query->whereHas('producto', function($q) {
                $q->where('categoria_id', $this->categoriaId);
            });
        }

        // Filtrar por búsqueda
        if ($this->busqueda) {
            $query->whereHas('producto', function($q) {
                $q->where('nombre', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('codigo', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('codigo_barras', 'ilike', '%' . $this->busqueda . '%');
            });
        }

        $stocks = $query->orderBy('producto_id')
                        ->paginate(50);

        // Obtener listas para filtros
        $depositos = collect(); // Pendiente de módulo Empresa
        $categorias = \App\Models\Stock\Categoria::where('activo', true)
                                                  ->orderBy('nombre')
                                                  ->get();

        return view('livewire.stock.inventario-stock', [
            'stocks' => $stocks,
            'depositos' => $depositos,
            'categorias' => $categorias,
        ]);
    }
}
