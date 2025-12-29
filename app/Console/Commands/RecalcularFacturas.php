<?php

namespace App\Console\Commands;

use App\Models\Ventas\Factura;
use Illuminate\Console\Command;

class RecalcularFacturas extends Command
{
    protected $signature = 'facturas:recalcular {factura_id?}';
    protected $description = 'Recalcula los totales de facturas con la nueva lógica de IVA incluido';

    public function handle()
    {
        $facturaId = $this->argument('factura_id');

        if ($facturaId) {
            $this->recalcularFactura($facturaId);
        } else {
            $facturas = Factura::all();
            $this->info("Recalculando {$facturas->count()} facturas...");

            foreach ($facturas as $factura) {
                $this->recalcularFactura($factura->id);
            }

            $this->info('✓ Todas las facturas han sido recalculadas');
        }
    }

    private function recalcularFactura($facturaId)
    {
        $factura = Factura::find($facturaId);

        if (!$factura) {
            $this->error("Factura {$facturaId} no encontrada");
            return;
        }

        // Recalcular totales de la factura
        $factura->calcularTotales();

        $this->info("✓ Factura #{$facturaId} ({$factura->numero_timbrado}-{$factura->numero_factura}) recalculada - Total: ₲ " . number_format($factura->total, 0, ',', '.'));
    }
}
