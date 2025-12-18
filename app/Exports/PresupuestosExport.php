<?php

namespace App\Exports;

use App\Models\Compras\Presupuesto;
use App\Models\Empresa\Empresa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PresupuestosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    public function collection()
    {
        return Presupuesto::with(['proveedor', 'pedidoCompra', 'solicitadoPorUsuario'])
            ->orderBy('fecha_solicitud', 'desc')
            ->get();
    }

    public function map($presupuesto): array
    {
        $condicion = match($presupuesto->condicion_pago) {
            'CONTADO' => 'Contado',
            '7_DIAS' => '7 Días',
            '15_DIAS' => '15 Días',
            '30_DIAS' => '30 Días',
            '60_DIAS' => '60 Días',
            '90_DIAS' => '90 Días',
            default => $presupuesto->condicion_pago
        };

        return [
            $presupuesto->numero_presupuesto,
            $presupuesto->fecha_solicitud ? $presupuesto->fecha_solicitud->format('d/m/Y') : '-',
            $presupuesto->proveedor->razon_social ?? 'N/A',
            $condicion,
            $presupuesto->dias_entrega ?? '-',
            $presupuesto->total ?? 0,
            ucfirst(strtolower($presupuesto->estado)),
            $presupuesto->pedidoCompra->numero_pedido ?? '-',
        ];
    }

    public function headings(): array
    {
        $empresa = Empresa::first();
        $empresaNombre = $empresa ? $empresa->razon_social : 'SIGEA';

        return [
            ['LISTA DE PRESUPUESTOS - ' . $empresaNombre],
            ['Generado: ' . now()->format('d/m/Y H:i')],
            [],
            ['N° Presupuesto', 'Fecha', 'Proveedor', 'Condición Pago', 'Días Entrega', 'Total (Gs.)', 'Estado', 'N° Pedido']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '17A2B8']
                ]
            ],
            2 => [
                'font' => ['italic' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '138496']
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ]
            ],
        ];
    }

    public function title(): string
    {
        return 'Presupuestos';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setWidth(15);
                $event->sheet->getColumnDimension('B')->setWidth(12);
                $event->sheet->getColumnDimension('C')->setWidth(30);
                $event->sheet->getColumnDimension('D')->setWidth(15);
                $event->sheet->getColumnDimension('E')->setWidth(12);
                $event->sheet->getColumnDimension('F')->setWidth(20);
                $event->sheet->getColumnDimension('G')->setWidth(15);
                $event->sheet->getColumnDimension('H')->setWidth(15);

                $event->sheet->mergeCells('A1:H1');
                $event->sheet->mergeCells('A2:H2');

                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('A4:H' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                    ]
                ]);

                $event->sheet->getStyle('F5:F' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                $event->sheet->getStyle('A4:H' . $lastRow)
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $event->sheet->getStyle('E5:E' . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event->sheet->getStyle('F5:F' . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $event->sheet->getStyle('G5:G' . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $totalRow = $lastRow + 1;
                $event->sheet->setCellValue('A' . $totalRow, 'TOTAL GENERAL:');
                $event->sheet->mergeCells('A' . $totalRow . ':E' . $totalRow);
                $event->sheet->setCellValue('F' . $totalRow, '=SUM(F5:F' . $lastRow . ')');

                $event->sheet->getStyle('A' . $totalRow . ':H' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9ECEF']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                $event->sheet->getStyle('A' . $totalRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $event->sheet->getStyle('F' . $totalRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                for ($i = 5; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $event->sheet->getStyle('A' . $i . ':H' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8F9FA']
                            ]
                        ]);
                    }
                }
            }
        ];
    }
}
