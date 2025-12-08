<?php

namespace App\Livewire\Stock\Productos;

use App\Models\Stock\Producto;
use App\Models\Stock\Categoria;
use App\Models\Stock\Marca;
use App\Models\Stock\UnidadMedida;
use App\Models\Stock\AtributoTipo;
use App\Models\Stock\ImagenProducto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    // Códigos
    public $codigo_barras = '';
    public $codigo_fabricante = '';

    // Información básica
    public $nombre = '';
    public $descripcion = '';
    public $modelo = '';
    public $aplicacion = '';

    // Clasificación
    public $tipo = 'PRODUCTO';
    public $origen = 'NACIONAL';

    // Relaciones
    public $categoria_id = null;
    public $marca_id = null;
    public $unidad_medida_id = null;

    // Flags
    public $permite_venta = true;
    public $permite_compra = true;
    public $maneja_stock = true;

    // Stock
    public $stock_minimo = 0;
    public $stock_maximo = null;

    // Atributos dinámicos
    public $atributos = [];
    public $tiene_atributos = false;
    public $tiene_codigos = false;

    // Imágenes
    public $imagenes = [];
    public $imagen_principal_index = null;

    protected function rules()
    {
        return [
            'codigo_barras' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(Producto::class, 'codigo_barras')->whereNull('deleted_at')
            ],
            'codigo_fabricante' => ['nullable', 'string', 'max:50'],
            'nombre' => [
                'required',
                'string',
                'max:200',
                Rule::unique(Producto::class, 'nombre')->whereNull('deleted_at')
            ],
            'descripcion' => ['nullable', 'string'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'aplicacion' => ['nullable', 'string'],
            'tipo' => ['required', 'in:PRODUCTO,INSUMO,SERVICIO,KIT'],
            'origen' => ['required', 'in:NACIONAL,IMPORTADO'],
            'categoria_id' => [
                'nullable',
                Rule::exists(Categoria::class, 'id')->whereNull('deleted_at')
            ],
            'marca_id' => [
                'nullable',
                Rule::exists(Marca::class, 'id')->whereNull('deleted_at')
            ],
            'unidad_medida_id' => [
                'required',
                Rule::exists(UnidadMedida::class, 'id')->whereNull('deleted_at')
            ],
            'permite_venta' => ['boolean'],
            'permite_compra' => ['boolean'],
            'maneja_stock' => ['boolean'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'stock_maximo' => ['nullable', 'numeric', 'min:0'],
            'imagenes.*' => ['nullable', 'image', 'max:2048'], // max 2MB
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.unique' => 'Este nombre ya está registrado.',
        'codigo_barras.unique' => 'Este código de barras ya está registrado.',
        'unidad_medida_id.required' => 'La unidad de medida es obligatoria.',
        'tipo.required' => 'El tipo es obligatorio.',
        'origen.required' => 'El origen es obligatorio.',
    ];

    public function guardar()
    {
        $this->validate();

        $producto = Producto::create([
            'codigo_barras' => $this->codigo_barras,
            'codigo_fabricante' => $this->codigo_fabricante,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'modelo' => $this->modelo,
            'aplicacion' => $this->aplicacion,
            'tipo' => $this->tipo,
            'origen' => $this->origen,
            'categoria_id' => $this->categoria_id,
            'marca_id' => $this->marca_id,
            'unidad_medida_id' => $this->unidad_medida_id,
            'permite_venta' => $this->permite_venta,
            'permite_compra' => $this->permite_compra,
            'maneja_stock' => $this->maneja_stock,
            'stock_minimo' => $this->stock_minimo ?? 0,
            'stock_maximo' => $this->stock_maximo,
            'activo' => true,
            'creadoPor' => Auth::id(),
        ]);

        // Guardar atributos
        if (!empty($this->atributos)) {
            $atributosSync = [];
            foreach ($this->atributos as $atributoId => $valor) {
                if (!empty($valor)) {
                    $atributosSync[$atributoId] = ['valor' => $valor];
                }
            }
            $producto->atributos()->sync($atributosSync);
        }

        // Guardar imágenes
        if (!empty($this->imagenes)) {
            foreach ($this->imagenes as $index => $imagen) {
                $path = $imagen->store('productos', 'public');

                ImagenProducto::create([
                    'producto_id' => $producto->id,
                    'path' => $path,
                    'nombre_original' => $imagen->getClientOriginalName(),
                    'es_principal' => $index == $this->imagen_principal_index,
                    'orden' => $index,
                ]);
            }
        }

        session()->flash('success', 'Producto creado correctamente!');
        return redirect()->route('stock.productos.index');
    }

    public function eliminarImagen($index)
    {
        unset($this->imagenes[$index]);
        $this->imagenes = array_values($this->imagenes);

        // Ajustar el índice principal si es necesario
        if ($this->imagen_principal_index == $index) {
            $this->imagen_principal_index = null;
        } elseif ($this->imagen_principal_index > $index) {
            $this->imagen_principal_index--;
        }
    }

    public function establecerPrincipal($index)
    {
        $this->imagen_principal_index = $index;
    }

    public function render()
    {
        $categorias = Categoria::activas()->ordenadas()->get();
        $marcas = Marca::where('activo', true)->orderBy('nombre')->get();
        $unidadesMedida = UnidadMedida::where('activo', true)->orderBy('nombre')->get();
        $atributosTipo = AtributoTipo::where('activo', true)->orderBy('orden')->orderBy('nombre')->get();

        return view('livewire.stock.productos.create', compact('categorias', 'marcas', 'unidadesMedida', 'atributosTipo'));
    }
}
