<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Paso 1: Eliminar restricción CHECK si existe
        DB::statement("ALTER TABLE compras.ordenes_compra DROP CONSTRAINT IF EXISTS ordenes_compra_estado_check");

        // Paso 2: Eliminar el default y convertir a VARCHAR
        DB::statement("ALTER TABLE compras.ordenes_compra ALTER COLUMN estado DROP DEFAULT");
        DB::statement("ALTER TABLE compras.ordenes_compra ALTER COLUMN estado TYPE VARCHAR(50)");

        // Paso 3: Actualizar los registros existentes a los nuevos estados
        DB::statement("
            UPDATE compras.ordenes_compra
            SET estado = CASE
                WHEN estado IN ('BORRADOR', 'EMITIDA', 'ENVIADA') THEN 'PENDIENTE'
                WHEN estado IN ('CONFIRMADA', 'PARCIALMENTE_RECIBIDA', 'COMPLETAMENTE_RECIBIDA', 'FACTURADA') THEN 'APROBADO'
                WHEN estado IN ('CANCELADA', 'ANULADA') THEN 'RECHAZADO'
                ELSE estado
            END
        ");

        // Paso 3: Crear el nuevo tipo enum con los estados simplificados
        DB::statement("DROP TYPE IF EXISTS compras.orden_compra_estado_enum CASCADE");
        DB::statement("
            CREATE TYPE compras.orden_compra_estado_enum AS ENUM (
                'PENDIENTE',
                'APROBADO',
                'RECHAZADO'
            )
        ");

        // Paso 4: Aplicar el nuevo tipo enum a la columna
        DB::statement("
            ALTER TABLE compras.ordenes_compra
            ALTER COLUMN estado TYPE compras.orden_compra_estado_enum
            USING estado::compras.orden_compra_estado_enum
        ");

        // Paso 5: Establecer el valor por defecto
        DB::statement("ALTER TABLE compras.ordenes_compra ALTER COLUMN estado SET DEFAULT 'PENDIENTE'::compras.orden_compra_estado_enum");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar estados originales
        DB::statement("ALTER TABLE compras.ordenes_compra ALTER COLUMN estado DROP DEFAULT");
        DB::statement("ALTER TABLE compras.ordenes_compra ALTER COLUMN estado TYPE VARCHAR(50)");

        DB::statement("DROP TYPE IF EXISTS compras.orden_compra_estado_enum CASCADE");
        DB::statement("
            CREATE TYPE compras.orden_compra_estado_enum AS ENUM (
                'BORRADOR',
                'EMITIDA',
                'ENVIADA',
                'CONFIRMADA',
                'PARCIALMENTE_RECIBIDA',
                'COMPLETAMENTE_RECIBIDA',
                'FACTURADA',
                'CANCELADA',
                'ANULADA'
            )
        ");

        DB::statement("
            ALTER TABLE compras.ordenes_compra
            ALTER COLUMN estado TYPE compras.orden_compra_estado_enum
            USING estado::compras.orden_compra_estado_enum
        ");

        DB::statement("ALTER TABLE compras.ordenes_compra ALTER COLUMN estado SET DEFAULT 'BORRADOR'::compras.orden_compra_estado_enum");
    }
};
