<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compras\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of proveedores
     */
    public function index()
    {
        $this->authorize('Proveedores Ver');

        return view('compras.proveedores.index');
    }

    /**
     * Show the form for creating a new proveedor
     */
    public function create()
    {
        $this->authorize('Proveedores Crear');

        return view('compras.proveedores.create');
    }

    /**
     * Show the form for editing the specified proveedor
     */
    public function edit(Proveedor $proveedor)
    {
        $this->authorize('Proveedores Editar');

        return view('compras.proveedores.edit', compact('proveedor'));
    }

    /**
     * Display the specified proveedor
     */
    public function show(Proveedor $proveedor)
    {
        $this->authorize('Proveedores Ver');

        $proveedor->load(['compras' => function ($query) {
            $query->latest('fecha_emision')->take(10);
        }]);

        return view('compras.proveedores.show', compact('proveedor'));
    }

    /**
     * Remove the specified proveedor from storage
     */
    public function destroy(Proveedor $proveedor)
    {
        $this->authorize('Proveedores Eliminar');

        try {
            $proveedor->delete();

            return redirect()->route('compras.proveedores.index')
                ->with('success', 'Proveedor eliminado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Activate or deactivate proveedor
     */
    public function toggleActivo(Proveedor $proveedor)
    {
        $this->authorize('Proveedores Editar');

        $proveedor->update(['activo' => !$proveedor->activo]);

        $estado = $proveedor->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Proveedor {$estado} correctamente");
    }
}
