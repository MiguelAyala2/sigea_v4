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
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="search_proveedor">Buscar Proveedor (por nombre o RUC) *</label>
                            <input type="hidden" name="proveedor_id" id="proveedor_id" required>
                            <div class="position-relative">
                                <input type="text"
                                       id="search_proveedor"
                                       class="form-control @error('proveedor_id') is-invalid @enderror"
                                       placeholder="Escriba para buscar por nombre, razón social o RUC..."
                                       autocomplete="off">
                                <!-- Dropdown de proveedores -->
                                <div id="proveedores-dropdown" class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                            </div>
                            <small id="proveedor-seleccionado" class="text-success mt-2 d-none">
                                <i class="fas fa-check-circle"></i> <span id="proveedor-nombre"></span>
                            </small>
                            @error('proveedor_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_compra_filtro">Fecha de Compra *</label>
                            <input type="date" id="fecha_compra_filtro" class="form-control" value="{{ date('Y-m-d') }}">
                            <small class="form-text text-muted">Para filtrar facturas</small>
                        </div>
                    </div>
                </div>

                <!-- Detalle del Proveedor y Factura Seleccionada -->
                <div class="row">
                    <div class="col-md-12">
                        <div id="detalle-seleccion" class="alert alert-info d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Proveedor:</strong> <span id="detalle-proveedor"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Factura:</strong> <span id="detalle-factura">Ninguna seleccionada</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="compra_id">Factura Relacionada</label>
                            <select name="compra_id" id="compra_id" class="form-control">
                                <option value="">Primero seleccione proveedor y fecha</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="motivo">Motivo *</label>
                            <select name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" required>
                                <option value="">Seleccione un motivo</option>
                                <option value="Intereses por mora en el pago">Intereses por mora en el pago</option>
                                <option value="Gastos de envío no incluidos inicialmente">Gastos de envío no incluidos inicialmente</option>
                                <option value="Corrección de subfacturación">Corrección de subfacturación</option>
                                <option value="Cargos adicionales acordados">Cargos adicionales acordados</option>
                            </select>
                            @error('motivo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero">Número de Nota de Débito *</label>
                            <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" required readonly value="{{ $numeroAutomatico }}" style="background-color: #e9ecef;">
                            <small class="form-text text-muted">Número generado automáticamente</small>
                            @error('numero')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">Fecha de Emisión *</label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                            @error('fecha')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_factura_afectada">Número Factura Afectada</label>
                            <input type="text" name="numero_factura_afectada" id="numero_factura_afectada" class="form-control" placeholder="Opcional">
                            <small class="form-text text-muted">Se completará automáticamente si selecciona una factura</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="2" class="form-control" placeholder="Información adicional sobre la nota de débito"></textarea>
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
let proveedoresData = @json($proveedores);
let proveedorSeleccionado = null;
let productosData = @json($productos);

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

function buscarProveedores(searchTerm) {
    if (searchTerm.length < 2) {
        $('#proveedores-dropdown').hide().html('');
        return;
    }

    const filtrados = proveedoresData.filter(p => {
        const nombre = (p.nombre_fantasia || p.nombre || '').toLowerCase();
        const razon = (p.razon_social || '').toLowerCase();
        const ruc = (p.ruc || '').toLowerCase();
        const search = searchTerm.toLowerCase();

        return nombre.includes(search) || razon.includes(search) || ruc.includes(search);
    });

    if (filtrados.length === 0) {
        $('#proveedores-dropdown').html('<div class="list-group-item text-muted">No se encontraron proveedores</div>').show();
        return;
    }

    let html = '';
    filtrados.slice(0, 10).forEach(proveedor => {
        const nombreMostrar = proveedor.nombre_fantasia || proveedor.nombre;
        const razonMostrar = proveedor.razon_social || nombreMostrar;
        html += `
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick="seleccionarProveedor(${proveedor.id})">
                <strong>${nombreMostrar}</strong><br>
                <small class="text-muted">${razonMostrar} - RUC: ${proveedor.ruc || 'N/A'}</small>
            </a>
        `;
    });

    $('#proveedores-dropdown').html(html).show();
}

function seleccionarProveedor(proveedorId) {
    proveedorSeleccionado = proveedoresData.find(p => p.id === proveedorId);

    if (!proveedorSeleccionado) return;

    const nombreMostrar = proveedorSeleccionado.nombre_fantasia || proveedorSeleccionado.nombre;
    const razonMostrar = proveedorSeleccionado.razon_social || nombreMostrar;

    $('#proveedor_id').val(proveedorId);
    $('#search_proveedor').val(nombreMostrar);
    $('#proveedor-nombre').text(nombreMostrar);
    $('#proveedor-seleccionado').removeClass('d-none');
    $('#proveedores-dropdown').hide();

    // Actualizar detalle
    $('#detalle-proveedor').text(`${nombreMostrar} (${razonMostrar}) - RUC: ${proveedorSeleccionado.ruc || 'N/A'}`);
    $('#detalle-seleccion').removeClass('d-none');

    // Cargar compras
    cargarComprasPorProveedorFecha();
}

function cargarComprasPorProveedorFecha() {
    const proveedorId = $('#proveedor_id').val();
    const fecha = $('#fecha_compra_filtro').val();

    if (!proveedorId || !fecha) {
        $('#compra_id').html('<option value="">Primero seleccione proveedor y fecha</option>');
        $('#detalle-factura').text('Ninguna seleccionada');
        return;
    }

    // Mostrar loading
    $('#compra_id').html('<option value="">Cargando compras...</option>');

    // Realizar petición AJAX
    $.ajax({
        url: '{{ route('compras.notas-debito.api.compras-por-proveedor-fecha') }}',
        method: 'GET',
        data: {
            proveedor_id: proveedorId,
            fecha: fecha
        },
        success: function(compras) {
            if (compras.length === 0) {
                $('#compra_id').html('<option value="">No hay compras aprobadas para esta fecha</option>');
                $('#detalle-factura').text('Ninguna disponible para esta fecha');
            } else {
                let options = '<option value="">Seleccione una factura (opcional)</option>';
                compras.forEach(function(compra) {
                    options += `<option value="${compra.id}"
                        data-numero="${compra.numero_factura}"
                        data-fecha="${compra.fecha_emision}"
                        data-tipo="${compra.tipo_factura}"
                        data-total="${compra.total}">
                        ${compra.numero_factura} | ${compra.fecha_emision} | ${compra.tipo_factura} | Total: ₲ ${compra.total}
                    </option>`;
                });
                $('#compra_id').html(options);
            }
        },
        error: function() {
            $('#compra_id').html('<option value="">Error al cargar compras</option>');
            alert('Error al cargar las compras. Por favor, intente nuevamente.');
        }
    });
}

function actualizarDetalleFactura() {
    const selectedOption = $('#compra_id option:selected');

    if (!selectedOption.val()) {
        $('#detalle-factura').text('Ninguna seleccionada');
        $('#numero_factura_afectada').val('');
        // Limpiar detalles
        $('#detalles-body').empty();
        detalleIndex = 0;
        agregarDetalle();
        return;
    }

    const compraId = selectedOption.val();
    const numero = selectedOption.data('numero');
    const fecha = selectedOption.data('fecha');
    const tipo = selectedOption.data('tipo');
    const total = selectedOption.data('total');

    $('#detalle-factura').html(`${numero} - ${fecha} - ${tipo} - <strong>Total: ₲ ${total}</strong>`);

    // Autocompletar el número de factura afectada
    $('#numero_factura_afectada').val(numero);

    // Cargar detalles de la compra
    cargarDetallesCompra(compraId);
}

function cargarDetallesCompra(compraId) {
    $.ajax({
        url: `/compras/notas-debito/api/compra/${compraId}/detalles`,
        method: 'GET',
        success: function(detalles) {
            // Limpiar tabla de detalles
            $('#detalles-body').empty();
            detalleIndex = 0;

            if (detalles.length === 0) {
                agregarDetalle();
                return;
            }

            // Agregar cada detalle de la compra
            detalles.forEach(function(detalle) {
                // Generar opciones de productos
                let productosOptions = '<option value="">Ninguno</option>';
                productosData.forEach(function(producto) {
                    const selected = producto.id === detalle.producto_id ? 'selected' : '';
                    productosOptions += `<option value="${producto.id}" ${selected}>${producto.nombre}</option>`;
                });

                const html = `
                    <tr id="detalle-${detalleIndex}">
                        <td>
                            <select name="detalles[${detalleIndex}][producto_id]" class="form-control">
                                ${productosOptions}
                            </select>
                        </td>
                        <td>
                            <input type="text" name="detalles[${detalleIndex}][descripcion]" class="form-control" value="${detalle.descripcion}" required>
                        </td>
                        <td>
                            <input type="number" name="detalles[${detalleIndex}][cantidad]" class="form-control" step="0.01" min="0.01" value="${detalle.cantidad}" required>
                        </td>
                        <td>
                            <input type="number" name="detalles[${detalleIndex}][precio_unitario]" class="form-control" step="0.01" min="0" value="${detalle.precio_unitario}" required>
                        </td>
                        <td>
                            <input type="number" name="detalles[${detalleIndex}][descuento]" class="form-control" step="0.01" min="0" value="${detalle.descuento}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(${detalleIndex})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#detalles-body').append(html);
                detalleIndex++;
            });
        },
        error: function() {
            alert('Error al cargar los detalles de la factura.');
            $('#detalles-body').empty();
            detalleIndex = 0;
            agregarDetalle();
        }
    });
}

// Agregar un detalle por defecto
document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda de proveedores con debounce
    let timeoutId;
    $('#search_proveedor').on('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            buscarProveedores($(this).val());
        }, 300);
    });

    // Ocultar dropdown al hacer click fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search_proveedor, #proveedores-dropdown').length) {
            $('#proveedores-dropdown').hide();
        }
    });

    // Cargar compras cuando cambie la fecha
    $('#fecha_compra_filtro').on('change', function() {
        cargarComprasPorProveedorFecha();
    });

    // Actualizar detalle cuando se selecciona una factura
    $('#compra_id').on('change', function() {
        actualizarDetalleFactura();
    });

    agregarDetalle();
});
</script>
@stop
