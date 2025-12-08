<?php

namespace Database\Seeders\Stock;

use App\Models\Stock\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // NIVEL 1: GRUPOS PRINCIPALES
        $motobombas = $this->crearCategoria('MOTB', 'Motobombas y Equipos de Bombeo', null, 1);
        $tanques = $this->crearCategoria('TANQ', 'Tanques y Cisternas', null, 1);
        $tubos = $this->crearCategoria('TUBO', 'Tubos y Cañerías', null, 1);
        $conexiones = $this->crearCategoria('CONE', 'Conexiones y Accesorios', null, 1);
        $valvulas = $this->crearCategoria('VALV', 'Válvulas y Llaves', null, 1);

        // NIVEL 2 y 3: MOTOBOMBAS
        $perifericas = $this->crearCategoria('MOTB-PER', 'Motobombas Periféricas', $motobombas, 2);
        $this->crearCategoria('MOTB-PER-05', 'Periféricas Residenciales 1/2 HP', $perifericas, 3);
        $this->crearCategoria('MOTB-PER-07', 'Periféricas Residenciales 3/4 HP', $perifericas, 3);
        $this->crearCategoria('MOTB-PER-10', 'Periféricas Residenciales 1 HP', $perifericas, 3);
        $this->crearCategoria('MOTB-PER-IND', 'Periféricas Industriales', $perifericas, 3);

        $centrifugas = $this->crearCategoria('MOTB-CEN', 'Motobombas Centrífugas', $motobombas, 2);
        $this->crearCategoria('MOTB-CEN-MONO', 'Centrífugas Monofásicas', $centrifugas, 3);
        $this->crearCategoria('MOTB-CEN-TRI', 'Centrífugas Trifásicas', $centrifugas, 3);
        $this->crearCategoria('MOTB-CEN-AUTO', 'Centrífugas Autocebantes', $centrifugas, 3);

        $sumergibles = $this->crearCategoria('MOTB-SUM', 'Motobombas Sumergibles', $motobombas, 2);
        $this->crearCategoria('MOTB-SUM-POZ', 'Sumergibles para Pozos', $sumergibles, 3);
        $this->crearCategoria('MOTB-SUM-DRE', 'Sumergibles para Drenaje', $sumergibles, 3);
        $this->crearCategoria('MOTB-SUM-ACH', 'Sumergibles de Achique', $sumergibles, 3);

        $this->crearCategoria('MOTB-PRE', 'Motobombas Presurizadoras', $motobombas, 2);

        $accBombas = $this->crearCategoria('MOTB-ACC', 'Accesorios para Bombas', $motobombas, 2);
        $this->crearCategoria('MOTB-ACC-IMP', 'Impulsores y Repuestos', $accBombas, 3);
        $this->crearCategoria('MOTB-ACC-SEL', 'Sellos Mecánicos', $accBombas, 3);
        $this->crearCategoria('MOTB-ACC-PRE', 'Presostatos', $accBombas, 3);

        // NIVEL 2 y 3: TANQUES
        $polietileno = $this->crearCategoria('TANQ-POL', 'Tanques de Polietileno', $tanques, 2);
        $this->crearCategoria('TANQ-POL-500', 'Tanques hasta 500 L', $polietileno, 3);
        $this->crearCategoria('TANQ-POL-1000', 'Tanques 501-1000 L', $polietileno, 3);
        $this->crearCategoria('TANQ-POL-2000', 'Tanques 1001-2000 L', $polietileno, 3);
        $this->crearCategoria('TANQ-POL-MAX', 'Tanques más de 2000 L', $polietileno, 3);

        $this->crearCategoria('TANQ-FIB', 'Tanques de Fibrocemento', $tanques, 2);
        $this->crearCategoria('TANQ-CIS', 'Cisternas Horizontales', $tanques, 2);

        $accTanques = $this->crearCategoria('TANQ-ACC', 'Accesorios para Tanques', $tanques, 2);
        $this->crearCategoria('TANQ-ACC-FLO', 'Flotantes', $accTanques, 3);
        $this->crearCategoria('TANQ-ACC-TAP', 'Tapas y Bocas de Inspección', $accTanques, 3);
        $this->crearCategoria('TANQ-ACC-BRI', 'Bridas y Conexiones', $accTanques, 3);

        // NIVEL 2 y 3: TUBOS
        $pvcFrio = $this->crearCategoria('TUBO-PVC', 'Tubos PVC para Agua Fría', $tubos, 2);
        $this->crearCategoria('TUBO-PVC-05', 'PVC 1/2" Clase 5', $pvcFrio, 3);
        $this->crearCategoria('TUBO-PVC-07', 'PVC 3/4" Clase 5', $pvcFrio, 3);
        $this->crearCategoria('TUBO-PVC-10', 'PVC 1" Clase 5', $pvcFrio, 3);
        $this->crearCategoria('TUBO-PVC-15', 'PVC 1 1/2" Clase 5', $pvcFrio, 3);
        $this->crearCategoria('TUBO-PVC-20', 'PVC 2" Clase 5', $pvcFrio, 3);

        $this->crearCategoria('TUBO-CPVC', 'Tubos PVC para Agua Caliente (CPVC)', $tubos, 2);
        $this->crearCategoria('TUBO-DES', 'Tubos PVC para Desagüe', $tubos, 2);
        $this->crearCategoria('TUBO-PPR', 'Tubos PPR (Polipropileno)', $tubos, 2);
        $this->crearCategoria('TUBO-GAL', 'Tubos Galvanizados', $tubos, 2);

        // NIVEL 2: CONEXIONES
        $this->crearCategoria('CONE-COD', 'Codos PVC', $conexiones, 2);
        $this->crearCategoria('CONE-TEE', 'Tees PVC', $conexiones, 2);
        $this->crearCategoria('CONE-RED', 'Reducciones PVC', $conexiones, 2);
        $this->crearCategoria('CONE-UNI', 'Uniones y Niples', $conexiones, 2);
        $this->crearCategoria('CONE-TAP', 'Tapones y Caps', $conexiones, 2);

        // NIVEL 2 y 3: VÁLVULAS
        $llavesPaso = $this->crearCategoria('VALV-LLA', 'Llaves de Paso', $valvulas, 2);
        $this->crearCategoria('VALV-LLA-BOL', 'Llaves Esféricas (Bola)', $llavesPaso, 3);
        $this->crearCategoria('VALV-LLA-COM', 'Llaves de Compuerta', $llavesPaso, 3);
        $this->crearCategoria('VALV-LLA-ANG', 'Llaves Angulares', $llavesPaso, 3);

        $this->crearCategoria('VALV-CHK', 'Válvulas Check (Anti-retorno)', $valvulas, 2);
        $this->crearCategoria('VALV-ALI', 'Válvulas de Alivio', $valvulas, 2);

        $griferia = $this->crearCategoria('VALV-GRI', 'Grifería', $valvulas, 2);
        $this->crearCategoria('VALV-GRI-JAR', 'Canillas de Jardín', $griferia, 3);
        $this->crearCategoria('VALV-GRI-LAV', 'Grifos de Lavatorio', $griferia, 3);
        $this->crearCategoria('VALV-GRI-MEZ', 'Mezcladoras', $griferia, 3);

        $this->command->info('✓ Estructura de categorías creada (3 niveles jerárquicos)');
    }

    private function crearCategoria(string $codigo, string $nombre, ?Categoria $parent, int $nivel): Categoria
    {
        return Categoria::create([
            'parent_id' => $parent?->id,
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => "Categoría: {$nombre}",
            'nivel' => $nivel,
            'activo' => true,
            'creadoPor' => null,
        ]);
    }
}
