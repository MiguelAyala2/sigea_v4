<?php

namespace App\Console\Commands;

use App\Models\Ventas\Factura;
use App\Models\Ventas\FacturaDetalle;
use App\Models\Ventas\CuentaPorCobrar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrearFacturaPruebaCredito extends Command
{
    protected $signature = 'factura:prueba-credito';
    protected $description = 'Crea una factura de prueba a crédito con su cuenta por cobrar';

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
            // Crear factura a crédito (30 días)
            $factura = Factura::create([
                'numero_factura' => str_pad($timbrado->numero_actual + 1, 7, '0', STR_PAD_LEFT),
                'fecha_emision' => now(),
                'fecha_vencimiento' => now()->addDays(30),
                'cliente_id' => $cliente->id,
                'timbrado_id' => $timbrado->id,
                'punto_expedicion_id' => $puntoExpedicion->id,
                'sucursal_id' => $sucursal->id,
                'deposito_id' => $deposito->id,
                'numero_timbrado' => $timbrado->numero_timbrado,
                'condicion_pago' => '30_DIAS', // CRÉDITO a 30 días
                'subtotal' => 500000,
                'iva_10' => 50000,
                'iva_5' => 0,
                'exenta' => 0,
                'total_iva' => 50000,
                'descuento_global' => 0,
                'flete' => 0,
                'total' => 550000,
                'estado' => 'BORRADOR',
                'activo' => true,
            ]);

            // Crear detalle
            FacturaDetalle::create([
                'factura_id' => $factura->id,
                'producto_id' => $producto->id,
                'cantidad' => 10,
                'precio_unitario' => 50000,
                'descuento_porcentaje' => 0,
                'descuento_monto' => 0,
                'subtotal' => 500000,
                'iva_porcentaje' => 10,
                'iva_monto' => 50000,
                'total' => 550000,
            ]);

            // Crear cuenta por cobrar
            CuentaPorCobrar::create([
                'factura_id' => $factura->id,
                'numero_factura' => $timbrado->numero_timbrado . '-' . $factura->numero_factura,
                'cliente_id' => $cliente->id,
                'fecha_emision' => $factura->fecha_emision,
                'fecha_vencimiento' => $factura->fecha_vencimiento,
                'monto_total' => $factura->total,
                'monto_pagado' => 0,
                'saldo_pendiente' => $factura->total,
                'estado' => 'PENDIENTE',
                'activo' => true,
            ]);

            $this->info('✓ Factura de prueba creada:');
            $this->line('  Factura #: ' . $factura->numero_factura);
            $this->line('  Cliente: ' . $cliente->nombre_razon_social);
            $this->line('  Condición: ' . $factura->condicion_pago);
            $this->line('  Total: ₲ ' . number_format($factura->total, 0, ',', '.'));
            $this->line('  Vencimiento: ' . $factura->fecha_vencimiento->format('d/m/Y'));
            $this->newLine();
            $this->info('✓ Cuenta por cobrar creada exitosamente');
        });

        // Verificar
        $cuentas = CuentaPorCobrar::count();
        $this->newLine();
        $this->info('Total de cuentas por cobrar en el sistema: ' . $cuentas);

        return 0;
    }
}
