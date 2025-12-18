<div>
    <form wire:submit.prevent="guardar">
        <!-- Mensajes de error/éxito -->
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <!-- Paso 1: Buscar Proveedor -->
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">1. Seleccionar Proveedor</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Buscar Proveedor *</label>
                            <div class="position-relative">
                                <input type="text"
                                       wire:model.live.debounce.500ms="search_proveedor"
                                       class="form-control"
                                       placeholder="Buscar por nombre, razón social o RUC..."
                                       autocomplete="off">

                                <!-- Dropdown de proveedores -->
                                @if($mostrar_proveedores && count($proveedores_encontrados) > 0)
                                <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                    @foreach($proveedores_encontrados as $proveedor)
                                    <a href="javascript:void(0)"
                                       wire:click="seleccionarProveedor({{ $proveedor['id'] }})"
                                       class="list-group-item list-group-item-action">
                                        <strong>{{ $proveedor['nombre_fantasia'] }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $proveedor['razon_social'] }} - RUC: {{ $proveedor['ruc'] }}</small>
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            @if($proveedor_seleccionado)
                                <small class="text-success mt-2 d-block">
                                    <i class="fas fa-check-circle"></i> Proveedor seleccionado: {{ $proveedor_seleccionado->nombre_fantasia }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paso 2: Seleccionar Orden de Compra -->
        @if($proveedor_seleccionado)
        <div class="card">
            <div class="card-header bg-secondary">
                <h3 class="card-title">2. Seleccionar Orden de Compra</h3>
            </div>
            <div class="card-body">
                @if(count($ordenes_disponibles) > 0)
                    @if(!$orden_seleccionada)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Se encontraron {{ count($ordenes_disponibles) }} órdenes de compra disponibles. Seleccione una:
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Número de Orden</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordenes_disponibles as $orden)
                                <tr>
                                    <td><strong>{{ $orden['numero_orden'] }}</strong></td>
                                    <td>{{ $orden['fecha_orden'] }}</td>
                                    <td>
                                        @switch($orden['estado'])
                                            @case('CONFIRMADA')
                                                <span class="badge badge-warning">Confirmada</span>
                                                @break
                                            @case('COMPLETAMENTE_RECIBIDA')
                                                <span class="badge badge-success">Recibida</span>
                                                @break
                                            @default
                                                <span class="badge badge-info">{{ $orden['estado'] }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-right">₲ {{ number_format($orden['total'], 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $orden['cantidad_items'] }}</td>
                                    <td class="text-center">
                                        <button type="button"
                                                wire:click="seleccionarOrden({{ $orden['id'] }})"
                                                class="btn btn-sm btn-primary">
                                            <i class="fas fa-check"></i> Seleccionar
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Orden de Compra seleccionada: <strong>{{ $orden_seleccionada->numero_orden }}</strong>
                    </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No hay órdenes de compra confirmadas disponibles para este proveedor.
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Paso 3: Datos de la Factura -->
        @if($orden_seleccionada)
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title text-white">3. Datos de la Factura</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Número de Factura *</label>
                            <input type="text" wire:model="numero_factura" class="form-control @error('numero_factura') is-invalid @enderror" required>
                            @error('numero_factura') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Timbrado</label>
                            <input type="text" wire:model="timbrado" class="form-control" maxlength="20">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de Emisión *</label>
                            <input type="date" wire:model="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror" required>
                            @error('fecha_emision') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de Factura *</label>
                            <select wire:model.live="tipo_factura" class="form-control" required>
                                <option value="CONTADO">Contado</option>
                                <option value="CREDITO">Crédito</option>
                            </select>
                        </div>
                    </div>

                    @if($tipo_factura === 'CREDITO')
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Condición de Pago *</label>
                            <select wire:model="condicion_pago" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="7_DIAS">7 Días</option>
                                <option value="15_DIAS">15 Días</option>
                                <option value="30_DIAS">30 Días</option>
                                <option value="60_DIAS">60 Días</option>
                                <option value="90_DIAS">90 Días</option>
                            </select>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea wire:model="observaciones" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalle de Productos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detalle de Productos</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Precio Unit.</th>
                                <th class="text-center">IVA %</th>
                                <th class="text-right">Subtotal</th>
                                <th class="text-right">IVA</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detalles as $detalle)
                            <tr>
                                <td>{{ $detalle['producto_codigo'] }}</td>
                                <td>{{ $detalle['producto_nombre'] }}</td>
                                <td class="text-right">{{ number_format($detalle['cantidad'], 2) }}</td>
                                <td class="text-right">₲ {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}</td>
                                <td class="text-center">{{ $detalle['iva_porcentaje'] }}%</td>
                                <td class="text-right">₲ {{ number_format($detalle['subtotal'], 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($detalle['iva_monto'], 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($detalle['total'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-right">SUBTOTAL:</td>
                                <td colspan="3" class="text-right">₲ {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">IVA 10%:</td>
                                <td colspan="3" class="text-right">₲ {{ number_format($iva_10, 0, ',', '.') }}</td>
                            </tr>
                            @if($iva_5 > 0)
                            <tr>
                                <td colspan="5" class="text-right">IVA 5%:</td>
                                <td colspan="3" class="text-right">₲ {{ number_format($iva_5, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($exenta > 0)
                            <tr>
                                <td colspan="5" class="text-right">EXENTA:</td>
                                <td colspan="3" class="text-right">₲ {{ number_format($exenta, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr class="table-success">
                                <td colspan="5" class="text-right">TOTAL:</td>
                                <td colspan="3" class="text-right">₲ {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Guardar Compra
                </button>
                <a href="{{ route('compras.compras.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
        @endif
    </form>

    <style>
    .list-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    </style>
</div>
