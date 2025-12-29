<?php

namespace App\Console\Commands;

use App\Models\Ventas\PedidoCliente;
use App\Models\Ventas\PedidoClienteDetalle;
use Illuminate\Console\Command;

class RecalcularPedidos extends Command
{
    protected $signature = 'pedidos:recalcular {pedido_id?}';
    protected $description = 'Recalcula los totales de pedidos con la nueva lógica de IVA incluido';

    public function handle()
    {
        $pedidoId = $this->argument('pedido_id');

        if ($pedidoId) {
            $this->recalcularPedido($pedidoId);
        } else {
            $pedidos = PedidoCliente::all();
            $this->info("Recalculando {$pedidos->count()} pedidos...");

            foreach ($pedidos as $pedido) {
                $this->recalcularPedido($pedido->id);
            }

            $this->info('✓ Todos los pedidos han sido recalculados');
        }
    }

    private function recalcularPedido($pedidoId)
    {
        $pedido = PedidoCliente::find($pedidoId);

        if (!$pedido) {
            $this->error("Pedido {$pedidoId} no encontrado");
            return;
        }

        // Recalcular cada detalle
        foreach ($pedido->detalles as $detalle) {
            $detalle->calcularTotales();
            $detalle->save();
        }

        // Recalcular totales del pedido
        $pedido->calcularTotales();

        $this->info("✓ Pedido #{$pedidoId} recalculado - Total: ₲ " . number_format($pedido->total, 0, ',', '.'));
    }
}
