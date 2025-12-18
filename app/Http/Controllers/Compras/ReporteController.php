<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\Compra;
use App\Models\Compras\CompraRecepcion;
use App\Models\Compras\Proveedor;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Reporte de compras por período
     */
    public function comprasPeriodo(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'proveedor_id' => 'nullable|exists:compras.proveedores,id',
            'sucursal_id' => 'nullable|exists:empresa.sucursales,id',
            'tipo_documento' => 'nullable|string',
            'estado' => 'nullable|string',
            'orden' => 'nullable|string',
        ]);

        $query = Compra::with('proveedor', 'sucursal', 'timbrado')
            ->whereBetween('fecha_emision', [$request->fecha_desde, $request->fecha_hasta]);

        // Aplicar filtros opcionales
        if ($request->proveedor_id) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->sucursal_id) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        if ($request->tipo_documento) {
            $query->where('tipo_documento', $request->tipo_documento);
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        } else {
            // Por defecto solo mostrar aprobadas si no se especifica estado
            $query->where('estado', 'APROBADO');
        }

        // Aplicar ordenamiento
        switch ($request->orden) {
            case 'fecha_asc':
                $query->orderBy('fecha_emision', 'asc');
                break;
            case 'fecha_desc':
                $query->orderBy('fecha_emision', 'desc');
                break;
            case 'monto_asc':
                $query->orderBy('total', 'asc');
                break;
            case 'monto_desc':
                $query->orderBy('total', 'desc');
                break;
            case 'proveedor':
                $query->join('compras.proveedores', 'compras.compras.proveedor_id', '=', 'compras.proveedores.id')
                    ->orderBy('compras.proveedores.razon_social', 'asc')
                    ->select('compras.compras.*');
                break;
            default:
                $query->orderBy('fecha_emision', 'asc');
        }

        $compras = $query->get();

        return view('compras.reportes.compras-periodo', [
            'compras' => $compras,
            'fecha_desde' => $request->fecha_desde,
            'fecha_hasta' => $request->fecha_hasta,
            'proveedor_id' => $request->proveedor_id,
            'sucursal_id' => $request->sucursal_id,
            'tipo_documento' => $request->tipo_documento,
            'estado' => $request->estado,
            'orden' => $request->orden,
        ]);
    }

    /**
     * Reporte de recepciones vs compras
     */
    public function recepcionesVsCompras(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        $compras = Compra::with(['recepciones', 'proveedor'])
            ->whereBetween('fecha_emision', [$request->fecha_desde, $request->fecha_hasta])
            ->where('estado', 'APROBADO')
            ->orderBy('fecha_emision')
            ->get()
            ->map(function ($compra) {
                return [
                    'compra' => $compra,
                    'recepciones_count' => $compra->recepciones->count(),
                    'recepciones_completas' => $compra->recepciones->where('estado', 'COMPLETA')->count(),
                    'porcentaje_recibido' => $compra->porcentaje_recibido,
                ];
            });

        return view('compras.reportes.recepciones-vs-compras', [
            'compras' => $compras,
            'fecha_desde' => $request->fecha_desde,
            'fecha_hasta' => $request->fecha_hasta,
        ]);
    }

    /**
     * Exportar libro de compras a Excel
     */
    public function exportarLibroCompras(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        // Aquí implementarías la lógica de exportación a Excel
        // usando Maatwebsite/Laravel-Excel
        
        return back()->with('success', 'Exportación programada. Recibirá un correo cuando esté lista.');
    }

    /**
     * Exportar análisis de proveedores a Excel
     */
    public function exportarAnalisisProveedores(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        // Lógica de exportación
        return back()->with('success', 'Exportación programada. Recibirá un correo cuando esté lista.');
    }

    /**
     * Generar reporte de proveedores con más compras
     */
    public function topProveedores(Request $request)
    {
        $limit = $request->get('limit', 10);
        $periodo = $request->get('periodo', 'mes');

        switch ($periodo) {
            case 'mes':
                $fechaInicio = now()->startOfMonth();
                $fechaFin = now()->endOfMonth();
                break;
            case 'trimestre':
                $fechaInicio = now()->startOfQuarter();
                $fechaFin = now()->endOfQuarter();
                break;
            case 'año':
                $fechaInicio = now()->startOfYear();
                $fechaFin = now()->endOfYear();
                break;
            default:
                $fechaInicio = now()->subMonth();
                $fechaFin = now();
        }

        $proveedores = Proveedor::withCount(['compras' => function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
                  ->where('estado', 'APROBADO');
        }])
        ->withSum(['compras' => function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
                  ->where('estado', 'APROBADO');
        }], 'total')
        ->orderByDesc('compras_sum_total')
        ->take($limit)
        ->get();

        return response()->json([
            'proveedores' => $proveedores,
            'periodo' => $periodo,
            'fecha_inicio' => $fechaInicio->format('d/m/Y'),
            'fecha_fin' => $fechaFin->format('d/m/Y'),
        ]);
    }
}