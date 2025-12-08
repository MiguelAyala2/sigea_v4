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
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $productoId;

    // Códigos
    public $codigo = '';
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
    public $imagenes_existentes = [];
    public $imagen_principal_id = null;

    public function mount(Producto $producto)
    {
        $this->productoId = $producto->id;
        $this->codigo = $producto->codigo;
        $this->codigo_barras = $producto->codigo_barras;
        $this->codigo_fabricante = $producto->codigo_fabricante;
        $this->nombre = $producto->nombre;
        $this->descripcion = $producto->descripcion;
        $this->modelo = $producto->modelo;
        $this->aplicacion = $producto->aplicacion;
        $this->tipo = $producto->tipo;
        $this->origen = $producto->origen;
        $this->categoria_id = $producto->categoria_id;
        $this->marca_id = $producto->marca_id;
        $this->unidad_medida_id = $producto->unidad_medida_id;
        $this->permite_venta = $producto->permite_venta;
        $this->permite_compra = $producto->permite_compra;
        $this->maneja_stock = $producto->maneja_stock;
        $this->stock_minimo = $producto->stock_minimo;
        $this->stock_maximo = $producto->stock_maximo;

        // Cargar atributos existentes
        foreach ($producto->atributos as $atributo) {
            $this->atributos[$atributo->id] = $atributo->pivot->valor;
        }

        // Si el producto tiene atributos, activar el checkbox
        $this->tiene_atributos = $producto->atributos->count() > 0;

        // Si el producto tiene códigos, activar el checkbox
        $this->tiene_codigos = !empty($producto->codigo_barras) || !empty($producto->codigo_fabricante);

        // Cargar imágenes existentes
        $this->imagenes_existentes = $producto->imagenes->toArray();
        $imagenPrincipal = $producto->imagenes->where('es_principal', true)->first();
        $this->imagen_principal_id = $imagenPrincipal ? $imagenPrincipal->id : null;
    }

    protected function rules()
    {
        return [
            'codigo_barras' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(Producto::class, 'codigo_barras')
                    ->ignore($this->productoId)
                    ->whereNull('deleted_at')
            ],
            'codigo_fabricante' => ['nullable', 'string', 'max:50'],
            'nombre' => [
                'required',
                'string',
                'max:200',
                Rule::unique(Producto::class, 'nombre')
                    ->ignore($this->productoId)
                    ->whereNull('deleted_at')
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

    public function guardar()
    {
        $this->validate();

        $producto = Producto::findOrFail($this->productoId);

        $producto->update([
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
            'actualizadoPor' => Auth::id(),
        ]);

        // Actualizar atributos
        if (!empty($this->atributos)) {
            $atributosSync = [];
            foreach ($this->atributos as $atributoId => $valor) {
                if (!empty($valor)) {
                    $atributosSync[$atributoId] = ['valor' => $valor];
                }
            }
            $producto->atributos()->sync($atributosSync);
        } else {
            $producto->atributos()->detach();
        }

        // Actualizar imagen principal en imágenes existentes
        if ($this->imagen_principal_id) {
            ImagenProducto::where('producto_id', $producto->id)->update(['es_principal' => false]);
            ImagenProducto::where('id', $this->imagen_principal_id)->update(['es_principal' => true]);
        }

        // Guardar nuevas imágenes
        if (!empty($this->imagenes)) {
            $ultimoOrden = $producto->imagenes()->max('orden') ?? -1;
            foreach ($this->imagenes as $index => $imagen) {
                $path = $imagen->store('productos', 'public');

                ImagenProducto::create([
                    'producto_id' => $producto->id,
                    'path' => $path,
                    'nombre_original' => $imagen->getClientOriginalName(),
                    'es_principal' => false,
                    'orden' => $ultimoOrden + $index + 1,
                ]);
            }
        }

        session()->flash('success', 'Producto actualizado correctamente!');
        return redirect()->route('stock.productos.index');
    }

    public function eliminarImagenExistente($imagenId)
    {
        $imagen = ImagenProducto::find($imagenId);
        if ($imagen && $imagen->producto_id == $this->productoId) {
            $imagen->delete();
            $this->imagenes_existentes = array_filter($this->imagenes_existentes, function($img) use ($imagenId) {
                return $img['id'] != $imagenId;
            });

            // Si era la principal, limpiar
            if ($this->imagen_principal_id == $imagenId) {
                $this->imagen_principal_id = null;
            }
        }
    }

    public function eliminarImagenNueva($index)
    {
        unset($this->imagenes[$index]);
        $this->imagenes = array_values($this->imagenes);
    }

    public function establecerPrincipalExistente($imagenId)
    {
        $this->imagen_principal_id = $imagenId;
    }

    public function render()
    {
        $categorias = Categoria::activas()->ordenadas()->get();
        $marcas = Marca::where('activo', true)->orderBy('nombre')->get();
        $unidadesMedida = UnidadMedida::where('activo', true)->orderBy('nombre')->get();
        $atributosTipo = AtributoTipo::where('activo', true)->orderBy('orden')->orderBy('nombre')->get();

        return view('livewire.stock.productos.edit', compact('categorias', 'marcas', 'unidadesMedida', 'atributosTipo'));
    }
}
