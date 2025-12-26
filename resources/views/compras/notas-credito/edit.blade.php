@extends('adminlte::page')

@section('title', 'Editar Nota de Crédito')

@section('content_header')
    <h1>Editar Nota de Crédito #{{ $notaCredito->numero }}</h1>
@stop

@section('content')
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Solo se pueden editar notas de crédito en estado <strong>borrador</strong>.
    </div>

    <form action="{{ route('compras.notas-credito.update', $notaCredito) }}" method="POST">
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
                            <input type="text" class="form-control" value="{{ $notaCredito->proveedor->razon_social ?? '' }}" readonly style="background-color: #e9ecef;">
                            <small class="text-muted">El proveedor no puede modificarse</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número de Nota de Crédito</label>
                            <input type="text" class="form-control" value="{{ $notaCredito->numero }}" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">Fecha de Emisión *</label>
                            <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $notaCredito->fecha->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_factura_afectada">Número Factura Afectada</label>
                            <input type="text" name="numero_factura_afectada" id="numero_factura_afectada" class="form-control" value="{{ old('numero_factura_afectada', $notaCredito->numero_factura_afectada) }}" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="motivo">Motivo *</label>
                            <select name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" required>
                                <option value="">Seleccione un motivo</option>
                                <option value="Devolución de mercadería defectuosa o equivocada" {{ old('motivo', $notaCredito->motivo) == 'Devolución de mercadería defectuosa o equivocada' ? 'selected' : '' }}>Devolución de mercadería defectuosa o equivocada</option>
                                <option value="Descuentos posteriores a la factura" {{ old('motivo', $notaCredito->motivo) == 'Descuentos posteriores a la factura' ? 'selected' : '' }}>Descuentos posteriores a la factura</option>
                                <option value="Corrección de sobrefacturación" {{ old('motivo', $notaCredito->motivo) == 'Corrección de sobrefacturación' ? 'selected' : '' }}>Corrección de sobrefacturación</option>
                                <option value="Bonificaciones acordadas después" {{ old('motivo', $notaCredito->motivo) == 'Bonificaciones acordadas después' ? 'selected' : '' }}>Bonificaciones acordadas después</option>
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
                            <textarea name="observaciones" id="observaciones" rows="3" class="form-control" placeholder="Información adicional sobre la nota de crédito">{{ old('observaciones', $notaCredito->observaciones) }}</textarea>
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
                            @foreach($notaCredito->detalles as $detalle)
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
                                <td class="text-right">{{ number_format($notaCredito->subtotal, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($notaCredito->impuesto, 0, ',', '.') }}</td>
                                <td class="text-right"><strong>{{ number_format($notaCredito->total, 0, ',', '.') }}</strong></td>
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
                <a href="{{ route('compras.notas-credito.show', $notaCredito) }}" class="btn btn-secondary">
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
