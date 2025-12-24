<?php

namespace App\Livewire\Servicios\Recepciones;

use App\Exports\Excel\Servicios\Recepciones\ExcelRecepciones;
use App\Exports\Pdf\Servicios\Recepciones\PdfRecepciones;
use App\Models\Servicios\Recepcion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarEstadoRecepcion = '';
    public $buscarEstado = '';
    public $buscarActivo = '';
    public $paginado = 10;

    // Modal de ver detalles
    public $showModal = false;
    public $recepcionSeleccionada = null;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarEstadoRecepcion', 'buscarEstado', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.servicios.recepciones.index', [
            'recepciones' => Recepcion::query()
                ->with(['cliente', 'producto', 'solicitud'])
                ->buscador($this->buscador)
                ->buscarEstadoRecepcion($this->buscarEstadoRecepcion)
                ->buscarEstado($this->buscarEstado)
                ->buscarActivo($this->buscarActivo)
                ->orderBy('fecha_recepcion', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($this->paginado),
        ]);
    }

    public function activar($id)
    {
        Recepcion::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Recepción activada correctamente!');
    }

    public function inactivar($id)
    {
        Recepcion::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Recepción inactivada correctamente!');
    }

    public function eliminar($id)
    {
        $recepcion = Recepcion::findOrFail($id);
        $recepcion->delete();
        session()->flash('success', 'Recepción eliminada correctamente!');
    }

    public function verDetalles($id)
    {
        $this->recepcionSeleccionada = Recepcion::with(['cliente', 'producto', 'solicitud'])->findOrFail($id);
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->recepcionSeleccionada = null;
    }

    public function exportarExcel()
    {
        $datos = Recepcion::query()
            ->with(['cliente', 'producto', 'solicitud'])
            ->buscador($this->buscador)
            ->buscarEstadoRecepcion($this->buscarEstadoRecepcion)
            ->buscarEstado($this->buscarEstado)
            ->buscarActivo($this->buscarActivo)
            ->orderBy('fecha_recepcion', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $encabezados = [
            'N° Recepción',
            'Fecha',
            'N° Solicitud',
            'Cliente',
            'Equipo/Producto',
            'Tipo Equipo',
            'Marca',
            'Modelo',
            'N° Serie',
            'Estado Recepción',
            'Estado',
            'Descripción Problema',
            'Activo',
        ];

        $nombre_archivo = 'Recepciones_' . date('Y-m-d_H-i-s');

        return Excel::download(
            new ExcelRecepciones($datos, $nombre_archivo, $encabezados),
            $nombre_archivo . '.xlsx'
        );
    }

    public function exportarPdf()
    {
        $datos = Recepcion::query()
            ->with(['cliente', 'producto', 'solicitud'])
            ->buscador($this->buscador)
            ->buscarEstadoRecepcion($this->buscarEstadoRecepcion)
            ->buscarEstado($this->buscarEstado)
            ->buscarActivo($this->buscarActivo)
            ->orderBy('fecha_recepcion', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $encabezados = [
            'N° Recep.',
            'Fecha',
            'N° Solic.',
            'Cliente',
            'Equipo/Prod.',
            'Tipo',
            'Marca',
            'Modelo',
            'Serie',
            'Est. Recep.',
            'Estado',
            'Descripción',
            'Activo',
        ];

        $nombre_archivo = 'Recepciones_' . date('Y-m-d_H-i-s');

        $pdf = new PdfRecepciones($datos, $nombre_archivo, $encabezados);
        return $pdf->download();
    }
}
