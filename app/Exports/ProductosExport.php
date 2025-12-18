<?php

namespace App\Exports;

use App\Models\Stock\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Producto::with(['categoria', 'marca', 'unidadMedida', 'precioActual'])
            ->orderBy('codigo')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Código',
            'Código Barras',
            'Código Fabricante',
            'Nombre',
            'Descripción',
            'Modelo',
            'Aplicación',
            'Tipo',
            'Origen',
            'Categoría',
            'Marca',
            'Unidad de Medida',
            'Precio Actual',
            'Permite Venta',
            'Permite Compra',
            'Maneja Stock',
            'Stock Mínimo',
            'Stock Máximo',
            'Estado',
            'Creado Por',
            'Fecha Creación',
        ];
    }

    /**
     * @param mixed $producto
     * @return array
     */
    public function map($producto): array
    {
        return [
            $producto->codigo,
            $producto->codigo_barras ?? 'N/A',
            $producto->codigo_fabricante ?? 'N/A',
            $producto->nombre,
            $producto->descripcion ?? '',
            $producto->modelo ?? '',
            $producto->aplicacion ?? '',
            $producto->tipo_texto,
            $producto->origen_texto,
            $producto->categoria->nombre ?? 'N/A',
            $producto->marca->nombre ?? 'N/A',
            $producto->unidadMedida->nombre ?? 'N/A',
            $producto->precioActual ? $producto->precioActual->precio_venta : 0,
            $producto->permite_venta ? 'Sí' : 'No',
            $producto->permite_compra ? 'Sí' : 'No',
            $producto->maneja_stock ? 'Sí' : 'No',
            $producto->stock_minimo,
            $producto->stock_maximo,
            $producto->activo ? 'Activo' : 'Inactivo',
            $producto->creadoPorUsuario->name ?? 'N/A',
            $producto->created_at->format('d/m/Y H:i'),
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
            'A' => 12,  // Código
            'B' => 15,  // Código Barras
            'C' => 18,  // Código Fabricante
            'D' => 35,  // Nombre
            'E' => 40,  // Descripción
            'F' => 15,  // Modelo
            'G' => 25,  // Aplicación
            'H' => 12,  // Tipo
            'I' => 12,  // Origen
            'J' => 20,  // Categoría
            'K' => 18,  // Marca
            'L' => 18,  // Unidad de Medida
            'M' => 15,  // Precio Actual
            'N' => 12,  // Permite Venta
            'O' => 12,  // Permite Compra
            'P' => 12,  // Maneja Stock
            'Q' => 12,  // Stock Mínimo
            'R' => 12,  // Stock Máximo
            'S' => 10,  // Estado
            'T' => 20,  // Creado Por
            'U' => 18,  // Fecha Creación
        ];
    }
}
