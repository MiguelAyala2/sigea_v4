<?php

namespace App\Livewire\Servicios\Clientes;

use App\Exports\Excel\Servicios\Clientes\ExcelClientes;
use App\Exports\Pdf\Servicios\Clientes\PdfClientes;
use App\Models\Servicios\Cliente;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarTipo = '';
    public $buscarActivo = '';
    public $paginado = 10;

    // Modal de ver detalles
    public $showModal = false;
    public $clienteSeleccionado = null;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarTipo', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.servicios.clientes.index', [
            'clientes' => Cliente::query()
                ->buscador($this->buscador)
                ->buscarTipo($this->buscarTipo)
                ->buscarActivo($this->buscarActivo)
                ->orderBy('created_at', 'desc')
                ->paginate($this->paginado),
        ]);
    }

    public function activar($id)
    {
        Cliente::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Cliente activado correctamente!');
    }

    public function inactivar($id)
    {
        Cliente::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Cliente inactivado correctamente!');
    }

    public function eliminar($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();
        session()->flash('success', 'Cliente eliminado correctamente!');
    }

    public function verDetalles($id)
    {
        $this->clienteSeleccionado = Cliente::findOrFail($id);
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->clienteSeleccionado = null;
    }

    private function cargarDatosParaExportar()
    {
        return Cliente::query()
            ->buscador($this->buscador)
            ->buscarTipo($this->buscarTipo)
            ->buscarActivo($this->buscarActivo)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function excel()
    {
        $datos = $this->cargarDatosParaExportar();
        $encabezados = ['Tipo', 'Documento', 'Nombre/Razón Social', 'Teléfono', 'Celular', 'Email', 'Dirección', 'Estado'];

        return Excel::download(new ExcelClientes($datos, $encabezados), 'Clientes.xlsx');
    }

    public function pdf()
    {
        $nombre_archivo = 'Clientes';
        $datos = $this->cargarDatosParaExportar();
        $encabezados = ['Tipo', 'Documento', 'Nombre/Razón Social', 'Teléfono', 'Celular', 'Email', 'Dirección', 'Estado'];

        return (new PdfClientes($datos, $encabezados, $nombre_archivo))->download();
    }
}
