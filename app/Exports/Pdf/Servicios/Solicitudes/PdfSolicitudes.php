<?php

namespace App\Exports\Pdf\Servicios\Solicitudes;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfSolicitudes
{
    protected $datos;
    protected $encabezados;
    protected $nombre_archivo;

    public function __construct($datos, $encabezados, $nombre_archivo = 'Solicitudes')
    {
        $this->datos = $datos;
        $this->encabezados = $encabezados;
        $this->nombre_archivo = $nombre_archivo;
    }

    public function download()
    {
        $pdf = Pdf::loadView('servicios.solicitudes.pdf.pdf-solicitudes-listado', [
            'nombre_archivo' => $this->nombre_archivo,
            'datos' => $this->datos,
            'encabezados' => $this->encabezados
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $this->nombre_archivo . '.pdf');
    }
}
