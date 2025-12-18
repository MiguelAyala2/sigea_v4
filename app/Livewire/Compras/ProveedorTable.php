<?php

namespace App\Livewire\Compras;

use App\Models\Compras\Proveedor;
use Livewire\Component;
use Livewire\WithPagination;

class ProveedorTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $tipo = '';
    public $estado = 'activos';

    protected $queryString = ['search', 'tipo', 'estado'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $proveedores = Proveedor::query()
            ->when($this->search, function ($query) {
                $query->buscador($this->search);
            })
            ->when($this->tipo, function ($query) {
                $query->porTipo($this->tipo);
            })
            ->when($this->estado === 'activos', function ($query) {
                $query->activos();
            })
            ->when($this->estado === 'inactivos', function ($query) {
                $query->inactivos();
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.compras.proveedor-table', [
            'proveedores' => $proveedores,
        ]);
    }
}
