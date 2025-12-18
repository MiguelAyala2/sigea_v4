<?php

namespace App\Exports;

use App\Models\Compras\Compra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CuentasPorPagarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Compra::with(['proveedor', 'creadoPorUsuario'])
            ->where('estado', 'APROBADO')
            ->orderBy('fecha_emision', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N° Factura',
            'Timbrado',
            'Proveedor',
            'RUC',
            'Fecha Emisión',
            'Fecha Vencimiento',
            'Tipo Factura',
            'Condición Pago',
            'Subtotal',
            'IVA 10%',
            'IVA 5%',
            'Exenta',
            'Total',
            'Estado',
            'Creado Por',
            'Fecha Creación',
        ];
    }

    /**
     * @param mixed $compra
     * @return array
     */
    public function map($compra): array
    {
        $condicionPago = match($compra->condicion_pago) {
            'CONTADO' => 'Contado',
            '7_DIAS' => '7 Días',
            '15_DIAS' => '15 Días',
            '30_DIAS' => '30 Días',
            '60_DIAS' => '60 Días',
            '90_DIAS' => '90 Días',
            default => $compra->condicion_pago,
        };

        return [
            $compra->numero_factura,
            $compra->timbrado ?? 'N/A',
            $compra->proveedor->razon_social ?? 'N/A',
            $compra->proveedor->ruc ?? 'N/A',
            $compra->fecha_emision ? $compra->fecha_emision->format('d/m/Y') : '',
            $compra->fecha_vencimiento ? $compra->fecha_vencimiento->format('d/m/Y') : '',
            $compra->tipo_factura,
            $condicionPago,
            $compra->subtotal,
            $compra->iva_10,
            $compra->iva_5,
            $compra->exenta,
            $compra->total,
            $compra->estado,
            $compra->creadoPorUsuario->name ?? 'N/A',
            $compra->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // N° Factura
            'B' => 15,  // Timbrado
            'C' => 30,  // Proveedor
            'D' => 15,  // RUC
            'E' => 15,  // Fecha Emisión
            'F' => 15,  // Fecha Vencimiento
            'G' => 12,  // Tipo Factura
            'H' => 15,  // Condición Pago
            'I' => 15,  // Subtotal
            'J' => 12,  // IVA 10%
            'K' => 12,  // IVA 5%
            'L' => 12,  // Exenta
            'M' => 15,  // Total
            'N' => 12,  // Estado
            'O' => 20,  // Creado Por
            'P' => 18,  // Fecha Creación
        ];
    }
}
