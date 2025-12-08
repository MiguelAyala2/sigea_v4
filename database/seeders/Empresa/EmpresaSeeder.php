<?php

namespace Database\Seeders\Empresa;

use App\Models\Empresa\ActividadEconomica;
use App\Models\Empresa\Empresa;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar actividad económica (debería existir del seeder anterior)
        $actividadEconomica = ActividadEconomica::where('codigo', '47520')->first();

        if (!$actividadEconomica) {
            // Crear si no existe
            $actividadEconomica = ActividadEconomica::create([
                'codigo' => '47520',
                'descripcion' => 'Venta al por menor de artículos de ferretería, pinturas y productos de vidrio en comercios especializados',
                'activo' => true,
            ]);
        }

        $empresa = Empresa::create([
            'razon_social' => 'AGUATERÍA Y PLOMERÍA SIGEA SOCIEDAD ANÓNIMA',
            'nombre_fantasia' => 'AGUATERÍA Y PLOMERÍA SIGEA S.A.',
            'ruc' => '80012345-6',
            'dv' => '6',
            'tipo_contribuyente' => 'juridica',
            'regimen_tributario' => 'general',
            'obligado_factura_electronica' => true,
            'direccion' => 'Av. Eusebio Ayala Km 4.5',
            'departamento' => 'Capital',
            'ciudad' => 'Asunción',
            'telefono' => '(021) 555-1234',
            'email' => 'ventas@aguateriasigea.com.py',
            'sitio_web' => 'www.aguateriasigea.com.py',
            'fecha_inicio_actividad' => '2020-03-15',
            'activo' => true,
            'creadoPor' => User::first()->id,
        ]);

        // Asociar actividad económica a la empresa
        $empresa->actividadesEconomicas()->attach($actividadEconomica->id);

        $this->command->info('✓ Empresa creada: AGUATERÍA Y PLOMERÍA SIGEA S.A.');
    }
}