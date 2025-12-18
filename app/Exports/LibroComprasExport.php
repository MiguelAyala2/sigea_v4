<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LibroComprasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $transacciones;
    protected $totales;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($transacciones, $totales, $fechaInicio, $fechaFin)
    {
        $this->transacciones = $transacciones;
        $this->totales = $totales;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        return $this->transacciones;
    }

    public function headings(): array
    {
        return [
            'TIPO DOCUMENTO',
            'NÚMERO',
            'FECHA',
            'PROVEEDOR',
            'RUC/DNI',
            'SUBTOTAL',
            'IVA',
            'TOTAL',
            'ESTADO',
        ];
    }

    public function map($transaccion): array
    {
        return [
            $transaccion['tipo'],
            $transaccion['numero'],
            \Carbon\Carbon::parse($transaccion['fecha'])->format('d/m/Y'),
            $transaccion['proveedor'],
            $transaccion['ruc'],
            $transaccion['subtotal'],
            $transaccion['impuesto'],
            $transaccion['total'],
            ucfirst($transaccion['estado']),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Encabezados (fila 1)
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '343a40'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Tipo
            'B' => 18,  // Número
            'C' => 12,  // Fecha
            'D' => 40,  // Proveedor
            'E' => 15,  // RUC
            'F' => 15,  // Subtotal
            'G' => 15,  // IVA
            'H' => 15,  // Total
            'I' => 12,  // Estado
        ];
    }

    public function title(): string
    {
        return 'Libro de Compras';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->transacciones->count() + 1; // +1 por el encabezado

                // Insertar filas de título al principio
                $sheet->insertNewRowBefore(1, 3);

                // Título del reporte
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'LIBRO DE COMPRAS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Período
                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'Período: ' .
                    \Carbon\Carbon::parse($this->fechaInicio)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::parse($this->fechaFin)->format('d/m/Y')
                );
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Espacio
                $sheet->getRowDimension(3)->setRowHeight(5);

                // Ajustar altura de fila de título
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Los encabezados ahora están en la fila 4
                $sheet->getStyle('A4:I4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '343a40'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Aplicar bordes a todas las celdas de datos
                $dataLastRow = $lastRow + 3; // +3 por las filas insertadas
                $sheet->getStyle('A4:I' . $dataLastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                // Formato numérico para columnas de montos (desde fila 5)
                $sheet->getStyle('F5:H' . $dataLastRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Alineación a la derecha para montos
                $sheet->getStyle('F5:H' . $dataLastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Fila de totales
                $totalRow = $dataLastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'TOTALES:');
                $sheet->mergeCells('A' . $totalRow . ':E' . $totalRow);
                $sheet->setCellValue('F' . $totalRow, $this->totales['subtotal']);
                $sheet->setCellValue('G' . $totalRow, $this->totales['impuesto']);
                $sheet->setCellValue('H' . $totalRow, $this->totales['total']);

                $sheet->getStyle('A' . $totalRow . ':I' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E9ECEF'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('A' . $totalRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('F' . $totalRow . ':H' . $totalRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                $sheet->getStyle('F' . $totalRow . ':H' . $totalRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
