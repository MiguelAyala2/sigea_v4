<?php

namespace App\Livewire\Servicios\Solicitudes;

use App\Exports\Excel\Servicios\Solicitudes\ExcelSolicitudes;
use App\Exports\Pdf\Servicios\Solicitudes\PdfSolicitudes;
use App\Models\Servicios\SolicitudServicio;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    public $buscador = '';
    public $buscarEstado = '';
    public $buscarPrioridad = '';
    public $buscarTipoServicio = '';
    public $buscarActivo = '';
    public $paginado = 10;

    // Modal de ver detalles
    public $showModal = false;
    public $solicitudSeleccionada = null;

    public function updating($key): void
    {
        if (in_array($key, ['buscador', 'buscarEstado', 'buscarPrioridad', 'buscarTipoServicio', 'buscarActivo', 'paginado'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.servicios.solicitudes.index', [
            'solicitudes' => SolicitudServicio::query()
                ->with(['cliente', 'producto'])
                ->buscador($this->buscador)
                ->buscarEstado($this->buscarEstado)
                ->buscarPrioridad($this->buscarPrioridad)
                ->buscarTipoServicio($this->buscarTipoServicio)
                ->buscarActivo($this->buscarActivo)
                ->orderBy('fecha', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($this->paginado),
        ]);
    }

    public function activar($id)
    {
        SolicitudServicio::findOrFail($id)->update(['activo' => true, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Solicitud activada correctamente!');
    }

    public function inactivar($id)
    {
        SolicitudServicio::findOrFail($id)->update(['activo' => false, 'actualizadoPor' => Auth::id()]);
        session()->flash('success', 'Solicitud inactivada correctamente!');
    }

    public function eliminar($id)
    {
        $solicitud = SolicitudServicio::findOrFail($id);
        $solicitud->delete();
        session()->flash('success', 'Solicitud eliminada correctamente!');
    }

    public function verDetalles($id)
    {
        $this->solicitudSeleccionada = SolicitudServicio::with(['cliente', 'producto'])->findOrFail($id);
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->solicitudSeleccionada = null;
    }

    private function cargarDatosParaExportar()
    {
        return SolicitudServicio::query()
            ->with(['cliente', 'producto'])
            ->buscador($this->buscador)
            ->buscarEstado($this->buscarEstado)
            ->buscarPrioridad($this->buscarPrioridad)
            ->buscarTipoServicio($this->buscarTipoServicio)
            ->buscarActivo($this->buscarActivo)
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function excel()
    {
        $datos = $this->cargarDatosParaExportar();
        $encabezados = ['N° Solicitud', 'Fecha', 'Cliente', 'Equipo/Producto', 'Tipo Servicio', 'Prioridad', 'Estado'];

        return Excel::download(new ExcelSolicitudes($datos, $encabezados), 'Solicitudes_Servicio.xlsx');
    }

    public function pdf()
    {
        $nombre_archivo = 'Solicitudes_Servicio';
        $datos = $this->cargarDatosParaExportar();
        $encabezados = ['N° Solicitud', 'Fecha', 'Cliente', 'Equipo/Producto', 'Tipo Servicio', 'Prioridad', 'Estado'];

        return (new PdfSolicitudes($datos, $encabezados, $nombre_archivo))->download();
    }
}
