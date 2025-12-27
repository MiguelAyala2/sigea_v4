<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios\OrdenServicio;
use App\Models\Empresa\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenServicioController extends Controller
{
    public function index()
    {
        return view('servicios.gestion.ordenes');
    }

    public function edit($id)
    {
        $orden = OrdenServicio::with([
            'presupuesto.diagnostico.recepcion.solicitud.cliente',
            'presupuesto.diagnostico.recepcion.producto',
            'presupuesto.diagnostico.tiposServicio.tipoServicio',
            'presupuesto.diagnostico.repuestos.producto',
            'tecnico'
        ])->findOrFail($id);

        return view('servicios.ordenes.edit', compact('orden'));
    }

    public function imprimirOrdenTrabajo($id)
    {
        $orden = OrdenServicio::with([
            'presupuesto.diagnostico.recepcion.solicitud.cliente',
            'presupuesto.diagnostico.recepcion.producto',
            'presupuesto.diagnostico.tiposServicio.tipoServicio',
            'presupuesto.diagnostico.repuestos.producto',
            'presupuesto.promocion',
            'presupuesto.descuento',
            'tecnico'
        ])->findOrFail($id);

        $empresa = Empresa::first();

        $pdf = PDF::loadView('servicios.ordenes.pdf-orden-trabajo', [
            'orden' => $orden,
            'empresa' => $empresa,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream('orden-trabajo-' . $orden->codigo . '.pdf');
    }

    public function imprimirContrato($id)
    {
        $orden = OrdenServicio::with([
            'presupuesto.diagnostico.recepcion.solicitud.cliente',
            'presupuesto.diagnostico.recepcion.producto',
            'presupuesto.diagnostico.tiposServicio.tipoServicio',
            'presupuesto.diagnostico.repuestos.producto',
            'presupuesto.promocion',
            'presupuesto.descuento',
            'tecnico'
        ])->findOrFail($id);

        $empresa = Empresa::first();

        $pdf = PDF::loadView('servicios.ordenes.pdf-contrato', [
            'orden' => $orden,
            'empresa' => $empresa,
            'fecha' => now()->format('d/m/Y H:i')
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->stream('contrato-servicio-' . $orden->codigo . '.pdf');
    }
}
