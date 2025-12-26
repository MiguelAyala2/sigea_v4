@extends('adminlte::page')

@section('title', 'Editar Nota de Débito')

@section('content_header')
    <h1>Editar Nota de Débito #{{ $notaDebito->numero }}</h1>
@stop

@section('content')
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Solo se pueden editar notas de crédito en estado <strong>borrador</strong>.
    </div>

    <form action="{{ route('compras.notas-debito.update', $notaDebito) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Proveedor *</label>
                            <input type="text" class="form-control" value="{{ $notaDebito->proveedor->razon_social ?? '' }}" readonly style="background-color: #e9ecef;">
                            <small class="text-muted">El proveedor no puede modificarse</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número de Nota de Débito</label>
                            <input type="text" class="form-control" value="{{ $notaDebito->numero }}" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">Fecha de Emisión *</label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $notaDebito->fecha->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_factura_afectada">Número Factura Afectada</label>
                            <input type="text" name="numero_factura_afectada" id="numero_factura_afectada" class="form-control" value="{{ old('numero_factura_afectada', $notaDebito->numero_factura_afectada) }}" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="motivo">Motivo *</label>
                            <select name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" required>
                                <option value="">Seleccione un motivo</option>
                                <option value="Intereses por mora en el pago" {{ old('motivo', $notaDebito->motivo) == 'Intereses por mora en el pago' ? 'selected' : '' }}>Intereses por mora en el pago</option>
                                <option value="Gastos de envío no incluidos inicialmente" {{ old('motivo', $notaDebito->motivo) == 'Gastos de envío no incluidos inicialmente' ? 'selected' : '' }}>Gastos de envío no incluidos inicialmente</option>
                                <option value="Corrección de subfacturación" {{ old('motivo', $notaDebito->motivo) == 'Corrección de subfacturación' ? 'selected' : '' }}>Corrección de subfacturación</option>
                                <option value="Cargos adicionales acordados" {{ old('motivo', $notaDebito->motivo) == 'Cargos adicionales acordados' ? 'selected' : '' }}>Cargos adicionales acordados</option>
                            </select>
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
                            <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Información adicional sobre la nota de débito">{{ old('observaciones', $notaDebito->observaciones) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detalle de Items</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Producto</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Descuento</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notaDebito->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                                <td>{{ $detalle->descripcion }}</td>
                                <td class="text-right">{{ number_format($detalle->cantidad, 2) }}</td>
                                <td class="text-right">{{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($detalle->descuento, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($detalle->impuesto, 0, ',', '.') }}</td>
                                <td class="text-right"><strong>{{ number_format($detalle->total, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-right">SUBTOTAL:</td>
                                <td class="text-right">{{ number_format($notaDebito->subtotal, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($notaDebito->impuesto, 0, ',', '.') }}</td>
                                <td class="text-right"><strong>{{ number_format($notaDebito->total, 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> Los items no pueden modificarse. Si necesita cambiar los productos, cancele esta nota y cree una nueva.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('compras.notas-debito.show', $notaDebito) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </form>
@stop

@section('css')
@stop

@section('js')
@stop
