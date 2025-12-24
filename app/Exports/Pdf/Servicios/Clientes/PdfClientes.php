<?php

namespace App\Exports\Pdf\Servicios\Clientes;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfClientes
{
    protected $datos;
    protected $encabezados;
    protected $nombre_archivo;

    public function __construct($datos, $encabezados, $nombre_archivo = 'Clientes')
    {
        $this->datos = $datos;
        $this->encabezados = $encabezados;
        $this->nombre_archivo = $nombre_archivo;
    }

    public function download()
    {
        $pdf = Pdf::loadView('servicios.clientes.pdf.pdf-clientes-listado', [
            'nombre_archivo' => $this->nombre_archivo,
            'datos' => $this->datos,
            'encabezados' => $this->encabezados
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $this->nombre_archivo . '.pdf');
    }
}
