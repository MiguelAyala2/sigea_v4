@extends('adminlte::page')

@section('title', 'Nueva Nota de Débito')

@section('content_header')
    <h1>Nueva Nota de Débito</h1>
@stop

@section('content')
    <form action="{{ route('compras.notas-debito.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="proveedor_id">Proveedor *</label>
                            <select name="proveedor_id" id="proveedor_id" class="form-control @error('proveedor_id') is-invalid @enderror" required>
                                <option value="">Seleccione un proveedor</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                            @error('proveedor_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="compra_id">Compra Relacionada</label>
                            <select name="compra_id" id="compra_id" class="form-control">
                                <option value="">Ninguna</option>
                                @foreach($compras as $compra)
                                    <option value="{{ $compra->id }}">{{ $compra->numero_factura }} - {{ $compra->proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero">Número *</label>
                            <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" required>
                            @error('numero')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">Fecha *</label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                            @error('fecha')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_factura_afectada">Número Factura Afectada</label>
                            <input type="text" name="numero_factura_afectada" id="numero_factura_afectada" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="motivo">Motivo *</label>
                            <textarea name="motivo" id="motivo" rows="3" class="form-control @error('motivo') is-invalid @enderror" required></textarea>
                            @error('motivo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detalle de Items</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="agregarDetalle()">
                        <i class="fas fa-plus"></i> Agregar Item
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="tabla-detalles">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Descripción</th>
                            <th width="100">Cantidad</th>
                            <th width="120">Precio Unit.</th>
                            <th width="100">Descuento</th>
                            <th width="50">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalles-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="{{ route('compras.notas-debito.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
let detalleIndex = 0;

function agregarDetalle() {
    const html = `
        <tr id="detalle-${detalleIndex}">
            <td>
                <select name="detalles[${detalleIndex}][producto_id]" class="form-control">
                    <option value="">Ninguno</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="detalles[${detalleIndex}][descripcion]" class="form-control" required>
            </td>
            <td>
                <input type="number" name="detalles[${detalleIndex}][cantidad]" class="form-control" step="0.01" min="0.01" required>
            </td>
            <td>
                <input type="number" name="detalles[${detalleIndex}][precio_unitario]" class="form-control" step="0.01" min="0" required>
            </td>
            <td>
                <input type="number" name="detalles[${detalleIndex}][descuento]" class="form-control" step="0.01" min="0" value="0">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(${detalleIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    document.getElementById('detalles-body').insertAdjacentHTML('beforeend', html);
    detalleIndex++;
}

function eliminarDetalle(index) {
    document.getElementById(`detalle-${index}`).remove();
}

// Agregar un detalle por defecto
document.addEventListener('DOMContentLoaded', function() {
    agregarDetalle();
});
</script>
@stop
