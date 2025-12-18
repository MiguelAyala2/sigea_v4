@extends('adminlte::page')

@section('title', 'Nueva Remisión')

@section('content_header')
    <h1>Nueva Remisión</h1>
@stop

@section('content')
    <form action="{{ route('compras.remisiones.store') }}" method="POST" id="form-remision">
        @csrf
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tipo">Tipo de Remisión *</label>
                            <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                                <option value="">Seleccione...</option>
                                <option value="INTERNA" {{ old('tipo') == 'INTERNA' ? 'selected' : '' }}>Interna (Entre Sucursales)</option>
                                <option value="EXTERNA" {{ old('tipo') == 'EXTERNA' ? 'selected' : '' }}>Externa (A Cliente)</option>
                            </select>
                            @error('tipo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="numero">Número de Remisión *</label>
                            <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror"
                                   value="{{ old('numero', $numeroRemision) }}" readonly required>
                            @error('numero')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha">Fecha *</label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="numero_guia_proveedor">N° Guía</label>
                            <input type="text" name="numero_guia_proveedor" id="numero_guia_proveedor" class="form-control" value="{{ old('numero_guia_proveedor') }}">
                        </div>
                    </div>
                </div>

                <!-- Campos para Remisión EXTERNA (Cliente) -->
                <div id="campos-externa" style="display: none;">
                    <h5 class="mt-3 mb-3">Datos del Cliente</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_nombre">Nombre del Cliente *</label>
                                <input type="text" name="cliente_nombre" id="cliente_nombre"
                                       class="form-control @error('cliente_nombre') is-invalid @enderror"
                                       value="{{ old('cliente_nombre') }}">
                                @error('cliente_nombre')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cliente_ruc">RUC/CI</label>
                                <input type="text" name="cliente_ruc" id="cliente_ruc" class="form-control" value="{{ old('cliente_ruc') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cliente_telefono">Teléfono</label>
                                <input type="text" name="cliente_telefono" id="cliente_telefono" class="form-control" value="{{ old('cliente_telefono') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_direccion">Dirección</label>
                                <input type="text" name="cliente_direccion" id="cliente_direccion" class="form-control" value="{{ old('cliente_direccion') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_email">Email</label>
                                <input type="email" name="cliente_email" id="cliente_email" class="form-control" value="{{ old('cliente_email') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos para Remisión INTERNA (Sucursal) -->
                <div id="campos-interna" style="display: none;">
                    <h5 class="mt-3 mb-3">Datos de Sucursal Destino</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sucursal_destino_id">Sucursal Destino *</label>
                                <select name="sucursal_destino_id" id="sucursal_destino_id"
                                        class="form-control @error('sucursal_destino_id') is-invalid @enderror">
                                    <option value="">Seleccione una sucursal</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ old('sucursal_destino_id') == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->codigo_establecimiento }} - {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sucursal_destino_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="deposito_destino_id">Depósito Destino *</label>
                                <select name="deposito_destino_id" id="deposito_destino_id"
                                        class="form-control @error('deposito_destino_id') is-invalid @enderror" disabled>
                                    <option value="">Seleccione primero una sucursal</option>
                                </select>
                                @error('deposito_destino_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="info-sucursal" style="display: none;">
                                <strong>Información de la Sucursal:</strong>
                                <div id="datos-sucursal"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transportista">Transportista</label>
                            <input type="text" name="transportista" id="transportista" class="form-control" value="{{ old('transportista') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="placa_vehiculo">Placa Vehículo</label>
                            <input type="text" name="placa_vehiculo" id="placa_vehiculo" class="form-control" value="{{ old('placa_vehiculo') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="direccion_entrega">Dirección de Entrega</label>
                            <input type="text" name="direccion_entrega" id="direccion_entrega" class="form-control" value="{{ old('direccion_entrega') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="2" class="form-control">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detalle de Productos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="agregarDetalle()">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm" id="tabla-detalles">
                    <thead class="bg-secondary">
                        <tr>
                            <th width="35%">Producto *</th>
                            <th width="30%">Descripción</th>
                            <th width="15%">Cantidad</th>
                            <th width="17%">Observaciones</th>
                            <th width="3%">
                                <i class="fas fa-trash"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="detalles-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Remisión
                </button>
                <a href="{{ route('compras.remisiones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let detalleIndex = 0;
const productos = @json($productos);

$(document).ready(function() {
    // Agregar un detalle por defecto
    agregarDetalle();

    // Manejar cambio de tipo de remisión
    $('#tipo').on('change', function() {
        const tipo = $(this).val();

        if (tipo === 'EXTERNA') {
            $('#campos-externa').show();
            $('#campos-interna').hide();
            $('#cliente_nombre').prop('required', true);
            $('#sucursal_destino_id').prop('required', false);
            $('#deposito_destino_id').prop('required', false);
        } else if (tipo === 'INTERNA') {
            $('#campos-externa').hide();
            $('#campos-interna').show();
            $('#cliente_nombre').prop('required', false);
            $('#sucursal_destino_id').prop('required', true);
            $('#deposito_destino_id').prop('required', true);
        } else {
            $('#campos-externa').hide();
            $('#campos-interna').hide();
        }
    });

    // Manejar cambio de sucursal
    $('#sucursal_destino_id').on('change', function() {
        const sucursalId = $(this).val();
        const $depositoSelect = $('#deposito_destino_id');

        if (sucursalId) {
            // Cargar depósitos de la sucursal
            $.ajax({
                url: `/compras/remisiones/sucursal/${sucursalId}/depositos`,
                method: 'GET',
                success: function(depositos) {
                    $depositoSelect.html('<option value="">Seleccione un depósito</option>');
                    depositos.forEach(function(deposito) {
                        $depositoSelect.append(`<option value="${deposito.id}">${deposito.codigo} - ${deposito.nombre}</option>`);
                    });
                    $depositoSelect.prop('disabled', false);
                },
                error: function() {
                    alert('Error al cargar los depósitos');
                }
            });

            // Cargar datos de la sucursal
            $.ajax({
                url: `/compras/remisiones/sucursal/${sucursalId}/datos`,
                method: 'GET',
                success: function(sucursal) {
                    let html = `
                        <p class="mb-1"><strong>Nombre:</strong> ${sucursal.nombre}</p>
                        <p class="mb-1"><strong>Dirección:</strong> ${sucursal.direccion || 'N/A'}</p>
                        <p class="mb-1"><strong>Ciudad:</strong> ${sucursal.ciudad || 'N/A'}</p>
                        <p class="mb-0"><strong>Teléfono:</strong> ${sucursal.telefono || 'N/A'}</p>
                    `;
                    $('#datos-sucursal').html(html);
                    $('#info-sucursal').show();
                },
                error: function() {
                    $('#info-sucursal').hide();
                }
            });
        } else {
            $depositoSelect.html('<option value="">Seleccione primero una sucursal</option>');
            $depositoSelect.prop('disabled', true);
            $('#info-sucursal').hide();
        }
    });

    // Trigger change si hay valor antiguo
    if ($('#tipo').val()) {
        $('#tipo').trigger('change');
    }
    if ($('#sucursal_destino_id').val()) {
        $('#sucursal_destino_id').trigger('change');
    }
});

function agregarDetalle() {
    let optionsProductos = '<option value="">Seleccione un producto</option>';
    productos.forEach(function(producto) {
        optionsProductos += `<option value="${producto.id}" data-nombre="${producto.nombre}" data-codigo="${producto.codigo}">${producto.codigo} - ${producto.nombre}</option>`;
    });

    const currentIndex = detalleIndex;
    const html = `
        <tr id="detalle-${currentIndex}">
            <td>
                <select name="detalles[${currentIndex}][producto_id]" id="producto-select-${currentIndex}" class="form-control form-control-sm select-producto" data-index="${currentIndex}" required>
                    ${optionsProductos}
                </select>
            </td>
            <td>
                <input type="text" name="detalles[${currentIndex}][descripcion]" id="descripcion-${currentIndex}" class="form-control form-control-sm" required>
            </td>
            <td>
                <input type="number" name="detalles[${currentIndex}][cantidad_enviada]" class="form-control form-control-sm" step="0.01" min="0.01" required>
            </td>
            <td>
                <input type="text" name="detalles[${currentIndex}][observaciones]" class="form-control form-control-sm">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(${currentIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    $('#detalles-body').append(html);

    // Inicializar Select2 para el select de producto
    $(`#producto-select-${currentIndex}`).select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar producto por código o nombre...',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron productos";
            },
            searching: function() {
                return "Buscando...";
            }
        },
        matcher: function(params, data) {
            // Si no hay término de búsqueda, mostrar todos
            if ($.trim(params.term) === '') {
                return data;
            }

            // No mostrar la opción por defecto en resultados
            if (data.id === '') {
                return null;
            }

            // Buscar en el texto completo (código + nombre)
            const searchTerm = params.term.toLowerCase();
            const text = data.text.toLowerCase();

            if (text.indexOf(searchTerm) > -1) {
                return data;
            }

            return null;
        }
    });

    // Agregar evento change al select de producto
    $(`#producto-select-${currentIndex}`).on('change', function() {
        const index = $(this).data('index');
        const productoNombre = $(this).find('option:selected').data('nombre');
        $(`#descripcion-${index}`).val(productoNombre || '');
    });

    detalleIndex++;
}

function eliminarDetalle(index) {
    if ($('#detalles-body tr').length > 1) {
        $(`#detalle-${index}`).remove();
    } else {
        alert('Debe haber al menos un producto en la remisión');
    }
}
</script>
@stop
