<?php

namespace App\Exports\Excel\Servicios\Clientes;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExcelClientes implements FromCollection, WithHeadings, WithMapping
{
    public $datos, $encabezados;

    public function __construct($datos = null, $encabezados = null)
    {
        $this->datos = $datos;
        $this->encabezados = $encabezados;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->datos;
    }

    public function headings(): array
    {
        return $this->encabezados;
    }

    public function map($cliente): array
    {
        $tipo = $cliente->tipo_cliente === 'fisica' ? 'Persona Física' : 'Persona Jurídica';
        $estado = $cliente->activo ? 'Activo' : 'Inactivo';

        return [
            $tipo,
            $cliente->documento ?? 'S/D',
            $cliente->nombre ?? 'S/D',
            $cliente->telefono ?? 'S/D',
            $cliente->celular ?? 'S/D',
            $cliente->email ?? 'S/D',
            $cliente->direccion ?? 'S/D',
            $estado,
        ];
    }
}
