<?php

namespace App\Exports\Excel\Servicios\Solicitudes;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExcelSolicitudes implements FromCollection, WithHeadings, WithMapping
{
    public $datos, $encabezados;

    public function __construct($datos = null, $encabezados = null)
    {
        $this->datos = $datos;
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

    public function map($solicitud): array
    {
        $tipoServicio = match($solicitud->tipo_servicio) {
            'mantenimiento' => 'Mantenimiento',
            'reparacion' => 'Reparación',
            'diagnostico' => 'Diagnóstico',
            default => 'S/D',
        };

        $prioridad = match($solicitud->prioridad) {
            'alta' => 'Alta',
            'media' => 'Media',
            'baja' => 'Baja',
            default => 'S/D',
        };

        $estado = match($solicitud->estado) {
            'completado' => 'Completado',
            'en_proceso' => 'En Proceso',
            'pendiente' => 'Pendiente',
            default => 'S/D',
        };

        return [
            $solicitud->numero_solicitud ?? 'S/D',
            $solicitud->fecha->format('d/m/Y') ?? 'S/D',
            $solicitud->cliente->nombre ?? 'S/D',
            $solicitud->producto->nombre ?? 'S/D',
            $tipoServicio,
            $prioridad,
            $estado,
        ];
    }
}
