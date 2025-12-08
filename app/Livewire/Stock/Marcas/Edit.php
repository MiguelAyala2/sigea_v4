<?php

namespace App\Livewire\Stock\Marcas;

use App\Models\Stock\Marca;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $marcaId;
    public $codigo = '';

    #[Validate]
    public $nombre = '';
    public $descripcion = '';

    public function mount(Marca $marca)
    {
        $this->marcaId = $marca->id;
        $this->codigo = $marca->codigo;
        $this->nombre = $marca->nombre;
        $this->descripcion = $marca->descripcion;
    }

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Marca::class, 'nombre')
                    ->ignore($this->marcaId)
                    ->whereNull('deleted_at')
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function guardar()
    {
        $this->validate();

        $marca = Marca::findOrFail($this->marcaId);
        $marca->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Marca actualizada correctamente!');
        return redirect()->route('stock.marcas.index');
    }

    public function render()
    {
        return view('livewire.stock.marcas.edit');
    }
}
