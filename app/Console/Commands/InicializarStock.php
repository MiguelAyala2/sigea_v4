<?php

namespace App\Console\Commands;

use App\Models\Stock\Producto;
use App\Models\Stock\Stock;
use App\Models\Empresa\Deposito;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class InicializarStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:inicializar {--deposito_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa registros de stock para productos que no tienen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $depositoId = $this->option('deposito_id');

        $this->info('Inicializando registros de stock...');

        // Obtener todos los productos activos que manejan stock
        $productos = Producto::where('activo', true)
                             ->where('maneja_stock', true)
                             ->get();

        $creados = 0;
        $existentes = 0;

        foreach ($productos as $producto) {
            // Verificar si ya existe un registro de stock para este producto y depósito
            $stockExistente = Stock::where('producto_id', $producto->id)
                                   ->where('deposito_id', $depositoId)
                                   ->first();

            if (!$stockExistente) {
                Stock::create([
                    'producto_id' => $producto->id,
                    'deposito_id' => $depositoId,
                    'stock_actual' => 0,
                    'stock_minimo' => $producto->stock_minimo ?? 0,
                    'stock_maximo' => $producto->stock_maximo ?? 0,
                    'creadoPor' => 1, // Usuario administrador
                ]);

                $creados++;
                $this->line("✓ Stock creado para: {$producto->nombre}");
            } else {
                $existentes++;
            }
        }

        $this->newLine();
        $this->info("Proceso completado:");
        $this->info("- Registros creados: {$creados}");
        $this->info("- Registros existentes: {$existentes}");
        $this->info("- Total procesados: " . ($creados + $existentes));

        return Command::SUCCESS;
    }
}
