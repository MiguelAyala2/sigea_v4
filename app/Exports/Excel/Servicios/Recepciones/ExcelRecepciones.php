<?php

namespace App\Exports\Excel\Servicios\Recepciones;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExcelRecepciones implements FromCollection, WithHeadings, WithMapping
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

    public function collection()
    {
        return $this->datos;
    }

    public function headings(): array
    {
        return $this->encabezados;
    }

    public function map($recepcion): array
    {
        $estadoRecepcion = match($recepcion->estado_recepcion) {
            'bueno' => 'Bueno',
            'regular' => 'Regular',
            'malo' => 'Malo',
            default => 'S/D',
        };

        $estado = match($recepcion->estado) {
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'completado' => 'Completado',
            default => 'S/D',
        };

        return [
            $recepcion->numero_recepcion,
            $recepcion->fecha_recepcion->format('d/m/Y'),
            $recepcion->solicitud->numero_solicitud ?? 'S/D',
            $recepcion->cliente->nombre ?? 'S/D',
            $recepcion->producto->nombre ?? 'S/D',
            $recepcion->tipo_equipo ?? 'S/D',
            $recepcion->marca ?? 'S/D',
            $recepcion->modelo ?? 'S/D',
            $recepcion->numero_serie ?? 'S/D',
            $estadoRecepcion,
            $estado,
            $recepcion->descripcion_problema ?? '',
            $recepcion->activo ? 'Activo' : 'Inactivo',
        ];
    }
}
