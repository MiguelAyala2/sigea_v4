<?php

namespace App\Exports;

use App\Models\Compras\PedidoCompra;
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

class PedidosCompraExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    public function collection()
    {
        return PedidoCompra::with(['usuarioSolicitante'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function map($pedido): array
    {
        $tipo = match($pedido->tipo_pedido) {
            'NORMAL' => 'Normal',
            'URGENTE' => 'Urgente',
            'SERVICIO' => 'Servicio',
            'INSUMOS' => 'Insumos',
            default => $pedido->tipo_pedido
        };

        $estado = $pedido->estado == 'PENDIENTE_APROBACION' ? 'Pendiente' : ucfirst(strtolower($pedido->estado));

        return [
            $pedido->numero_pedido,
            $pedido->fecha_pedido ? $pedido->fecha_pedido->format('d/m/Y') : '-',
            $pedido->usuarioSolicitante->name ?? 'N/A',
            $tipo,
            ucfirst(strtolower($pedido->prioridad ?? 'Media')),
            $pedido->total_estimado ?? 0,
            $estado,
            $pedido->observaciones ?? '-',
        ];
    }

    public function headings(): array
    {
        $empresa = Empresa::first();
        $empresaNombre = $empresa ? $empresa->razon_social : 'SIGEA';

        return [
            ['LISTA DE PEDIDOS DE COMPRA - ' . $empresaNombre],
            ['Generado: ' . now()->format('d/m/Y H:i')],
            [],
            ['Número', 'Fecha', 'Solicitante', 'Tipo', 'Prioridad', 'Total Estimado (Gs.)', 'Estado', 'Observaciones']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Título principal
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '007BFF']
                ]
            ],
            // Fecha
            2 => [
                'font' => ['italic' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            // Encabezados de columnas
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0056B3']
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ]
            ],
        ];
    }

    public function title(): string
    {
        return 'Pedidos de Compra';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Ajustar anchos de columna
                $event->sheet->getColumnDimension('A')->setWidth(12);  // Número
                $event->sheet->getColumnDimension('B')->setWidth(12);  // Fecha
                $event->sheet->getColumnDimension('C')->setWidth(25);  // Solicitante
                $event->sheet->getColumnDimension('D')->setWidth(15);  // Tipo
                $event->sheet->getColumnDimension('E')->setWidth(12);  // Prioridad
                $event->sheet->getColumnDimension('F')->setWidth(20);  // Total
                $event->sheet->getColumnDimension('G')->setWidth(15);  // Estado
                $event->sheet->getColumnDimension('H')->setWidth(40);  // Observaciones

                // Fusionar celdas del título y fecha
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->mergeCells('A2:H2');

                // Aplicar bordes a toda la tabla de datos
                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('A4:H' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                    ]
                ]);

                // Formato de números para la columna Total
                $event->sheet->getStyle('F5:F' . $lastRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Alineación
                $event->sheet->getStyle('A4:H' . $lastRow)
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $event->sheet->getStyle('F5:F' . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $event->sheet->getStyle('G5:G' . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Agregar fila de total
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

                // Alternar colores de filas
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
