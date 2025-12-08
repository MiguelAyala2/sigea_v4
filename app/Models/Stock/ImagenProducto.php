<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ImagenProducto extends Model
{
    protected $table = 'stock.IMAGENES_PRODUCTO';

    protected $fillable = [
        'producto_id',
        'path',
        'nombre_original',
        'es_principal',
        'orden'
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
            'orden' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // RELACIONES
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // ACCESSORS
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    // MÉTODOS
    public function eliminarArchivo(): void
    {
        if (Storage::exists($this->path)) {
            Storage::delete($this->path);
        }
    }

    // BOOT
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($imagen) {
            $imagen->eliminarArchivo();
        });
    }
}
