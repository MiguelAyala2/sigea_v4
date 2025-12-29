<?php

namespace App\Console\Commands;

use App\Models\Ventas\Factura;
use App\Models\Ventas\FacturaFormaPago;
use App\Models\Ventas\MovimientoCaja;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CorregirFactura11 extends Command
{
    protected $signature = 'factura:corregir-11';
    protected $description = 'Corrige la factura 11 que tiene error de cálculo IVA';

    public function handle()
    {
        DB::transaction(function () {
            // Obtener factura
            $factura = Factura::find(11);

            if (!$factura) {
                $this->error('Factura no encontrada');
                return 1;
            }

            $this->info('Factura encontrada: #' . $factura->numero_factura);
            $this->line('Total actual: ₲ ' . number_format($factura->total, 0, ',', '.'));

            // El sistema trabaja con IVA INCLUIDO
            // Subtotal de 800.000 YA incluye el IVA
            // IVA desglosado: 800.000 / 1.1 = 727.273 (base) + 72.727 (IVA)

            $subtotal = 800000; // IVA incluido
            $ivaMonto = round($subtotal - ($subtotal / 1.1)); // 72.727

            // Actualizar factura
            $factura->update([
                'subtotal' => $subtotal,
                'iva_10' => $ivaMonto,
                'total_iva' => $ivaMonto,
                'total' => $subtotal, // Total = Subtotal cuando IVA está incluido
            ]);

            $this->info('✓ Factura actualizada');
            $this->line('  Subtotal: ₲ ' . number_format($subtotal, 0, ',', '.') . ' (IVA incluido)');
            $this->line('  IVA 10%: ₲ ' . number_format($ivaMonto, 0, ',', '.') . ' (desglosado)');
            $this->line('  Total: ₲ ' . number_format($subtotal, 0, ',', '.'));

            // Actualizar forma de pago
            $formaPago = FacturaFormaPago::where('factura_id', $factura->id)->first();
            if ($formaPago) {
                $formaPago->update(['monto' => $subtotal]);
                $this->info('✓ Forma de pago actualizada a ₲ ' . number_format($subtotal, 0, ',', '.'));
            }

            // Actualizar movimiento de caja
            $movimiento = MovimientoCaja::where('venta_id', $factura->id)->first();
            if ($movimiento) {
                $movimiento->update(['monto' => $subtotal]);
                $this->info('✓ Movimiento de caja actualizado a ₲ ' . number_format($subtotal, 0, ',', '.'));
            }

            // Actualizar detalle
            $detalle = $factura->detalles()->first();
            if ($detalle) {
                $detalle->update([
                    'subtotal' => $subtotal,
                    'iva_monto' => $ivaMonto,
                    'total' => $subtotal,
                ]);
                $this->info('✓ Detalle actualizado');
            }
        });

        $this->newLine();
        $this->info('Corrección completada exitosamente');

        return 0;
    }
}
