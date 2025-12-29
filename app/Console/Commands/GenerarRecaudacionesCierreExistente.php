<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ventas\CierreCaja;
use App\Models\Ventas\RecaudacionADepositar;
use Illuminate\Support\Facades\DB;

class GenerarRecaudacionesCierreExistente extends Command
{
    protected $signature = 'recaudaciones:generar-cierre {cierre_id}';
    protected $description = 'Genera recaudaciones para un cierre de caja existente';

    public function handle()
    {
        $cierreId = $this->argument('cierre_id');

        try {
            DB::beginTransaction();

            $cierre = CierreCaja::findOrFail($cierreId);

            $this->info("Procesando cierre #{$cierre->id}...");

            $recaudacionesGeneradas = 0;

            // Generar recaudación para cheques
            if ($cierre->total_cheques > 0) {
                RecaudacionADepositar::create([
                    'apertura_caja_id' => $cierre->apertura_caja_id,
                    'cierre_caja_id' => $cierre->id,
                    'fecha_recaudacion' => $cierre->fecha_cierre,
                    'tipo_recaudacion' => 'CHEQUE',
                    'monto' => $cierre->total_cheques,
                    'fecha_prevista_deposito' => now()->parse($cierre->fecha_cierre)->addDay()->toDateString(),
                    'estado' => 'PENDIENTE',
                    'usuario_registra_id' => $cierre->usuario_id,
                    'observaciones' => 'Generado manualmente para cierre existente',
                    'activo' => true,
                    'creadoPor' => $cierre->usuario_id,
                ]);
                $recaudacionesGeneradas++;
                $this->info("✓ Recaudación de CHEQUES creada: ₲ " . number_format($cierre->total_cheques, 0, ',', '.'));
            }

            // Generar recaudación para tarjetas
            if ($cierre->total_tarjetas > 0) {
                RecaudacionADepositar::create([
                    'apertura_caja_id' => $cierre->apertura_caja_id,
                    'cierre_caja_id' => $cierre->id,
                    'fecha_recaudacion' => $cierre->fecha_cierre,
                    'tipo_recaudacion' => 'TARJETA',
                    'monto' => $cierre->total_tarjetas,
                    'fecha_prevista_deposito' => now()->parse($cierre->fecha_cierre)->addDays(2)->toDateString(),
                    'estado' => 'PENDIENTE',
                    'usuario_registra_id' => $cierre->usuario_id,
                    'observaciones' => 'Generado manualmente para cierre existente',
                    'activo' => true,
                    'creadoPor' => $cierre->usuario_id,
                ]);
                $recaudacionesGeneradas++;
                $this->info("✓ Recaudación de TARJETAS creada: ₲ " . number_format($cierre->total_tarjetas, 0, ',', '.'));
            }

            // Generar recaudación para transferencias
            if ($cierre->total_transferencias > 0) {
                RecaudacionADepositar::create([
                    'apertura_caja_id' => $cierre->apertura_caja_id,
                    'cierre_caja_id' => $cierre->id,
                    'fecha_recaudacion' => $cierre->fecha_cierre,
                    'tipo_recaudacion' => 'TRANSFERENCIA',
                    'monto' => $cierre->total_transferencias,
                    'fecha_prevista_deposito' => $cierre->fecha_cierre,
                    'estado' => 'PENDIENTE',
                    'usuario_registra_id' => $cierre->usuario_id,
                    'observaciones' => 'Generado manualmente para cierre existente',
                    'activo' => true,
                    'creadoPor' => $cierre->usuario_id,
                ]);
                $recaudacionesGeneradas++;
                $this->info("✓ Recaudación de TRANSFERENCIAS creada: ₲ " . number_format($cierre->total_transferencias, 0, ',', '.'));
            }

            // Generar recaudación para otros
            if ($cierre->total_otros > 0) {
                RecaudacionADepositar::create([
                    'apertura_caja_id' => $cierre->apertura_caja_id,
                    'cierre_caja_id' => $cierre->id,
                    'fecha_recaudacion' => $cierre->fecha_cierre,
                    'tipo_recaudacion' => 'MIXTO',
                    'monto' => $cierre->total_otros,
                    'fecha_prevista_deposito' => now()->parse($cierre->fecha_cierre)->addDay()->toDateString(),
                    'estado' => 'PENDIENTE',
                    'usuario_registra_id' => $cierre->usuario_id,
                    'observaciones' => 'QR y otros medios de pago - Generado manualmente para cierre existente',
                    'activo' => true,
                    'creadoPor' => $cierre->usuario_id,
                ]);
                $recaudacionesGeneradas++;
                $this->info("✓ Recaudación MIXTA (QR y otros) creada: ₲ " . number_format($cierre->total_otros, 0, ',', '.'));
            }

            DB::commit();

            if ($recaudacionesGeneradas > 0) {
                $this->info("\n✓ Se generaron {$recaudacionesGeneradas} recaudaciones exitosamente.");
            } else {
                $this->warn("\n⚠ No se generaron recaudaciones porque todos los totales son cero.");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
