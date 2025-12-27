<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use App\Models\Servicios\Reclamo;
use App\Models\Servicios\Cliente;
use App\Models\Servicios\OrdenServicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReclamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('servicios.reclamos.seguimiento');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $ordenes = OrdenServicio::with(['presupuesto.diagnostico.recepcion.solicitud.cliente'])
            ->orderBy('created_at', 'desc')
            ->get();
        $responsables = User::orderBy('name')->get();

        return view('servicios.reclamos.registrar', compact('clientes', 'ordenes', 'responsables'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Cliente::find($value)) {
                        $fail('El cliente seleccionado no existe.');
                    }
                }
            ],
            'orden_servicio_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !OrdenServicio::find($value)) {
                        $fail('La orden de servicio seleccionada no existe.');
                    }
                }
            ],
            'tipo_reclamo' => 'required|in:calidad_servicio,demora_entrega,falla_post_servicio,atencion_cliente,costo_facturacion,otro',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'fecha_reclamo' => 'required|date',
            'descripcion' => 'required|string',
            'responsable_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !User::find($value)) {
                        $fail('El responsable seleccionado no existe.');
                    }
                }
            ],
            'canal_recepcion' => 'required|in:presencial,telefono,email,whatsapp,web',
        ]);

        $validated['codigo'] = Reclamo::generarCodigo();
        $validated['creadoPor'] = Auth::id();

        $reclamo = Reclamo::create($validated);

        return redirect()->route('servicios.reclamos.index')
            ->with('success', 'Reclamo registrado exitosamente con código: ' . $reclamo->codigo);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reclamo $reclamo)
    {
        $reclamo->load(['cliente', 'ordenServicio', 'responsable', 'creador']);
        return view('servicios.reclamos.show', compact('reclamo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reclamo $reclamo)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $ordenes = OrdenServicio::with(['presupuesto.diagnostico.recepcion.solicitud.cliente'])
            ->orderBy('created_at', 'desc')
            ->get();
        $responsables = User::orderBy('name')->get();

        return view('servicios.reclamos.edit', compact('reclamo', 'clientes', 'ordenes', 'responsables'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reclamo $reclamo)
    {
        $validated = $request->validate([
            'cliente_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Cliente::find($value)) {
                        $fail('El cliente seleccionado no existe.');
                    }
                }
            ],
            'orden_servicio_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !OrdenServicio::find($value)) {
                        $fail('La orden de servicio seleccionada no existe.');
                    }
                }
            ],
            'tipo_reclamo' => 'required|in:calidad_servicio,demora_entrega,falla_post_servicio,atencion_cliente,costo_facturacion,otro',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'estado' => 'required|in:pendiente,en_revision,en_proceso,resuelto,cerrado,rechazado',
            'fecha_reclamo' => 'required|date',
            'descripcion' => 'required|string',
            'responsable_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !User::find($value)) {
                        $fail('El responsable seleccionado no existe.');
                    }
                }
            ],
            'canal_recepcion' => 'required|in:presencial,telefono,email,whatsapp,web',
            'solucion' => 'nullable|string',
            'fecha_resolucion' => 'nullable|date',
            'fecha_cierre' => 'nullable|date',
        ]);

        $validated['actualizadoPor'] = Auth::id();

        $reclamo->update($validated);

        return redirect()->route('servicios.reclamos.index')
            ->with('success', 'Reclamo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reclamo $reclamo)
    {
        $codigo = $reclamo->codigo;
        $reclamo->delete();

        return redirect()->route('servicios.reclamos.index')
            ->with('success', 'Reclamo ' . $codigo . ' eliminado exitosamente.');
    }
}
