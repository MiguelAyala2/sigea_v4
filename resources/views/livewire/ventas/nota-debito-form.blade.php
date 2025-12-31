<div>
    <form wire:submit.prevent="guardar">
        <div class="row">
            {{-- Columna izquierda --}}
            <div class="col-md-8">
                {{-- Búsqueda de Factura (opcional) --}}
                @if(!$factura_seleccionada)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">1. Seleccionar Factura (Opcional)</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Puede vincular la nota de débito a una factura existente o crearla sin factura asociada.
                            </p>
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
                                @elseif($mostrar_busqueda_factura && strlen($search_factura) > 2)
                                    <div class="alert alert-warning mt-2">
                                        No se encontraron facturas
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-invoice"></i>
                                <strong>Factura Vinculada:</strong> {{ $factura_seleccionada->numero_completo }} -
                                {{ $factura_seleccionada->cliente->nombre }}
                            </div>
                            <button type="button" wire:click="$set('factura_seleccionada', null)" class="btn btn-sm btn-secondary">
                                <i class="fas fa-times"></i> Desvincular
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Conceptos de la Nota de Débito --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">2. Agregar Conceptos</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Agregue los conceptos a cobrar: intereses, cargos administrativos, ajustes, etc.
                        </p>

                        {{-- Buscar Producto/Concepto --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Buscar Producto/Concepto</label>
                                    <input type="text" wire:model.live.debounce.300ms="search_producto" class="form-control"
                                           placeholder="Buscar producto...">

                                    @if($mostrar_busqueda_producto && count($productos_encontrados) > 0)
                                        <div class="list-group mt-2" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($productos_encontrados as $producto)
                                                <button type="button" wire:click="seleccionarProducto({{ $producto->id }})"
                                                        class="list-group-item list-group-item-action">
                                                    <div>
                                                        <strong>{{ $producto->nombre }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $producto->codigo }}</small>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Descripción Adicional</label>
                                    <input type="text" wire:model="detalle_temp.descripcion_adicional" class="form-control"
                                           placeholder="Ej: Interés por mora del mes de diciembre">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cantidad</label>
                                    <input type="number" wire:model="detalle_temp.cantidad" class="form-control" min="1" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Precio Unitario</label>
                                    <input type="number" wire:model="detalle_temp.precio_unitario" class="form-control" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>IVA %</label>
                                    <select wire:model="detalle_temp.iva_porcentaje" class="form-control">
                                        <option value="10">10%</option>
                                        <option value="5">5%</option>
                                        <option value="0">Exenta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" wire:click="agregarDetalle" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Lista de conceptos agregados --}}
                        @if(count($detalles) > 0)
                            <div class="table-responsive mt-3">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="text-right">Cant.</th>
                                            <th class="text-right">Precio Unit.</th>
                                            <th class="text-right">IVA</th>
                                            <th class="text-right">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles as $index => $detalle)
                                            <tr>
                                                <td>
                                                    <strong>{{ $detalle['producto_nombre'] }}</strong>
                                                    @if($detalle['descripcion_adicional'])
                                                        <br><small class="text-muted">{{ $detalle['descripcion_adicional'] }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ number_format($detalle['cantidad'], 2) }}</td>
                                                <td class="text-right">₲ {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}</td>
                                                <td class="text-right">{{ $detalle['iva_porcentaje'] }}%</td>
                                                <td class="text-right">
                                                    ₲ {{ number_format($detalle['cantidad'] * $detalle['precio_unitario'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" wire:click="eliminarDetalle({{ $index }})"
                                                            class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No hay conceptos agregados. Agregue al menos un concepto para continuar.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Motivo --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">3. Motivo de la Nota de Débito</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Motivo <span class="text-danger">*</span></label>
                            <textarea wire:model="motivo" class="form-control" rows="3"
                                      placeholder="Ej: Intereses por mora en el pago, Cargo administrativo, etc."></textarea>
                            @error('motivo') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea wire:model="observaciones" class="form-control" rows="2"
                                      placeholder="Observaciones adicionales (opcional)"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna derecha --}}
            <div class="col-md-4">
                {{-- Configuración --}}
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">Configuración</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Fecha Emisión <span class="text-danger">*</span></label>
                            <input type="date" wire:model="fecha_emision" class="form-control">
                            @error('fecha_emision') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Timbrado <span class="text-danger">*</span></label>
                            <select wire:model="timbrado_id" class="form-control">
                                <option value="">Seleccionar...</option>
                                @foreach($timbrados as $timbrado)
                                    <option value="{{ $timbrado->id }}">
                                        {{ $timbrado->numero_timbrado }}
                                        ({{ $timbrado->numero_desde }} - {{ $timbrado->numero_hasta }})
                                    </option>
                                @endforeach
                            </select>
                            @error('timbrado_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Punto de Expedición <span class="text-danger">*</span></label>
                            <select wire:model="punto_expedicion_id" class="form-control">
                                <option value="">Seleccionar...</option>
                                @foreach($puntos_expedicion as $punto)
                                    <option value="{{ $punto->id }}">
                                        {{ $punto->nombre }} - {{ $punto->sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('punto_expedicion_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Depósito <span class="text-danger">*</span></label>
                            <select wire:model="deposito_id" class="form-control">
                                <option value="">Seleccionar...</option>
                                @foreach($depositos as $deposito)
                                    <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
                                @endforeach
                            </select>
                            @error('deposito_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" wire:model="es_electronica" class="form-check-input" id="es_electronica">
                            <label class="form-check-label" for="es_electronica">
                                Nota Electrónica
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Totales --}}
                <div class="card">
                    <div class="card-header bg-info">
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
                            <tr>
                                <th>Exenta:</th>
                                <td class="text-right">₲ {{ number_format($exenta, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-active">
                                <th>TOTAL:</th>
                                <th class="text-right">₲ {{ number_format($total, 0, ',', '.') }}</th>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" wire:loading.attr="disabled">
                            <i class="fas fa-save"></i>
                            <span wire:loading.remove>Guardar Nota de Débito</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                        <a href="{{ route('ventas.notas-debito.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
</div>
