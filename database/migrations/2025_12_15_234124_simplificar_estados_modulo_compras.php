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
        // ============================================
        // PEDIDOS DE COMPRA
        // ============================================

        // Eliminar restricción CHECK
        DB::statement("ALTER TABLE compras.pedidos_compra DROP CONSTRAINT IF EXISTS pedidos_compra_estado_check");

        // Convertir a VARCHAR
        DB::statement("ALTER TABLE compras.pedidos_compra ALTER COLUMN estado DROP DEFAULT");
        DB::statement("ALTER TABLE compras.pedidos_compra ALTER COLUMN estado TYPE VARCHAR(50)");

        // Actualizar estados existentes
        DB::statement("
            UPDATE compras.pedidos_compra
            SET estado = CASE
                WHEN estado IN ('BORRADOR', 'PENDIENTE_APROBACION') THEN 'PENDIENTE'
                WHEN estado = 'APROBADO' THEN 'APROBADO'
                WHEN estado = 'RECHAZADO' THEN 'RECHAZADO'
                ELSE 'PENDIENTE'
            END
        ");

        // Crear nuevo ENUM
        DB::statement("DROP TYPE IF EXISTS compras.pedido_compra_estado_enum CASCADE");
        DB::statement("
            CREATE TYPE compras.pedido_compra_estado_enum AS ENUM (
                'PENDIENTE',
                'APROBADO',
                'RECHAZADO'
            )
        ");

        // Aplicar ENUM
        DB::statement("
            ALTER TABLE compras.pedidos_compra
            ALTER COLUMN estado TYPE compras.pedido_compra_estado_enum
            USING estado::compras.pedido_compra_estado_enum
        ");

        DB::statement("ALTER TABLE compras.pedidos_compra ALTER COLUMN estado SET DEFAULT 'PENDIENTE'::compras.pedido_compra_estado_enum");

        // ============================================
        // PRESUPUESTOS
        // ============================================

        // Eliminar restricción CHECK
        DB::statement("ALTER TABLE compras.presupuestos DROP CONSTRAINT IF EXISTS presupuestos_estado_check");

        // Convertir a VARCHAR
        DB::statement("ALTER TABLE compras.presupuestos ALTER COLUMN estado DROP DEFAULT");
        DB::statement("ALTER TABLE compras.presupuestos ALTER COLUMN estado TYPE VARCHAR(50)");

        // Actualizar estados existentes
        DB::statement("
            UPDATE compras.presupuestos
            SET estado = CASE
                WHEN estado IN ('PENDIENTE', 'RECIBIDO', 'EN_EVALUACION') THEN 'PENDIENTE'
                WHEN estado IN ('SELECCIONADO', 'APROBADO') THEN 'APROBADO'
                WHEN estado IN ('RECHAZADO', 'VENCIDO', 'ANULADO') THEN 'RECHAZADO'
                ELSE 'PENDIENTE'
            END
        ");

        // Crear nuevo ENUM
        DB::statement("DROP TYPE IF EXISTS compras.presupuesto_estado_enum CASCADE");
        DB::statement("
            CREATE TYPE compras.presupuesto_estado_enum AS ENUM (
                'PENDIENTE',
                'APROBADO',
                'RECHAZADO'
            )
        ");

        // Aplicar ENUM
        DB::statement("
            ALTER TABLE compras.presupuestos
            ALTER COLUMN estado TYPE compras.presupuesto_estado_enum
            USING estado::compras.presupuesto_estado_enum
        ");

        DB::statement("ALTER TABLE compras.presupuestos ALTER COLUMN estado SET DEFAULT 'PENDIENTE'::compras.presupuesto_estado_enum");

        // ============================================
        // COMPRAS/FACTURAS
        // ============================================

        // Eliminar restricción CHECK
        DB::statement("ALTER TABLE compras.compras DROP CONSTRAINT IF EXISTS compras_estado_check");

        // Convertir a VARCHAR
        DB::statement("ALTER TABLE compras.compras ALTER COLUMN estado DROP DEFAULT");
        DB::statement("ALTER TABLE compras.compras ALTER COLUMN estado TYPE VARCHAR(50)");

        // Actualizar estados existentes
        DB::statement("
            UPDATE compras.compras
            SET estado = CASE
                WHEN estado IN ('BORRADOR', 'PENDIENTE') THEN 'PENDIENTE'
                WHEN estado IN ('APROBADA', 'PAGADA', 'PARCIAL') THEN 'APROBADO'
                WHEN estado IN ('RECHAZADA', 'ANULADA') THEN 'RECHAZADO'
                ELSE 'PENDIENTE'
            END
        ");

        // Crear nuevo ENUM
        DB::statement("DROP TYPE IF EXISTS compras.compra_estado_enum CASCADE");
        DB::statement("
            CREATE TYPE compras.compra_estado_enum AS ENUM (
                'PENDIENTE',
                'APROBADO',
                'RECHAZADO'
            )
        ");

        // Aplicar ENUM
        DB::statement("
            ALTER TABLE compras.compras
            ALTER COLUMN estado TYPE compras.compra_estado_enum
            USING estado::compras.compra_estado_enum
        ");

        DB::statement("ALTER TABLE compras.compras ALTER COLUMN estado SET DEFAULT 'PENDIENTE'::compras.compra_estado_enum");

        // ============================================
        // CUENTAS POR PAGAR
        // ============================================

        // Verificar si la tabla existe
        $exists = DB::select("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'compras' AND table_name = 'cuentas_por_pagar')");

        if ($exists[0]->exists) {
            // Eliminar restricción CHECK
            DB::statement("ALTER TABLE compras.cuentas_por_pagar DROP CONSTRAINT IF EXISTS cuentas_por_pagar_estado_check");

            // Convertir a VARCHAR
            DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN estado DROP DEFAULT");
            DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN estado TYPE VARCHAR(50)");

            // Actualizar estados existentes
            DB::statement("
                UPDATE compras.cuentas_por_pagar
                SET estado = CASE
                    WHEN estado IN ('PENDIENTE', 'VENCIDA') THEN 'PENDIENTE'
                    WHEN estado IN ('PAGADA', 'PARCIALMENTE_PAGADA') THEN 'APROBADO'
                    WHEN estado IN ('ANULADA', 'CANCELADA') THEN 'RECHAZADO'
                    ELSE 'PENDIENTE'
                END
            ");

            // Crear nuevo ENUM
            DB::statement("DROP TYPE IF EXISTS compras.cuenta_por_pagar_estado_enum CASCADE");
            DB::statement("
                CREATE TYPE compras.cuenta_por_pagar_estado_enum AS ENUM (
                    'PENDIENTE',
                    'APROBADO',
                    'RECHAZADO'
                )
            ");

            // Aplicar ENUM
            DB::statement("
                ALTER TABLE compras.cuentas_por_pagar
                ALTER COLUMN estado TYPE compras.cuenta_por_pagar_estado_enum
                USING estado::compras.cuenta_por_pagar_estado_enum
            ");

            DB::statement("ALTER TABLE compras.cuentas_por_pagar ALTER COLUMN estado SET DEFAULT 'PENDIENTE'::compras.cuenta_por_pagar_estado_enum");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta migración es irreversible sin perder datos
        // Se recomienda hacer un backup antes de ejecutar
    }
};
