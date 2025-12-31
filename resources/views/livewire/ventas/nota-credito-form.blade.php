<div>
    <form wire:submit.prevent="guardar">
        <div class="row">
            {{-- Columna izquierda --}}
            <div class="col-md-8">
                {{-- Búsqueda de Factura --}}
                @if(!$factura_seleccionada)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">1. Seleccionar Factura</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Buscar Factura</label>
                                <input type="text" wire:model.live.debounce.300ms="search_factura" class="form-control"
                                       placeholder="Buscar por número de factura o cliente...">

                                @if($mostrar_busqueda_factura && count($facturas_encontradas) > 0)
                                    <div class="list-group mt-2" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($facturas_encontradas as $factura)
                                            <button type="button" wire:click="seleccionarFactura({{ $factura->id }})"
                                                    class="list-group-item list-group-item-action">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ $factura->numero_completo }}</strong>
                                                        <br>
                                                        <small>{{ $factura->cliente->nombre }}</small>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="badge badge-primary">{{ $factura->estado }}</span>
                                                        <br>
                                                        <strong>₲ {{ number_format($factura->total, 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Detalles de la Factura Seleccionada --}}
                @if($factura_seleccionada)
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Factura Seleccionada: {{ $factura_seleccionada->numero_completo }}</h3>
                            <div class="card-tools">
                                <button type="button" wire:click="$set('factura_seleccionada', null)" class="btn btn-tool text-white">
                                    <i class="fas fa-times"></i> Cambiar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Cliente:</strong> {{ $factura_seleccionada->cliente->nombre }}</p>
                                    <p><strong>Documento:</strong> {{ $factura_seleccionada->cliente->documento }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fecha:</strong> {{ $factura_seleccionada->fecha_emision->format('d/m/Y') }}</p>
                                    <p><strong>Total:</strong> ₲ {{ number_format($factura_seleccionada->total, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detalles para Nota de Crédito --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">2. Seleccionar Productos a Acreditar</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th width="50">Sel</th>
                                            <th>Producto</th>
                                            <th width="100" class="text-center">Cant. Fact</th>
                                            <th width="120" class="text-center">Cant. NC</th>
                                            <th width="120" class="text-right">Precio Unit</th>
                                            <th width="100" class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles_factura_disponibles as $index => $detalle)
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" wire:click="toggleDetalleFactura({{ $index }})"
                                                           {{ $detalle['seleccionado'] ? 'checked' : '' }}>
                                                </td>
                                                <td>{{ $detalle['producto_nombre'] }}</td>
                                                <td class="text-center">{{ number_format($detalle['cantidad'], 2) }}</td>
                                                <td>
                                                    @if($detalle['seleccionado'])
                                                        <input type="number"
                                                               wire:model.blur="detalles_factura_disponibles.{{ $index }}.cantidad_nc"
                                                               wire:change="actualizarCantidadNC({{ $index }})"
                                                               class="form-control form-control-sm text-center"
                                                               min="0"
                                                               max="{{ $detalle['cantidad'] }}"
                                                               step="0.01">
                                                    @else
                                                        <input type="number" class="form-control form-control-sm text-center" value="0" disabled>
                                                    @endif
                                                </td>
                                                <td class="text-right">₲ {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}</td>
                                                <td class="text-right">
                                                    @if($detalle['seleccionado'])
                                                        ₲ {{ number_format($detalle['cantidad_nc'] * $detalle['precio_unitario'], 0, ',', '.') }}
                                                    @else
                                                        ₲ 0
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if(count($detalles_seleccionados) == 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Debe seleccionar al menos un producto para la nota de crédito
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Motivo y Observaciones --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">3. Motivo y Observaciones</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Motivo <span class="text-danger">*</span></label>
                                <textarea wire:model="motivo" class="form-control @error('motivo') is-invalid @enderror"
                                          rows="3" placeholder="Describa el motivo de la nota de crédito..."></textarea>
                                @error('motivo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea wire:model="observaciones" class="form-control" rows="2"
                                          placeholder="Observaciones adicionales (opcional)"></textarea>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Columna derecha - Resumen --}}
            <div class="col-md-4">
                {{-- Datos de la Nota de Crédito --}}
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Datos de la Nota de Crédito</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Fecha Emisión</label>
                            <input type="date" wire:model="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror">
                            @error('fecha_emision') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Timbrado</label>
                            <select wire:model="timbrado_id" class="form-control @error('timbrado_id') is-invalid @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($timbrados as $t)
                                    <option value="{{ $t->id }}">{{ $t->numero_timbrado }} ({{ $t->fecha_inicio_vigencia->format('d/m/Y') }} - {{ $t->fecha_fin_vigencia->format('d/m/Y') }})</option>
                                @endforeach
                            </select>
                            @error('timbrado_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Punto de Expedición</label>
                            <select wire:model="punto_expedicion_id" class="form-control @error('punto_expedicion_id') is-invalid @enderror" disabled>
                                <option value="">Seleccione...</option>
                                @foreach($puntos_expedicion as $pe)
                                    <option value="{{ $pe->id }}">{{ $pe->nombre }} - {{ $pe->sucursal->nombre }}</option>
                                @endforeach
                            </select>
                            @error('punto_expedicion_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="text-muted">Se usa el mismo punto de expedición de la factura</small>
                        </div>

                        <div class="form-group">
                            <label>Depósito</label>
                            <select wire:model="deposito_id" class="form-control @error('deposito_id') is-invalid @enderror" disabled>
                                <option value="">Seleccione...</option>
                                @foreach($depositos as $dep)
                                    <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                                @endforeach
                            </select>
                            @error('deposito_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="text-muted">Se usa el mismo depósito de la factura</small>
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" wire:model="es_electronica" class="custom-control-input" id="es_electronica">
                            <label class="custom-control-label" for="es_electronica">Nota de Crédito Electrónica</label>
                        </div>
                    </div>
                </div>

                {{-- Totales --}}
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Totales</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Subtotal:</th>
                                <td class="text-right">₲ {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>IVA 10%:</th>
                                <td class="text-right">₲ {{ number_format($iva_10, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>IVA 5%:</th>
                                <td class="text-right">₲ {{ number_format($iva_5, 0, ',', '.') }}</td>
                            </tr>
                            @if($exenta > 0)
                                <tr>
                                    <th>Exenta:</th>
                                    <td class="text-right">₲ {{ number_format($exenta, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Total IVA:</th>
                                <td class="text-right">₲ {{ number_format($total_iva, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-primary">
                                <th>TOTAL NC:</th>
                                <th class="text-right">₲ {{ number_format($total, 0, ',', '.') }}</th>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success btn-block" {{ !$factura_seleccionada || count($detalles_seleccionados) == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-save"></i> Guardar Nota de Crédito
                        </button>
                        <a href="{{ route('ventas.notas-credito.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
