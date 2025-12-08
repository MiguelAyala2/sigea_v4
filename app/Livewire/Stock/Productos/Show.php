<?php

namespace App\Livewire\Stock\Productos;

use App\Models\Stock\Producto;
use Livewire\Component;

class Show extends Component
{
    public $productoId;
    public $producto;
    public $tabActiva = 'informacion';

    public function mount(Producto $producto)
    {
        $this->productoId = $producto->id;
        $this->producto = $producto->load([
            'categoria',
            'marca',
            'unidadMedida',
            'atributos',
            'imagenes',
            'imagenPrincipal',
            'precioActual',
            'stock.deposito',
            'movimientosStock' => function($query) {
                $query->with(['deposito', 'usuario'])
                      ->orderBy('fecha_movimiento', 'desc')
                      ->limit(10);
            }
        ]);
    }

    public function cambiarTab($tab)
    {
        $this->tabActiva = $tab;
    }

    public function render()
    {
        return view('livewire.stock.productos.show');
    }
}
