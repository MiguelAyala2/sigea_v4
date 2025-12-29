<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-file-alt"></i> Datos de la Remisión
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Búsqueda de Cliente --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="buscar_cliente">Buscar Cliente <span class="text-danger">*</span></label>

                        @if($cliente_seleccionado)
                            {{-- Cliente seleccionado --}}
                            <div class="input-group">
                                <input type="text"
                                       class="form-control bg-light"
                                       value="{{ $cliente_seleccionado->nombre_razon_social }} - {{ $cliente_seleccionado->ruc_ci }}"
                                       readonly>
                                <div class="input-group-append">
                                    <button type="button"
                                            wire:click="limpiarCliente"
                                            class="btn btn-warning"
                                            title="Cambiar cliente">
                                        <i class="fas fa-times"></i> Cambiar
                                    </button>
                                </div>
                            </div>

                            {{-- Información adicional del cliente --}}
                            <div class="mt-2 p-2 bg-light border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">RUC/CI:</small><br>
                                        <strong>{{ $cliente_seleccionado->ruc_ci }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Teléfono:</small><br>
                                        <strong>{{ $cliente_seleccionado->telefono ?? $cliente_seleccionado->celular ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Email:</small><br>
                                        <strong>{{ $cliente_seleccionado->email ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                @if($cliente_seleccionado->direccion)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <small class="text-muted">Dirección:</small><br>
                                            <strong>{{ $cliente_seleccionado->direccion }}</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- Campo de búsqueda --}}
                            <div style="position: relative;">
                                <input type="text"
                                       wire:model.live.debounce.300ms="buscar_cliente"
                                       class="form-control @error('cliente_id') is-invalid @enderror"
                                       id="buscar_cliente"
                                       placeholder="Escriba el nombre o documento del cliente..."
                                       autocomplete="off">

                                {{-- Resultados de búsqueda --}}
                                @if($mostrar_resultados_cliente && count($clientes_encontrados) > 0)
                                    <div class="list-group" style="position: absolute; z-index: 1000; width: 100%; max-height: 300px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        @foreach($clientes_encontrados as $cliente)
                                            <button type="button"
                                                    wire:click="seleccionarCliente({{ $cliente->id }})"
                                                    class="list-group-item list-group-item-action">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $cliente->nombre_razon_social }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-id-card"></i> {{ $cliente->ruc_ci }}
                                                            @if($cliente->telefono || $cliente->celular)
                                                                | <i class="fas fa-phone"></i> {{ $cliente->telefono ?? $cliente->celular }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif($mostrar_resultados_cliente && strlen($buscar_cliente) >= 2)
                                    <div class="alert alert-info mt-2 mb-0">
                                        <i class="fas fa-info-circle"></i> No se encontraron clientes con ese criterio de búsqueda.
                                    </div>
                                @endif

                                @if(strlen($buscar_cliente) > 0 && strlen($buscar_cliente) < 2)
                                    <small class="text-muted">Escriba al menos 2 caracteres para buscar</small>
                                @endif
                            </div>
                            @error('cliente_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            </div>

            {{-- Factura Origen (solo visible cuando hay cliente seleccionado) --}}
            @if($cliente_seleccionado)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="factura_id">
                                Factura Origen (opcional)
                                @if(count($facturas) > 0)
                                    <span class="badge badge-success">{{ count($facturas) }} factura(s) disponible(s)</span>
                                @endif
                            </label>

                            @if(count($facturas) > 0)
                                <select wire:model.live="factura_id" class="form-control" id="factura_id">
                                    <option value="">Seleccione una factura o cree remisión manual</option>
                                    @foreach($facturas as $factura)
                                        <option value="{{ $factura->id }}">
                                            {{ $factura->numero_factura }} -
                                            ₲ {{ number_format($factura->total, 0, ',', '.') }} -
                                            {{ $factura->fecha_emision->format('d/m/Y') }} -
                                            {{ $factura->estado }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Seleccione una factura para cargar automáticamente sus productos, o deje en blanco para agregar productos manualmente.
                                </small>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Este cliente no tiene facturas emitidas. Puede agregar productos manualmente.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                {{-- Fecha Emisión --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_emision">Fecha Emisión <span class="text-danger">*</span></label>
                        <input type="date" wire:model="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror" id="fecha_emision">
                        @error('fecha_emision') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Fecha Entrega --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_entrega">Fecha Entrega Estimada</label>
                        <input type="date" wire:model="fecha_entrega" class="form-control" id="fecha_entrega">
                    </div>
                </div>

                {{-- Sucursal --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sucursal_id">Sucursal <span class="text-danger">*</span></label>
                        <select wire:model.live="sucursal_id" class="form-control @error('sucursal_id') is-invalid @enderror" id="sucursal_id">
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                        @error('sucursal_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Depósito --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="deposito_id">Depósito <span class="text-danger">*</span></label>
                        <select wire:model="deposito_id" class="form-control @error('deposito_id') is-invalid @enderror" id="deposito_id">
                            @foreach($depositos as $deposito)
                                <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
                            @endforeach
                        </select>
                        @error('deposito_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Dirección de Entrega --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="direccion_entrega">Dirección de Entrega</label>
                        <input type="text" wire:model="direccion_entrega" class="form-control @error('direccion_entrega') is-invalid @enderror" id="direccion_entrega" placeholder="Dirección de entrega...">
                        @error('direccion_entrega') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Observaciones --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="observaciones">Observaciones</label>
                        <textarea wire:model="observaciones" class="form-control" id="observaciones" rows="2" placeholder="Observaciones generales..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalles de la Remisión --}}
    <div class="card">
        <div class="card-header bg-info">
            <h3 class="card-title">
                <i class="fas fa-boxes"></i> Productos a Remitir
            </h3>
            <div class="card-tools">
                <button type="button" wire:click="agregarDetalle" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(empty($detalles))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay productos agregados.
                    @if($cliente_id && count($facturas) > 0)
                        Seleccione una factura para cargar sus productos automáticamente, o agregue productos manualmente.
                    @else
                        Haga clic en "Agregar Producto" para añadir productos manualmente.
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40%">Descripción <span class="text-danger">*</span></th>
                                <th style="width: 12%">Cantidad <span class="text-danger">*</span></th>
                                <th style="width: 12%">Unidad</th>
                                <th style="width: 15%">Precio Unit.</th>
                                <th style="width: 15%">Subtotal</th>
                                <th style="width: 6%" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $index => $detalle)
                                <tr>
                                    <td>
                                        <input type="text"
                                               wire:model="detalles.{{ $index }}.producto_descripcion"
                                               class="form-control form-control-sm @error('detalles.'.$index.'.producto_descripcion') is-invalid @enderror"
                                               placeholder="Descripción del producto">
                                        @error('detalles.'.$index.'.producto_descripcion')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number"
                                               wire:model="detalles.{{ $index }}.cantidad"
                                               wire:change="calcularSubtotal({{ $index }})"
                                               class="form-control form-control-sm @error('detalles.'.$index.'.cantidad') is-invalid @enderror"
                                               step="0.01"
                                               min="0">
                                        @error('detalles.'.$index.'.cantidad')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text"
                                               wire:model="detalles.{{ $index }}.unidad_medida"
                                               class="form-control form-control-sm"
                                               placeholder="UND">
                                    </td>
                                    <td>
                                        <input type="number"
                                               wire:model="detalles.{{ $index }}.precio_unitario"
                                               wire:change="calcularSubtotal({{ $index }})"
                                               class="form-control form-control-sm"
                                               step="0.01"
                                               min="0">
                                    </td>
                                    <td>
                                        <input type="number"
                                               wire:model="detalles.{{ $index }}.subtotal"
                                               class="form-control form-control-sm"
                                               readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                wire:click="eliminarDetalle({{ $index }})"
                                                class="btn btn-danger btn-xs"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Botones de Acción --}}
    <div class="card">
        <div class="card-body">
            <button type="button"
                    wire:click="guardar"
                    class="btn btn-success"
                    {{ empty($detalles) || !$cliente_id ? 'disabled' : '' }}>
                <i class="fas fa-save"></i> Guardar Remisión
            </button>
            <a href="{{ route('ventas.remisiones.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>

            @if(empty($detalles) || !$cliente_id)
                <span class="text-muted ml-3">
                    <i class="fas fa-info-circle"></i>
                    Complete los datos requeridos para guardar la remisión
                </span>
            @endif
        </div>
    </div>
</div>
