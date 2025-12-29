<?php

namespace App\Console\Commands;

use App\Models\Ventas\Factura;
use App\Models\Ventas\FacturaDetalle;
use App\Models\Ventas\FacturaFormaPago;
use App\Models\Ventas\MovimientoCaja;
use App\Models\Ventas\AperturaCaja;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CrearFacturaPruebaContado extends Command
{
    protected $signature = 'factura:prueba-contado';
    protected $description = 'Crea una factura de prueba al contado con movimiento de caja';

    public function handle()
    {
        // Obtener datos necesarios
        $cliente = \App\Models\Servicios\Cliente::first();
        $timbrado = \App\Models\Empresa\Timbrado::where('tipo_documento', 'factura')
            ->where('activo', true)
            ->first();
        $puntoExpedicion = \App\Models\Empresa\PuntoExpedicion::where('activo', true)->first();
        $sucursal = \App\Models\Empresa\Sucursal::where('activo', true)->first();
        $deposito = \App\Models\Empresa\Deposito::where('activo', true)->first();
        $producto = \App\Models\Stock\Producto::first();

        if (!$cliente || !$timbrado || !$puntoExpedicion || !$sucursal || !$deposito || !$producto) {
            $this->error('Faltan datos requeridos:');
            $this->line('  Cliente: ' . ($cliente ? '✓ OK' : '✗ FALTA'));
            $this->line('  Timbrado: ' . ($timbrado ? '✓ OK' : '✗ FALTA'));
            $this->line('  Punto Expedición: ' . ($puntoExpedicion ? '✓ OK' : '✗ FALTA'));
            $this->line('  Sucursal: ' . ($sucursal ? '✓ OK' : '✗ FALTA'));
            $this->line('  Depósito: ' . ($deposito ? '✓ OK' : '✗ FALTA'));
            $this->line('  Producto: ' . ($producto ? '✓ OK' : '✗ FALTA'));
            return 1;
        }

        DB::transaction(function () use ($cliente, $timbrado, $puntoExpedicion, $sucursal, $deposito, $producto) {
            // IMPORTANTE: El sistema trabaja con precios IVA INCLUIDO
            // Si el precio unitario es 50.000, ese valor YA incluye el IVA

            // Calcular: 16 unidades x 50.000 = 800.000 (IVA INCLUIDO)
            // IVA 10% incluido: 800.000 / 1.1 = 727.273 (base) + 72.727 (IVA) = 800.000
            $precioUnitario = 50000; // Ya incluye IVA
            $cantidad = 16;
            $subtotal = $precioUnitario * $cantidad; // 800.000 (IVA incluido)
            $ivaMonto = round($subtotal - ($subtotal / 1.1)); // IVA desglosado: 72.727

            // Crear factura al CONTADO
            $factura = Factura::create([
                'numero_factura' => str_pad($timbrado->numero_actual + 2, 7, '0', STR_PAD_LEFT),
                'fecha_emision' => now(),
                'fecha_vencimiento' => now(),
                'cliente_id' => $cliente->id,
                'timbrado_id' => $timbrado->id,
                'punto_expedicion_id' => $puntoExpedicion->id,
                'sucursal_id' => $sucursal->id,
                'deposito_id' => $deposito->id,
                'numero_timbrado' => $timbrado->numero_timbrado,
                'condicion_pago' => 'CONTADO', // CONTADO
                'subtotal' => $subtotal, // 800.000 (IVA incluido)
                'iva_10' => $ivaMonto, // 72.727 (solo informativo)
                'iva_5' => 0,
                'exenta' => 0,
                'total_iva' => $ivaMonto,
                'descuento_global' => 0,
                'flete' => 0,
                'total' => $subtotal, // 800.000 (mismo que subtotal porque IVA ya está incluido)
                'estado' => 'BORRADOR',
                'activo' => true,
            ]);

            // Crear detalle
            FacturaDetalle::create([
                'factura_id' => $factura->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'descuento_porcentaje' => 0,
                'descuento_monto' => 0,
                'subtotal' => $subtotal, // 800.000 (IVA incluido)
                'iva_porcentaje' => 10,
                'iva_monto' => $ivaMonto, // 72.727 (solo informativo)
                'total' => $subtotal, // 800.000
            ]);

            // Crear forma de pago (EFECTIVO)
            FacturaFormaPago::create([
                'factura_id' => $factura->id,
                'forma_pago' => 'EFECTIVO',
                'monto' => $subtotal, // 800.000 (el total de la factura)
                'referencia' => null,
            ]);

            // Verificar si hay apertura activa
            $aperturaActual = AperturaCaja::obtenerAperturaActual($puntoExpedicion->id);

            if ($aperturaActual) {
                // Crear movimiento de caja
                MovimientoCaja::create([
                    'apertura_caja_id' => $aperturaActual->id,
                    'usuario_responsable_id' => 1, // ID del primer usuario
                    'tipo_movimiento' => 'INGRESO',
                    'concepto' => 'Venta factura ' . $timbrado->numero_timbrado . '-' . $factura->numero_factura,
                    'monto' => $subtotal, // Usar el total correcto
                    'forma_pago' => 'EFECTIVO',
                    'comprobante_numero' => $timbrado->numero_timbrado . '-' . $factura->numero_factura,
                    'referencia' => null,
                    'fecha_movimiento' => now(),
                    'hora_movimiento' => now()->format('H:i:s'),
                    'venta_id' => $factura->id,
                    'activo' => true,
                    'creadoPor' => 1,
                ]);

                $this->info('✓ Movimiento de caja registrado');
            } else {
                $this->warn('⚠ No hay apertura de caja activa - movimiento NO registrado');
            }

            $this->info('✓ Factura de prueba CONTADO creada:');
            $this->line('  Factura #: ' . $factura->numero_factura);
            $this->line('  Cliente: ' . $cliente->nombre_razon_social);
            $this->line('  Condición: ' . $factura->condicion_pago);
            $this->line('  Subtotal: ₲ ' . number_format($subtotal, 0, ',', '.') . ' (IVA incluido)');
            $this->line('  IVA 10%: ₲ ' . number_format($ivaMonto, 0, ',', '.') . ' (desglosado)');
            $this->line('  Total: ₲ ' . number_format($factura->total, 0, ',', '.'));
            $this->line('  Forma de pago: EFECTIVO ₲ ' . number_format($subtotal, 0, ',', '.'));
        });

        // Verificar
        $movimientos = MovimientoCaja::count();
        $this->newLine();
        $this->info('Total de movimientos de caja en el sistema: ' . $movimientos);

        return 0;
    }
}
