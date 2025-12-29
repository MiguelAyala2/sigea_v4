<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\Ventas\PedidoCliente;

class PedidoClienteController extends Controller
{
    /**
     * Muestra el listado de pedidos de clientes
     */
    public function index()
    {
        $pedidos = PedidoCliente::with(['cliente', 'vendedor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ventas.pedidos.historial', compact('pedidos'));
    }

    /**
     * Muestra el formulario para crear un nuevo pedido
     */
    public function create()
    {
        return view('ventas.pedidos.registrar');
    }

    /**
     * Muestra los detalles de un pedido específico
     */
    public function show(PedidoCliente $pedido)
    {
        $pedido->load(['cliente', 'vendedor', 'detalles.producto']);

        return view('ventas.pedidos.show', compact('pedido'));
    }

    /**
     * Muestra el formulario para editar un pedido existente
     */
    public function edit(PedidoCliente $pedido)
    {
        return view('ventas.pedidos.editar', compact('pedido'));
    }

    /**
     * Confirma el pedido
     */
    public function confirmar(PedidoCliente $pedido)
    {
        try {
            $pedido->confirmar();

            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('success', 'Pedido confirmado correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('error', 'Error al confirmar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Marca el pedido como en preparación
     */
    public function preparar(PedidoCliente $pedido)
    {
        try {
            $pedido->prepararPedido();

            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('success', 'Pedido marcado como en preparación');
        } catch (\Exception $e) {
            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('error', 'Error al preparar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Marca el pedido como listo para entregar
     */
    public function listoEntregar(PedidoCliente $pedido)
    {
        try {
            $pedido->listoParaEntregar();

            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('success', 'Pedido marcado como listo para entregar');
        } catch (\Exception $e) {
            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('error', 'Error al marcar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Cancela el pedido
     */
    public function cancelar(PedidoCliente $pedido)
    {
        try {
            $pedido->cancelar();

            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('success', 'Pedido cancelado correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('error', 'Error al cancelar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Anula el pedido
     */
    public function anular(PedidoCliente $pedido)
    {
        try {
            $pedido->anular();

            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('success', 'Pedido anulado correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->route('ventas.pedidos.show', $pedido)
                ->with('error', 'Error al anular el pedido: ' . $e->getMessage());
        }
    }
}
