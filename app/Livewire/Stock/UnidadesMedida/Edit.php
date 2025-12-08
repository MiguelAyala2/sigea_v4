<?php

namespace App\Livewire\Stock\UnidadesMedida;

use App\Models\Stock\UnidadMedida;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public $unidadMedidaId;
    public $codigo = '';

    #[Validate]
    public $nombre = '';
    public $simbolo = '';
    public $permite_decimales = false;

    public function mount(UnidadMedida $unidadMedida)
    {
        $this->unidadMedidaId = $unidadMedida->id;
        $this->codigo = $unidadMedida->codigo;
        $this->nombre = $unidadMedida->nombre;
        $this->simbolo = $unidadMedida->simbolo;
        $this->permite_decimales = $unidadMedida->permite_decimales;
    }

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique(UnidadMedida::class, 'nombre')
                    ->ignore($this->unidadMedidaId)
                    ->whereNull('deleted_at')
            ],
            'simbolo' => ['required', 'string', 'max:10'],
            'permite_decimales' => ['boolean'],
        ];
    }

    public function guardar()
    {
        $this->validate();

        $unidad = UnidadMedida::findOrFail($this->unidadMedidaId);
        $unidad->update([
            'nombre' => $this->nombre,
            'simbolo' => $this->simbolo,
            'permite_decimales' => $this->permite_decimales,
            'actualizadoPor' => Auth::id(),
        ]);

        session()->flash('success', 'Unidad de medida actualizada correctamente!');
        return redirect()->route('stock.unidades-medida.index');
    }

    public function render()
    {
        return view('livewire.stock.unidades-medida.edit');
    }
}
