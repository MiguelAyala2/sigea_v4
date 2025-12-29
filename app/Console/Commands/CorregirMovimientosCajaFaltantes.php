<?php

namespace App\Console\Commands;

use App\Models\Ventas\Factura;
use App\Models\Ventas\FacturaFormaPago;
use App\Models\Ventas\MovimientoCaja;
use App\Models\Ventas\AperturaCaja;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CorregirMovimientosCajaFaltantes extends Command
{
    protected $signature = 'factura:corregir-movimientos-caja';
    protected $description = 'Corrige facturas CONTADO que no tienen movimientos de caja ni formas de pago';

    public function handle()
    {
        $this->info('Buscando facturas CONTADO sin movimientos de caja...');

        // Buscar facturas CONTADO que no tienen movimientos de caja
        $facturas = Factura::where('condicion_pago', 'CONTADO')
            ->whereDoesntHave('movimientosCaja')
            ->with('formasPago')
            ->get();

        $this->line("Encontradas: {$facturas->count()} facturas");

        if ($facturas->count() === 0) {
            $this->info('No hay facturas para corregir.');
            return 0;
        }

        $corregidas = 0;
        $errores = 0;

        foreach ($facturas as $factura) {
            $this->newLine();
            $this->line("Procesando factura ID: {$factura->id} | {$factura->numero_timbrado}-{$factura->numero_factura}");
            $this->line("  Total: ₲ " . number_format($factura->total, 0, ',', '.'));

            try {
                DB::beginTransaction();

                // Si no tiene formas de pago, crear EFECTIVO por el total
                if ($factura->formasPago->count() === 0) {
                    $this->line("  → Creando forma de pago EFECTIVO...");
                    FacturaFormaPago::create([
                        'factura_id' => $factura->id,
                        'forma_pago' => 'EFECTIVO',
                        'monto' => $factura->total,
                        'referencia' => null,
                    ]);
                }

                // Obtener apertura activa o la más reciente para el punto de expedición
                $apertura = AperturaCaja::obtenerAperturaActual($factura->punto_expedicion_id);

                if (!$apertura) {
                    // Si no hay apertura actual, buscar la más reciente
                    $apertura = AperturaCaja::where('punto_expedicion_id', $factura->punto_expedicion_id)
                        ->orderBy('fecha_apertura', 'desc')
                        ->orderBy('hora_apertura', 'desc')
                        ->first();
                }

                if (!$apertura) {
                    $this->warn("  ⚠ No hay apertura de caja disponible para punto_expedicion_id: {$factura->punto_expedicion_id}");
                    $this->warn("  Saltando esta factura...");
                    DB::rollBack();
                    $errores++;
                    continue;
                }

                $this->line("  → Usando apertura ID: {$apertura->id}");

                // Obtener formas de pago (ahora deberían existir)
                $formasPago = $factura->formasPago()->get();

                foreach ($formasPago as $fp) {
                    $this->line("  → Creando movimiento de caja: {$fp->forma_pago} - ₲ " . number_format($fp->monto, 0, ',', '.'));

                    MovimientoCaja::create([
                        'apertura_caja_id' => $apertura->id,
                        'usuario_responsable_id' => $factura->creado_por ?? 1,
                        'tipo_movimiento' => 'INGRESO',
                        'concepto' => 'Venta factura ' . $factura->numero_timbrado . '-' . $factura->numero_factura . ' (corrección)',
                        'monto' => $fp->monto,
                        'forma_pago' => $fp->forma_pago,
                        'comprobante_numero' => $factura->numero_timbrado . '-' . $factura->numero_factura,
                        'referencia' => $fp->referencia,
                        'fecha_movimiento' => $factura->fecha_emision,
                        'hora_movimiento' => $factura->created_at->format('H:i:s'),
                        'venta_id' => $factura->id,
                        'activo' => true,
                        'creadoPor' => $factura->creado_por ?? 1,
                    ]);
                }

                DB::commit();
                $this->info("  ✓ Factura corregida exitosamente");
                $corregidas++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("  ✗ Error: " . $e->getMessage());
                $errores++;
            }
        }

        $this->newLine(2);
        $this->info("Resumen:");
        $this->line("  Total procesadas: {$facturas->count()}");
        $this->line("  Corregidas: {$corregidas}");
        $this->line("  Errores: {$errores}");

        return 0;
    }
}
