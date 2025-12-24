<?php

namespace Database\Seeders;

use App\Models\Stock\Producto;
use Illuminate\Database\Seeder;

class RepuestosMotobombasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $repuestos = [
            [
                'nombre' => 'Impulsor',
                'descripcion' => 'Impulsor (rotor) para motobomba centrífuga. Componente que impulsa el agua mediante fuerza centrífuga.',
                'modelo' => 'IMP-STD-100',
                'precio' => 285000,
                'categoria_id' => 21, // Impulsores y Repuestos
                'marca_id' => 2, // Pedrollo
            ],
            [
                'nombre' => 'Camisa de impulsor / difusor',
                'descripcion' => 'Camisa o difusor que rodea el impulsor, dirige el flujo del agua de manera eficiente.',
                'modelo' => 'CAM-DIF-75',
                'precio' => 165000,
                'categoria_id' => 21,
                'marca_id' => 2,
            ],
            [
                'nombre' => 'Sello mecánico',
                'descripcion' => 'Sello mecánico estándar para evitar fugas de agua en el eje de la bomba.',
                'modelo' => 'SEL-MEC-20',
                'precio' => 195000,
                'categoria_id' => 22, // Sellos Mecánicos
                'marca_id' => 1, // Grundfos
            ],
            [
                'nombre' => 'Empaquetadura (packing)',
                'descripcion' => 'Empaquetadura tradicional para sellado de eje, alternativa al sello mecánico.',
                'modelo' => 'PACK-12MM',
                'precio' => 45000,
                'categoria_id' => 20, // Accesorios para Bombas
                'marca_id' => 5, // Dancor
            ],
            [
                'nombre' => 'Retenes',
                'descripcion' => 'Juego de retenes de goma para sellar ejes y prevenir fugas de aceite y agua.',
                'modelo' => 'RET-JGO-6',
                'precio' => 65000,
                'categoria_id' => 20,
                'marca_id' => 5,
            ],
            [
                'nombre' => 'Junta de carcasa',
                'descripcion' => 'Junta/empaquetadura plana para sellado entre las mitades de la carcasa de la bomba.',
                'modelo' => 'JUN-CARC-150',
                'precio' => 35000,
                'categoria_id' => 20,
                'marca_id' => 2,
            ],
            [
                'nombre' => 'Junta de tapa de impulsor',
                'descripcion' => 'Junta de sellado para la tapa del impulsor.',
                'modelo' => 'JUN-TAPA-80',
                'precio' => 28000,
                'categoria_id' => 20,
                'marca_id' => 2,
            ],
            [
                'nombre' => 'Válvula de pie (check/foot valve)',
                'descripcion' => 'Válvula de pie/retención que se instala al final de la tubería de succión para mantener el cebado.',
                'modelo' => 'VAL-PIE-1"',
                'precio' => 145000,
                'categoria_id' => 54, // Válvulas Check (Anti-retorno)
                'marca_id' => 7, // Tigre
            ],
            [
                'nombre' => 'Válvula de retención',
                'descripcion' => 'Válvula check horizontal para evitar el retorno de agua en la descarga.',
                'modelo' => 'VAL-CHECK-1.5"',
                'precio' => 175000,
                'categoria_id' => 54,
                'marca_id' => 7,
            ],
            [
                'nombre' => 'Filtro / criba de succión',
                'descripcion' => 'Filtro de malla metálica para proteger la bomba de impurezas en la succión.',
                'modelo' => 'FILT-SUC-100M',
                'precio' => 95000,
                'categoria_id' => 20,
                'marca_id' => 6, // Anauger
            ],
            [
                'nombre' => 'Rejillas / tamices',
                'descripcion' => 'Rejilla o tamiz de protección para entrada de agua.',
                'modelo' => 'REJ-PROT-150',
                'precio' => 55000,
                'categoria_id' => 20,
                'marca_id' => 6,
            ],
            [
                'nombre' => 'Rodamientos (del motor y de bomba)',
                'descripcion' => 'Juego de rodamientos/baleros para motor eléctrico y bomba.',
                'modelo' => 'ROD-6205-JGO',
                'precio' => 185000,
                'categoria_id' => 20,
                'marca_id' => 1,
            ],
            [
                'nombre' => 'Eje de bomba',
                'descripcion' => 'Eje de acero inoxidable para transmitir la rotación del motor al impulsor.',
                'modelo' => 'EJE-INOX-250',
                'precio' => 245000,
                'categoria_id' => 21,
                'marca_id' => 2,
            ],
            [
                'nombre' => 'Acoplamiento flexible',
                'descripcion' => 'Acoplamiento elástico para unir el eje del motor con el eje de la bomba.',
                'modelo' => 'ACOP-FLEX-50',
                'precio' => 125000,
                'categoria_id' => 20,
                'marca_id' => 5,
            ],
            [
                'nombre' => 'Chavetas y espaciadores',
                'descripcion' => 'Juego de chavetas/cuñas y espaciadores para fijación del impulsor en el eje.',
                'modelo' => 'CHAV-JGO-8',
                'precio' => 42000,
                'categoria_id' => 20,
                'marca_id' => 5,
            ],
            [
                'nombre' => 'Tornillería (tornillos, tuercas, arandelas)',
                'descripcion' => 'Kit completo de tornillería en acero inoxidable para ensamblaje de bomba.',
                'modelo' => 'TORN-KIT-INOX',
                'precio' => 68000,
                'categoria_id' => 20,
                'marca_id' => 5,
            ],
            [
                'nombre' => 'Abrazaderas para mangueras',
                'descripcion' => 'Juego de abrazaderas metálicas para sujeción de mangueras flexibles.',
                'modelo' => 'ABRAZ-1.5"-JGO',
                'precio' => 38000,
                'categoria_id' => 48, // Uniones y Niples
                'marca_id' => 11, // Plastisur
            ],
            [
                'nombre' => 'Mangueras de succión y descarga',
                'descripcion' => 'Manguera flexible reforzada para succión y descarga de agua (6 metros).',
                'modelo' => 'MANG-FLEX-1.5"x6m',
                'precio' => 320000,
                'categoria_id' => 20,
                'marca_id' => 11,
            ],
            [
                'nombre' => 'Borneras y conectores eléctricos',
                'descripcion' => 'Bornera de conexión eléctrica para motor monofásico/trifásico.',
                'modelo' => 'BORN-3P-20A',
                'precio' => 52000,
                'categoria_id' => 20,
                'marca_id' => 13, // Leo
            ],
            [
                'nombre' => 'Cables eléctricos (de alimentación)',
                'descripcion' => 'Cable eléctrico tripolar 3x2.5mm² para alimentación de motor (10 metros).',
                'modelo' => 'CAB-3x2.5-10m',
                'precio' => 185000,
                'categoria_id' => 20,
                'marca_id' => 13,
            ],
            [
                'nombre' => 'Contactor / relé',
                'descripcion' => 'Contactor electromagnético para arranque de motor eléctrico 25A.',
                'modelo' => 'CONT-25A-220V',
                'precio' => 245000,
                'categoria_id' => 20,
                'marca_id' => 13,
            ],
            [
                'nombre' => 'Fusibles y portafusibles',
                'descripcion' => 'Juego de fusibles cerámicos con portafusible para protección de motor.',
                'modelo' => 'FUS-20A-KIT',
                'precio' => 75000,
                'categoria_id' => 20,
                'marca_id' => 13,
            ],
            [
                'nombre' => 'Interruptor térmico / protector térmico',
                'descripcion' => 'Relé térmico de protección contra sobrecarga para motor eléctrico.',
                'modelo' => 'TERM-16-25A',
                'precio' => 165000,
                'categoria_id' => 20,
                'marca_id' => 13,
            ],
            [
                'nombre' => 'Capacitor de arranque (para monofásicas)',
                'descripcion' => 'Capacitor electrolítico de arranque para motor monofásico 450V 60µF.',
                'modelo' => 'CAP-60uF-450V',
                'precio' => 95000,
                'categoria_id' => 20,
                'marca_id' => 13,
            ],
            [
                'nombre' => 'Batería (si es impulsión con motor diésel/combustión)',
                'descripcion' => 'Batería de arranque 12V 45Ah para motor diésel de motobomba.',
                'modelo' => 'BAT-12V-45AH',
                'precio' => 485000,
                'categoria_id' => 20,
                'marca_id' => 14, // Shimge
            ],
            [
                'nombre' => 'Bujías incandescentes (en motores diésel)',
                'descripcion' => 'Juego de bujías de precalentamiento para motor diésel pequeño.',
                'modelo' => 'BUJ-GLOW-4P',
                'precio' => 125000,
                'categoria_id' => 20,
                'marca_id' => 14,
            ],
            [
                'nombre' => 'Filtro de combustible (en motobombas diésel)',
                'descripcion' => 'Filtro de combustible tipo cartucho para motor diésel pequeño.',
                'modelo' => 'FILT-COMB-D10',
                'precio' => 85000,
                'categoria_id' => 20,
                'marca_id' => 14,
            ],
            [
                'nombre' => 'Filtro de aire',
                'descripcion' => 'Filtro de aire de espuma lavable para motor de combustión interna.',
                'modelo' => 'FILT-AIRE-FOAM',
                'precio' => 65000,
                'categoria_id' => 20,
                'marca_id' => 14,
            ],
            [
                'nombre' => 'Protector de ventilador',
                'descripcion' => 'Rejilla protectora metálica para ventilador de motor eléctrico.',
                'modelo' => 'PROT-VENT-150',
                'precio' => 48000,
                'categoria_id' => 20,
                'marca_id' => 5,
            ],
            [
                'nombre' => 'Termostato o sensor térmico',
                'descripcion' => 'Sensor de temperatura / termostato para protección de sobrecalentamiento.',
                'modelo' => 'TERM-SENSOR-NC',
                'precio' => 72000,
                'categoria_id' => 20,
                'marca_id' => 1,
            ],
        ];

        foreach ($repuestos as $repuesto) {
            Producto::create([
                'nombre' => $repuesto['nombre'],
                'descripcion' => $repuesto['descripcion'],
                'modelo' => $repuesto['modelo'],
                'tipo' => 'PRODUCTO',
                'origen' => 'IMPORTADO',
                'categoria_id' => $repuesto['categoria_id'],
                'marca_id' => $repuesto['marca_id'],
                'unidad_medida_id' => 1, // Unidad
                'permite_venta' => true,
                'permite_compra' => true,
                'maneja_stock' => true,
                'activo' => true,
                'stock_minimo' => 2,
                'stock_maximo' => 20,
                'creadoPor' => 1,
            ]);
        }

        // Crear precios para cada producto creado
        $productos = Producto::where('nombre', 'LIKE', '%impulsor%')
            ->orWhere('nombre', 'LIKE', '%sello%')
            ->orWhere('nombre', 'LIKE', '%válvula%')
            ->orWhere('nombre', 'LIKE', '%filtro%')
            ->orWhere('nombre', 'LIKE', '%rodamiento%')
            ->orWhere('nombre', 'LIKE', '%eje%')
            ->orWhere('nombre', 'LIKE', '%acoplamiento%')
            ->orWhere('nombre', 'LIKE', '%manguera%')
            ->orWhere('nombre', 'LIKE', '%cable%')
            ->orWhere('nombre', 'LIKE', '%contactor%')
            ->orWhere('nombre', 'LIKE', '%capacitor%')
            ->orWhere('nombre', 'LIKE', '%batería%')
            ->orWhere('nombre', 'LIKE', '%empaquetadura%')
            ->orWhere('nombre', 'LIKE', '%retenes%')
            ->orWhere('nombre', 'LIKE', '%junta%')
            ->orWhere('nombre', 'LIKE', '%rejilla%')
            ->orWhere('nombre', 'LIKE', '%chaveta%')
            ->orWhere('nombre', 'LIKE', '%tornillería%')
            ->orWhere('nombre', 'LIKE', '%abrazadera%')
            ->orWhere('nombre', 'LIKE', '%bornera%')
            ->orWhere('nombre', 'LIKE', '%fusible%')
            ->orWhere('nombre', 'LIKE', '%interruptor%')
            ->orWhere('nombre', 'LIKE', '%bujía%')
            ->orWhere('nombre', 'LIKE', '%protector%')
            ->orWhere('nombre', 'LIKE', '%termostato%')
            ->orWhere('nombre', 'LIKE', '%camisa%')
            ->get();

        foreach ($productos as $index => $producto) {
            $precio = $repuestos[$index]['precio'] ?? 100000;
            $precioCompra = round($precio * 0.65); // 65% del precio de venta
            $margenPorcentaje = round((($precio - $precioCompra) / $precioCompra) * 100, 2);

            \App\Models\Stock\Precio::create([
                'producto_id' => $producto->id,
                'precio_venta' => $precio,
                'precio_compra' => $precioCompra,
                'margen_porcentaje' => $margenPorcentaje,
                'iva' => '10',
                'moneda' => 'PYG',
                'es_actual' => true,
                'creadoPor' => 1,
            ]);

            // Crear stock inicial
            \App\Models\Stock\Stock::create([
                'producto_id' => $producto->id,
                'deposito_id' => 1, // Depósito principal
                'stock_actual' => rand(5, 15),
                'stock_minimo' => 2,
                'stock_maximo' => 20,
                'creadoPor' => 1,
            ]);
        }
    }
}
