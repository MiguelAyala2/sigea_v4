<?php

namespace App\Exports;

use App\Models\Empresa\Empresa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DashboardComprasExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function collection()
    {
        $rows = collect();

        // Pedidos de Compra
        $rows->push([
            'PEDIDOS DE COMPRA',
            '',
            ''
        ]);
        $rows->push([
            'Pendientes',
            $this->datos['pedidos']['pendientes']['cantidad'],
            'Gs. ' . number_format($this->datos['pedidos']['pendientes']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Aprobados',
            $this->datos['pedidos']['aprobados']['cantidad'],
            'Gs. ' . number_format($this->datos['pedidos']['aprobados']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Rechazados',
            $this->datos['pedidos']['rechazados']['cantidad'],
            'Gs. ' . number_format($this->datos['pedidos']['rechazados']['total'], 0, ',', '.')
        ]);
        $rows->push(['', '', '']); // Espacio

        // Presupuestos
        $rows->push([
            'PRESUPUESTOS',
            '',
            ''
        ]);
        $rows->push([
            'Pendientes',
            $this->datos['presupuestos']['pendientes']['cantidad'],
            'Gs. ' . number_format($this->datos['presupuestos']['pendientes']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Aprobados',
            $this->datos['presupuestos']['aprobados']['cantidad'],
            'Gs. ' . number_format($this->datos['presupuestos']['aprobados']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Rechazados',
            $this->datos['presupuestos']['rechazados']['cantidad'],
            'Gs. ' . number_format($this->datos['presupuestos']['rechazados']['total'], 0, ',', '.')
        ]);
        $rows->push(['', '', '']); // Espacio

        // Órdenes de Compra
        $rows->push([
            'ÓRDENES DE COMPRA',
            '',
            ''
        ]);
        $rows->push([
            'Pendientes',
            $this->datos['ordenes']['pendientes']['cantidad'],
            'Gs. ' . number_format($this->datos['ordenes']['pendientes']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Aprobados',
            $this->datos['ordenes']['aprobados']['cantidad'],
            'Gs. ' . number_format($this->datos['ordenes']['aprobados']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Rechazados',
            $this->datos['ordenes']['rechazados']['cantidad'],
            'Gs. ' . number_format($this->datos['ordenes']['rechazados']['total'], 0, ',', '.')
        ]);
        $rows->push(['', '', '']); // Espacio

        // Compras/Facturas
        $rows->push([
            'COMPRAS/FACTURAS',
            '',
            ''
        ]);
        $rows->push([
            'Pendientes',
            $this->datos['compras']['pendientes']['cantidad'],
            'Gs. ' . number_format($this->datos['compras']['pendientes']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Aprobados',
            $this->datos['compras']['aprobados']['cantidad'],
            'Gs. ' . number_format($this->datos['compras']['aprobados']['total'], 0, ',', '.')
        ]);
        $rows->push([
            'Rechazados',
            $this->datos['compras']['rechazados']['cantidad'],
            'Gs. ' . number_format($this->datos['compras']['rechazados']['total'], 0, ',', '.')
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            ['DASHBOARD DE COMPRAS - ' . now()->format('d/m/Y H:i')],
            ['Estado', 'Cantidad', 'Total']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Título principal
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            // Encabezados de columnas
            2 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ],
        ];
    }

    public function title(): string
    {
        return 'Dashboard Compras';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Ajustar anchos de columna
                $event->sheet->getColumnDimension('A')->setWidth(30);
                $event->sheet->getColumnDimension('B')->setWidth(15);
                $event->sheet->getColumnDimension('C')->setWidth(25);

                // Fusionar celdas del título
                $event->sheet->mergeCells('A1:C1');

                // Obtener empresa para el encabezado
                $empresa = Empresa::first();
                if ($empresa) {
                    $event->sheet->setCellValue('A1', 'DASHBOARD DE COMPRAS - ' . $empresa->razon_social);
                }

                // Aplicar bordes a toda la tabla
                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('A2:C' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                // Títulos de secciones en negrita y con fondo
                $sectionRows = [3, 8, 13, 18]; // Filas con títulos de secciones
                foreach ($sectionRows as $row) {
                    $event->sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D6EAF8']
                        ]
                    ]);
                    $event->sheet->mergeCells('A' . $row . ':C' . $row);
                }
            }
        ];
    }
}
