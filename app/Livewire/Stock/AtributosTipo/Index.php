<?php

namespace App\Livewire\Stock\AtributosTipo;

use App\Models\Stock\AtributoTipo;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $busqueda = '';
    public $soloActivos = true;

    protected $queryString = [
        'busqueda' => ['except' => ''],
        'soloActivos' => ['except' => true],
    ];

    public function updatingBusqueda()
    {
        $this->resetPage();
    }

    public function updatingSoloActivos()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AtributoTipo::query();

        // Filtrar activos
        if ($this->soloActivos) {
            $query->activos();
        }

        // Buscar
        if ($this->busqueda) {
            $query->where(function($q) {
                $q->where('nombre', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('codigo', 'ilike', '%' . $this->busqueda . '%');
            });
        }

        $atributosTipo = $query->orderBy('orden')
                               ->orderBy('nombre')
                               ->paginate(20);

        return view('livewire.stock.atributos-tipo.index', [
            'atributosTipo' => $atributosTipo,
        ]);
    }
}
