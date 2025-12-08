<?php

namespace Database\Seeders;

use Database\Seeders\Empresa\DepositoSeeder;
use Database\Seeders\Empresa\EmpresaSeeder;
use Database\Seeders\Empresa\SucursalSeeder;
use Database\Seeders\Empresa\TimbradoSeeder;
use Database\Seeders\Stock\AtributoTipoSeeder;
use Database\Seeders\Stock\CategoriaSeeder;
use Database\Seeders\Stock\MarcaSeeder;
use Database\Seeders\Stock\ProductoSeeder;
use Database\Seeders\Stock\StockInicialSeeder;
use Database\Seeders\Stock\UnidadMedidaSeeder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParaguayDemoSeeder extends Seeder
{
    /**
     * Seeder principal para cargar datos de demostración de Paraguay
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   SEEDER DE DATOS DEMO - PARAGUAY');
        $this->command->info('   AGUATERÍA Y PLOMERÍA SIGEA S.A.');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');

        // VERIFICAR que existe al menos un usuario
        $user = User::first();
        
        if (!$user) {
            $this->command->error('× ERROR CRÍTICO: No hay usuarios en la base de datos.');
            $this->command->error('  Ejecutar primero: php artisan db:seed --class=RolYPermisoSeeder');
            $this->command->info('');
            $this->command->info('  O ejecutar: php artisan db:seed (que ejecuta todo en orden)');
            return;
        }

        // MÓDULO EMPRESA
        $this->command->info('🏢 MÓDULO EMPRESA');
        $this->command->info('─────────────────────────────────────────────────────────');
        $this->call(EmpresaSeeder::class);
        $this->call(SucursalSeeder::class);
        $this->call(DepositoSeeder::class);
        $this->call(TimbradoSeeder::class);
        $this->command->info('');

        // MÓDULO STOCK
        $this->command->info('📦 MÓDULO STOCK - CATÁLOGOS BASE');
        $this->command->info('─────────────────────────────────────────────────────────');
        $this->call(MarcaSeeder::class);
        $this->call(UnidadMedidaSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(AtributoTipoSeeder::class);
        $this->command->info('');

        // PRODUCTOS Y STOCK
        $this->command->info('🛒 PRODUCTOS Y STOCK INICIAL');
        $this->command->info('─────────────────────────────────────────────────────────');
        $this->call(ProductoSeeder::class);
        $this->call(StockInicialSeeder::class);
        $this->command->info('');

        // RESUMEN FINAL
        $this->mostrarResumen();
    }

    private function mostrarResumen(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   ✓ DATOS DE DEMOSTRACIÓN CARGADOS EXITOSAMENTE');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 RESUMEN:');
        $this->command->info('');
        $this->command->info('   EMPRESA:');
        $this->command->info('   • 1 Empresa: AGUATERÍA Y PLOMERÍA SIGEA S.A.');
        $this->command->info('   • 3 Sucursales: Asunción, San Lorenzo, Ñemby');
        $this->command->info('   • 3 Depósitos: 1 por sucursal');
        $this->command->info('   • 3 Timbrados: Facturación electrónica activa');
        $this->command->info('');
        $this->command->info('   STOCK:');
        $this->command->info('   • 15 Marcas: Grundfos, Pedrollo, Tigre, Rotoplas, etc.');
        $this->command->info('   • 29 Unidades de Medida');
        $this->command->info('   • 45+ Categorías (estructura jerárquica 3 niveles)');
        $this->command->info('   • 30 Atributos Tipo');
        $this->command->info('   • 50+ Productos con precios y atributos');
        $this->command->info('   • Stock inicial distribuido en 3 depósitos');
        $this->command->info('');
        $this->command->info('💰 MONEDA: Solo PYG (Guaraníes)');
        $this->command->info('📝 IVA: 10% (mayoría) / 5% (canasta básica)');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🚀 Sistema listo para usar!');
        $this->command->info('');
        $this->command->info('👤 Usuario creador: ID ' . User::first()->id);
        $this->command->info('');
    }
}