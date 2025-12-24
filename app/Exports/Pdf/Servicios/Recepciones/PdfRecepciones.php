<?php

namespace App\Exports\Pdf\Servicios\Recepciones;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfRecepciones
{
    protected $datos;
    protected $nombre_archivo;
    protected $encabezados;

    public function __construct($datos, $nombre_archivo, $encabezados)
    {
        $this->datos = $datos;
        $this->nombre_archivo = $nombre_archivo;
        $this->encabezados = $encabezados;
    }

    public function download()
    {
        $pdf = Pdf::loadView('servicios.recepciones.pdf.pdf-recepciones-listado', [
            'nombre_archivo' => $this->nombre_archivo,
            'datos' => $this->datos,
            'encabezados' => $this->encabezados
        ]);

        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $this->nombre_archivo . '.pdf');
    }
}
