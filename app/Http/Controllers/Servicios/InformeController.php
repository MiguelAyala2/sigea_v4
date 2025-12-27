<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios\SolicitudServicio;
use App\Models\Servicios\Presupuesto;
use App\Models\Servicios\OrdenServicio;
use App\Models\Servicios\Reclamo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InformeController extends Controller
{
    public function index()
    {
        return view('servicios.informes.index');
    }

    public function solicitudes(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'formato' => 'required|in:pdf,excel',
        ]);

        $solicitudes = SolicitudServicio::with(['cliente', 'producto', 'creador'])
            ->whereBetween('fecha', [$request->fecha_desde, $request->fecha_hasta])
            ->orderBy('fecha', 'desc')
            ->get();

        if ($request->formato === 'pdf') {
            return $this->exportarSolicitudesPDF($solicitudes, $request->fecha_desde, $request->fecha_hasta);
        } else {
            return $this->exportarSolicitudesExcel($solicitudes, $request->fecha_desde, $request->fecha_hasta);
        }
    }

    public function presupuestos(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'formato' => 'required|in:pdf,excel',
        ]);

        $presupuestos = Presupuesto::with(['diagnostico.recepcion.solicitud.cliente', 'creador'])
            ->whereBetween('fecha_presupuesto', [$request->fecha_desde, $request->fecha_hasta])
            ->orderBy('fecha_presupuesto', 'desc')
            ->get();

        if ($request->formato === 'pdf') {
            return $this->exportarPresupuestosPDF($presupuestos, $request->fecha_desde, $request->fecha_hasta);
        } else {
            return $this->exportarPresupuestosExcel($presupuestos, $request->fecha_desde, $request->fecha_hasta);
        }
    }

    public function ordenes(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'formato' => 'required|in:pdf,excel',
        ]);

        $ordenes = OrdenServicio::with(['presupuesto.diagnostico.recepcion.solicitud.cliente', 'creador'])
            ->whereBetween('fecha_orden', [$request->fecha_desde, $request->fecha_hasta])
            ->orderBy('fecha_orden', 'desc')
            ->get();

        if ($request->formato === 'pdf') {
            return $this->exportarOrdenesPDF($ordenes, $request->fecha_desde, $request->fecha_hasta);
        } else {
            return $this->exportarOrdenesExcel($ordenes, $request->fecha_desde, $request->fecha_hasta);
        }
    }

    public function reclamos(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'formato' => 'required|in:pdf,excel',
        ]);

        $reclamos = Reclamo::with(['cliente', 'ordenServicio', 'responsable', 'creador'])
            ->whereBetween('fecha_reclamo', [$request->fecha_desde, $request->fecha_hasta])
            ->orderBy('fecha_reclamo', 'desc')
            ->get();

        if ($request->formato === 'pdf') {
            return $this->exportarReclamosPDF($reclamos, $request->fecha_desde, $request->fecha_hasta);
        } else {
            return $this->exportarReclamosExcel($reclamos, $request->fecha_desde, $request->fecha_hasta);
        }
    }

    // ========== EXPORTAR SOLICITUDES ==========
    private function exportarSolicitudesPDF($solicitudes, $fechaDesde, $fechaHasta)
    {
        $pdf = PDF::loadView('servicios.informes.pdf.solicitudes', [
            'solicitudes' => $solicitudes,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]);

        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('solicitudes_' . $fechaDesde . '_' . $fechaHasta . '.pdf');
    }

    private function exportarSolicitudesExcel($solicitudes, $fechaDesde, $fechaHasta)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->setCellValue('A1', 'INFORME DE SOLICITUDES DE SERVICIO');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Período
        $sheet->setCellValue('A2', 'Período: ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Encabezados
        $headers = ['Código', 'Fecha', 'Cliente', 'Producto', 'Tipo Servicio', 'Estado', 'Prioridad', 'Creado Por'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4CAF50');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Datos
        $row = 5;
        foreach ($solicitudes as $solicitud) {
            $sheet->fromArray([
                $solicitud->numero_solicitud,
                $solicitud->fecha->format('d/m/Y'),
                $solicitud->cliente->nombre ?? 'N/A',
                $solicitud->producto->nombre ?? 'N/A',
                $solicitud->tipo_servicio,
                $solicitud->estado,
                $solicitud->prioridad,
                $solicitud->creador->name ?? 'N/A',
            ], null, 'A' . $row);
            $row++;
        }

        // Ajustar anchos
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Bordes
        $sheet->getStyle('A4:H' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'solicitudes_' . $fechaDesde . '_' . $fechaHasta . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    // ========== EXPORTAR PRESUPUESTOS ==========
    private function exportarPresupuestosPDF($presupuestos, $fechaDesde, $fechaHasta)
    {
        $pdf = PDF::loadView('servicios.informes.pdf.presupuestos', [
            'presupuestos' => $presupuestos,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]);

        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('presupuestos_' . $fechaDesde . '_' . $fechaHasta . '.pdf');
    }

    private function exportarPresupuestosExcel($presupuestos, $fechaDesde, $fechaHasta)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'INFORME DE PRESUPUESTOS');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Período: ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['Código', 'Fecha', 'Cliente', 'Subtotal Servicios', 'Subtotal Repuestos', 'Descuento', 'Total', 'Estado'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2196F3');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setARGB('FFFFFFFF');

        $row = 5;
        foreach ($presupuestos as $presupuesto) {
            $totalDescuentos = ($presupuesto->descuento_promocion ?? 0) + ($presupuesto->descuento_descuento ?? 0);
            $sheet->fromArray([
                $presupuesto->codigo,
                $presupuesto->fecha_presupuesto->format('d/m/Y'),
                $presupuesto->cliente ?? 'N/A',
                number_format($presupuesto->total_servicios ?? 0, 0, ',', '.'),
                number_format($presupuesto->total_repuestos ?? 0, 0, ',', '.'),
                number_format($totalDescuentos, 0, ',', '.'),
                number_format($presupuesto->monto_total ?? 0, 0, ',', '.'),
                $presupuesto->estado,
            ], null, 'A' . $row);
            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A4:H' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'presupuestos_' . $fechaDesde . '_' . $fechaHasta . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    // ========== EXPORTAR ÓRDENES DE SERVICIO ==========
    private function exportarOrdenesPDF($ordenes, $fechaDesde, $fechaHasta)
    {
        $pdf = PDF::loadView('servicios.informes.pdf.ordenes', [
            'ordenes' => $ordenes,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]);

        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('ordenes_servicio_' . $fechaDesde . '_' . $fechaHasta . '.pdf');
    }

    private function exportarOrdenesExcel($ordenes, $fechaDesde, $fechaHasta)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'INFORME DE ÓRDENES DE SERVICIO');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Período: ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)));
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['Código', 'Fecha', 'Cliente', 'Equipo', 'Total Servicios', 'Total Repuestos', 'Total', 'Estado', 'Creado Por'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:I4')->getFont()->setBold(true);
        $sheet->getStyle('A4:I4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF9800');
        $sheet->getStyle('A4:I4')->getFont()->getColor()->setARGB('FFFFFFFF');

        $row = 5;
        foreach ($ordenes as $orden) {
            $sheet->fromArray([
                $orden->codigo,
                $orden->fecha_orden->format('d/m/Y'),
                $orden->cliente,
                $orden->equipo,
                number_format($orden->presupuesto->total_servicios ?? 0, 0, ',', '.'),
                number_format($orden->presupuesto->total_repuestos ?? 0, 0, ',', '.'),
                number_format($orden->presupuesto->monto_total ?? 0, 0, ',', '.'),
                $orden->estado,
                $orden->creador->name ?? 'N/A',
            ], null, 'A' . $row);
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A4:I' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'ordenes_servicio_' . $fechaDesde . '_' . $fechaHasta . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    // ========== EXPORTAR RECLAMOS ==========
    private function exportarReclamosPDF($reclamos, $fechaDesde, $fechaHasta)
    {
        $pdf = PDF::loadView('servicios.informes.pdf.reclamos', [
            'reclamos' => $reclamos,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]);

        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('reclamos_' . $fechaDesde . '_' . $fechaHasta . '.pdf');
    }

    private function exportarReclamosExcel($reclamos, $fechaDesde, $fechaHasta)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'INFORME DE RECLAMOS DE CLIENTES');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Período: ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta)));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['Código', 'Fecha', 'Cliente', 'Tipo', 'Prioridad', 'Estado', 'Responsable', 'Orden Relacionada'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF44336');
        $sheet->getStyle('A4:H4')->getFont()->getColor()->setARGB('FFFFFFFF');

        $row = 5;
        foreach ($reclamos as $reclamo) {
            $sheet->fromArray([
                $reclamo->codigo,
                $reclamo->fecha_reclamo->format('d/m/Y'),
                $reclamo->cliente->nombre ?? 'N/A',
                $reclamo->tipo_reclamo_text,
                $reclamo->prioridad,
                $reclamo->estado,
                $reclamo->responsable->name ?? 'Sin asignar',
                $reclamo->ordenServicio->codigo ?? 'N/A',
            ], null, 'A' . $row);
            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A4:H' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $writer = new Xlsx($spreadsheet);
        $filename = 'reclamos_' . $fechaDesde . '_' . $fechaHasta . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
