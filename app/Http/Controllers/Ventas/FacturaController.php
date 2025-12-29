<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\Factura;
use App\Models\Ventas\PedidoCliente;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    /**
     * Muestra el listado de facturas
     */
    public function index()
    {
        return view('ventas.facturas.index');
    }

    /**
     * Muestra el formulario para crear una nueva factura
     */
    public function create(Request $request)
    {
        $pedidoId = $request->get('pedido_id');
        $cotizacionId = $request->get('cotizacion_id');

        return view('ventas.facturas.crear', [
            'pedidoId' => $pedidoId,
            'cotizacionId' => $cotizacionId,
        ]);
    }

    /**
     * Muestra el detalle de una factura
     */
    public function show(Factura $factura)
    {
        $factura->load([
            'cliente',
            'pedidoCliente',
            'timbrado',
            'puntoExpedicion',
            'sucursal',
            'deposito',
            'vendedor',
            'detalles.producto',
            'servicios',
            'formasPago',
            'creadoPor',
            'actualizadoPor',
            'emitidoPor',
            'anuladoPor',
        ]);

        return view('ventas.facturas.show', compact('factura'));
    }

    /**
     * Muestra el formulario para editar una factura
     */
    public function edit(Factura $factura)
    {
        // Solo se pueden editar facturas en estado BORRADOR
        if ($factura->estado !== 'BORRADOR') {
            return redirect()->route('ventas.facturas.show', $factura)
                ->with('error', 'Solo se pueden editar facturas en estado BORRADOR.');
        }

        $factura->load([
            'cliente',
            'detalles.producto',
            'formasPago',
        ]);

        return view('ventas.facturas.editar', compact('factura'));
    }

    /**
     * Emite una factura
     */
    public function emitir(Factura $factura)
    {
        try {
            $factura->emitir(auth()->user()->id);

            return redirect()->route('ventas.facturas.show', $factura)
                ->with('success', 'Factura emitida correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Anula una factura
     */
    public function anular(Request $request, Factura $factura)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:10',
        ]);

        try {
            $factura->anular($request->motivo_anulacion, auth()->user()->id);

            return redirect()->route('ventas.facturas.show', $factura)
                ->with('success', 'Factura anulada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Genera el PDF de la factura
     */
    public function pdf(Factura $factura, Request $request)
    {
        try {
            // Si se pasa el parámetro 'download', descarga el PDF
            if ($request->has('download')) {
                return $factura->descargarPDF();
            }

            // Por defecto, muestra el PDF en el navegador
            return $factura->verPDF();
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Envía la factura por email
     */
    public function enviarEmail(Factura $factura)
    {
        // TODO: Implementar envío por email
        return back()->with('info', 'Envío por email en desarrollo.');
    }

    /**
     * Envía la factura electrónica a SET
     */
    public function enviarSET(Factura $factura)
    {
        try {
            if (!$factura->es_electronica) {
                return back()->with('error', 'La factura no es electrónica.');
            }

            $factura->enviarSET();

            return back()->with('success', 'Factura enviada a SET correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
