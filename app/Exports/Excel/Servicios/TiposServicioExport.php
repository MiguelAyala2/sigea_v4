<?php

namespace App\Exports\Excel\Servicios;

use App\Models\Servicios\TipoServicio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TiposServicioExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filtros;

    public function __construct($filtros = [])
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = TipoServicio::query();

        if (!empty($this->filtros['buscar'])) {
            $query->buscador($this->filtros['buscar']);
        }

        if (isset($this->filtros['activo']) && $this->filtros['activo'] !== '') {
            $query->buscarActivo($this->filtros['activo']);
        }

        return $query->orderBy('codigo', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'CÓDIGO',
            'DESCRIPCIÓN',
            'COSTO (₲)',
            'ESTADO',
        ];
    }

    public function map($tipoServicio): array
    {
        return [
            $tipoServicio->codigo,
            $tipoServicio->descripcion,
            number_format($tipoServicio->costo, 0, ',', '.'),
            $tipoServicio->activo ? 'ACTIVO' : 'INACTIVO',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Tipos de Servicio';
    }
}
