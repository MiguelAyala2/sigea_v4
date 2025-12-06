<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa.TIMBRADOS', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->string('numero_timbrado', 15);
            $table->date('fecha_inicio_vigencia');
            $table->date('fecha_fin_vigencia');
            $table->enum('tipo_documento', ['factura', 'nota_credito', 'nota_debito', 'remision', 'retencion'])->default('factura');
            $table->bigInteger('numero_desde');
            $table->bigInteger('numero_hasta');
            $table->bigInteger('numero_actual')->default(0);
            $table->boolean('es_electronico')->default(false);
            $table->enum('cdc_ambiente', ['produccion', 'test'])->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('creadoPor')->nullable();
            $table->unsignedBigInteger('actualizadoPor')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresa.EMPRESA')->onDelete('cascade');
            $table->foreign('creadoPor')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizadoPor')->references('id')->on('users')->onDelete('set null');
            $table->index(['empresa_id', 'tipo_documento', 'activo'], 'timbrado_empresa_tipo_activo_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa.TIMBRADOS');
    }
};
