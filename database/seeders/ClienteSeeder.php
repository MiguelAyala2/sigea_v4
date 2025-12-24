<?php

namespace Database\Seeders;

use App\Models\Servicios\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'tipo_cliente' => 'fisica',
                'documento' => '1234567-8',
                'nombre' => 'JUAN CARLOS PÉREZ GONZÁLEZ',
                'telefono' => '021-555-1234',
                'celular' => '0981-123-456',
                'email' => 'juan.perez@email.com',
                'direccion' => 'Av. Mariscal López 123, Asunción',
                'observaciones' => 'Cliente frecuente, prefiere pago en efectivo',
                'activo' => true,
                'creadoPor' => 1,
            ],
            [
                'tipo_cliente' => 'juridica',
                'documento' => '80012345-6',
                'nombre' => 'TECNOLOGÍA Y SERVICIOS S.A.',
                'telefono' => '021-555-5678',
                'celular' => '0982-234-567',
                'email' => 'contacto@tecnoservicios.com.py',
                'direccion' => 'Ruta Mariano Roque Alonso Km 12',
                'observaciones' => 'Empresa de IT, requiere factura electrónica',
                'activo' => true,
                'creadoPor' => 1,
            ],
            [
                'tipo_cliente' => 'fisica',
                'documento' => '2345678-9',
                'nombre' => 'MARÍA TERESA LÓPEZ ACOSTA',
                'telefono' => '021-555-9012',
                'celular' => '0983-345-678',
                'email' => 'maria.lopez@gmail.com',
                'direccion' => 'Barrio San Vicente, Calle 1 N° 456',
                'observaciones' => null,
                'activo' => true,
                'creadoPor' => 1,
            ],
            [
                'tipo_cliente' => 'juridica',
                'documento' => '80023456-7',
                'nombre' => 'DISTRIBUIDORA CENTRAL S.R.L.',
                'telefono' => '021-555-3456',
                'celular' => '0984-456-789',
                'email' => 'ventas@districentral.com.py',
                'direccion' => 'Zona Industrial, Luque',
                'observaciones' => 'Descuento del 10% en compras mayores a 1.000.000 Gs',
                'activo' => true,
                'creadoPor' => 1,
            ],
            [
                'tipo_cliente' => 'fisica',
                'documento' => '3456789-0',
                'nombre' => 'CARLOS ALBERTO MENDOZA RÍOS',
                'telefono' => null,
                'celular' => '0985-567-890',
                'email' => null,
                'direccion' => 'Fernando de la Mora, Zona Norte',
                'observaciones' => 'Inactivo temporalmente por mudanza',
                'activo' => false,
                'creadoPor' => 1,
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }

        $this->command->info('✅ 5 clientes creados exitosamente!');
    }
}
