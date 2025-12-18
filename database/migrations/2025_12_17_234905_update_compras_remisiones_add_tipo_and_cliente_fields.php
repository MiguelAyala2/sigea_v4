<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras.remisiones', function (Blueprint $table) {
            // Tipo de remisión: interna (entre sucursales) o externa (a clientes)
            $table->enum('tipo', ['INTERNA', 'EXTERNA'])->after('empresa_id')->default('EXTERNA');

            // Campos para remisiones externas (a clientes)
            $table->string('cliente_nombre', 200)->nullable()->after('tipo');
            $table->string('cliente_ruc', 50)->nullable()->after('cliente_nombre');
            $table->text('cliente_direccion')->nullable()->after('cliente_ruc');
            $table->string('cliente_telefono', 50)->nullable()->after('cliente_direccion');
            $table->string('cliente_email', 100)->nullable()->after('cliente_telefono');

            // Campos para remisiones internas (entre sucursales)
            $table->unsignedBigInteger('sucursal_destino_id')->nullable()->after('cliente_email');
            $table->unsignedBigInteger('deposito_destino_id')->nullable()->after('sucursal_destino_id');

            // Hacer proveedor_id nullable ya que no aplica para remisiones internas/externas
            $table->foreignId('proveedor_id')->nullable()->change();

            // Agregar foreign keys
            $table->foreign('sucursal_destino_id')->references('id')->on('empresa.SUCURSALES')->onDelete('restrict');
            $table->foreign('deposito_destino_id')->references('id')->on('empresa.DEPOSITOS')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('compras.remisiones', function (Blueprint $table) {
            $table->dropForeign(['sucursal_destino_id']);
            $table->dropForeign(['deposito_destino_id']);
            $table->dropColumn([
                'tipo',
                'cliente_nombre',
                'cliente_ruc',
                'cliente_direccion',
                'cliente_telefono',
                'cliente_email',
                'sucursal_destino_id',
                'deposito_destino_id',
            ]);
        });
    }
};
