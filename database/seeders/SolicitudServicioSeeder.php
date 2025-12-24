<?php

namespace Database\Seeders;

use App\Models\Servicios\Cliente;
use App\Models\Servicios\SolicitudServicio;
use App\Models\Stock\Producto;
use Illuminate\Database\Seeder;

class SolicitudServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener clientes y productos para las solicitudes
        $clientes = Cliente::where('activo', true)->get();
        $productos = Producto::where('activo', true)->limit(5)->get();

        if ($clientes->isEmpty() || $productos->isEmpty()) {
            $this->command->warn('⚠️  No hay clientes o productos activos para crear solicitudes.');
            return;
        }

        $solicitudes = [
            [
                'numero_solicitud' => 'SOL-000001',
                'fecha' => '2025-12-16',
                'cliente_id' => $clientes[0]->id,
                'producto_id' => $productos[0]->id,
                'tipo_servicio' => 'mantenimiento',
                'prioridad' => 'media',
                'estado' => 'pendiente',
                'observaciones' => 'Mantenimiento preventivo programado',
                'activo' => true,
                'creadoPor' => 1,
            ],
            [
                'numero_solicitud' => 'SOL-000002',
                'fecha' => '2025-12-15',
                'cliente_id' => $clientes[1]->id ?? $clientes[0]->id,
                'producto_id' => $productos[1]->id ?? $productos[0]->id,
                'tipo_servicio' => 'reparacion',
                'prioridad' => 'alta',
                'estado' => 'en_proceso',
                'observaciones' => 'Equipo con falla en motor, requiere revisión urgente',
                'activo' => true,
                'creadoPor' => 1,
            ],
            [
                'numero_solicitud' => 'SOL-000003',
                'fecha' => '2025-12-14',
                'cliente_id' => $clientes[2]->id ?? $clientes[0]->id,
                'producto_id' => $productos[2]->id ?? $productos[0]->id,
                'tipo_servicio' => 'diagnostico',
                'prioridad' => 'baja',
                'estado' => 'completado',
                'observaciones' => 'Diagnóstico completo realizado, equipo en buen estado',
                'activo' => true,
                'creadoPor' => 1,
            ],
        ];

        foreach ($solicitudes as $solicitud) {
            SolicitudServicio::create($solicitud);
        }

        $this->command->info('✅ 3 solicitudes de servicio creadas exitosamente!');
    }
}
