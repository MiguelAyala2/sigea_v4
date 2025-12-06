<?php

namespace App\Livewire\Empresa\Empresa;

use App\Models\Empresa\Empresa;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.empresa.empresa.index', [
            'empresa' => Empresa::actual(),
        ]);
    }
}
