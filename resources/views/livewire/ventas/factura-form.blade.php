<div>
    {{-- Mensajes Flash --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Error de validación</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="guardar">
        {{-- Cabecera de la Factura --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Datos de la Factura
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Cliente --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text"
                                       wire:model.live.debounce.300ms="search_cliente"
                                       class="form-control @error('cliente_id') is-invalid @enderror"
                                       placeholder="Buscar por nombre o documento..."
                                       autocomplete="off">

                                @if($mostrar_busqueda_cliente && count($clientes_encontrados) > 0)
                                    <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                        @foreach($clientes_encontrados as $cliente)
                                            <button type="button"
                                                    wire:click="seleccionarCliente({{ $cliente->id }})"
                                                    class="list-group-item list-group-item-action">
                                                <strong>{{ $cliente->nombre }}</strong>
                                                <br><small class="text-muted">Documento: {{ $cliente->documento }}</small>
                                                @if($cliente->tipo_cliente === 'juridica')
                                                    <span class="badge badge-info ml-1">Jurídica</span>
                                                @else
                                                    <span class="badge badge-secondary ml-1">Física</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @error('cliente_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Fecha Emisión --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha Emisión <span class="text-danger">*</span></label>
                            <input type="date" wire:model="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror">
                            @error('fecha_emision') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Tipo de Facturación --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo de Facturación <span class="text-danger">*</span></label>
                            <select wire:model.live="tipo_facturacion" class="form-control">
                                <option value="PEDIDOS">Pedidos</option>
                                <option value="SERVICIOS">Servicios</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Detalle del Cliente Seleccionado --}}
                @if($cliente_seleccionado)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-0">
                                <div class="card-body p-3">
                                    <h6 class="mb-3"><strong>{{ $cliente_seleccionado->nombre }}</strong></h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted">Documento:</small>
                                            <div><strong>{{ $cliente_seleccionado->documento }}</strong></div>
                                        </div>
                                        @if($cliente_seleccionado->celular)
                                            <div class="col-md-3">
                                                <small class="text-muted">Celular:</small>
                                                <div><strong>{{ $cliente_seleccionado->celular }}</strong></div>
                                            </div>
                                        @endif
                                        @if($cliente_seleccionado->email)
                                            <div class="col-md-3">
                                                <small class="text-muted">Email:</small>
                                                <div><strong>{{ $cliente_seleccionado->email }}</strong></div>
                                            </div>
                                        @endif
                                        @if($cliente_seleccionado->direccion)
                                            <div class="col-md-3">
                                                <small class="text-muted">Dirección:</small>
                                                <div><strong>{{ $cliente_seleccionado->direccion }}</strong></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Información de Configuración (Solo lectura) --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-light border">
                            <small class="text-muted"><strong>Configuración de Caja:</strong></small>
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Timbrado</small>
                                    <strong>
                                        @if($timbrado_id)
                                            {{ \App\Models\Empresa\Timbrado::find($timbrado_id)?->numero_timbrado ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Punto de Expedición</small>
                                    <strong>
                                        @if($punto_expedicion_id)
                                            {{ \App\Models\Empresa\PuntoExpedicion::find($punto_expedicion_id)?->nombre ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Sucursal</small>
                                    <strong>
                                        @if($sucursal_id)
                                            {{ \App\Models\Empresa\Sucursal::find($sucursal_id)?->nombre ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Depósito</small>
                                    <strong>
                                        @if($deposito_id)
                                            {{ \App\Models\Empresa\Deposito::find($deposito_id)?->nombre ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Búsqueda de Pedidos --}}
        @if($tipo_facturacion === 'PEDIDOS')
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Buscar Pedido
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Buscar por código de pedido, nombre o documento del cliente</label>
                        <div class="position-relative">
                            <input type="text"
                                   wire:model.live.debounce.300ms="search_pedido"
                                   class="form-control"
                                   placeholder="Buscar por código de pedido, nombre o documento del cliente..."
                                   autocomplete="off">

                            @if($mostrar_busqueda_pedido && count($pedidos_encontrados) > 0)
                                <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                    @foreach($pedidos_encontrados as $pedido)
                                        <button type="button"
                                                wire:click="seleccionarPedido({{ $pedido->id }})"
                                                class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>{{ $pedido->codigo_pedido }}</strong> - Cliente: {{ $pedido->cliente_nombre }}
                                                    <br><small class="text-muted">Total: ₲ {{ number_format($pedido->monto_total, 0, ',', '.') }}</small>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-success">{{ $pedido->estado }}</span>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Búsqueda de Orden de Servicio --}}
        @if($tipo_facturacion === 'SERVICIOS')
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-tools mr-2"></i>
                        Buscar Orden de Servicio
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label>Buscar por código de orden, nombre o documento del cliente</label>
                        <div class="position-relative">
                            <input type="text"
                                   wire:model.live.debounce.300ms="search_orden_servicio"
                                   class="form-control"
                                   placeholder="Buscar por código de orden, nombre o documento del cliente..."
                                   autocomplete="off">

                            @if($mostrar_busqueda_orden && count($ordenes_encontradas) > 0)
                                <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                    @foreach($ordenes_encontradas as $orden)
                                        <button type="button"
                                                wire:click="agregarOrdenServicio({{ $orden->id }})"
                                                class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>{{ $orden->codigo_orden }}</strong> - Cliente: {{ $orden->cliente_nombre }}
                                                    <br><small class="text-muted">Total: ₲ {{ number_format($orden->monto_total, 0, ',', '.') }}</small>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-success">Entregada</span>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Productos del Pedido (solo para PEDIDOS) --}}
        @if($tipo_facturacion === 'PEDIDOS' && count($detalles) > 0)
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-box mr-2"></i>
                        Productos del Pedido
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th style="width: 15%;">Código</th>
                                    <th style="width: 50%;">Producto</th>
                                    <th style="width: 10%;" class="text-center">Cantidad</th>
                                    <th style="width: 12%;" class="text-right">Precio Unit.</th>
                                    <th style="width: 13%;" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $detalle)
                                    <tr>
                                        <td><strong>{{ $detalle['producto_codigo'] }}</strong></td>
                                        <td>{{ $detalle['producto_nombre'] }}</td>
                                        <td class="text-center"><strong>{{ $detalle['cantidad'] }}</strong></td>
                                        <td class="text-right"><strong>₲ {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}</strong></td>
                                        <td class="text-right"><strong>₲ {{ number_format($detalle['subtotal'], 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-right"><strong>SUBTOTAL PRODUCTOS:</strong></td>
                                    <td class="text-right"><strong>₲ {{ number_format(array_sum(array_column($detalles, 'subtotal')), 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr class="bg-success text-white">
                                    <td colspan="4" class="text-right"><strong>TOTAL PRODUCTOS:</strong></td>
                                    <td class="text-right"><strong>₲ {{ number_format(array_sum(array_column($detalles, 'subtotal')), 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tipos de Servicio (solo para SERVICIOS) --}}
        @if(count($detalles_servicios) > 0)
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-tools mr-2"></i>
                        Tipos de Servicio
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th style="width: 15%;">Código</th>
                                    <th style="width: 50%;">Tipo de Servicio</th>
                                    <th style="width: 10%;" class="text-center">Cantidad</th>
                                    <th style="width: 12%;" class="text-right">Precio Unitario</th>
                                    <th style="width: 13%;" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles_servicios as $index => $servicio)
                                    <tr>
                                        <td><strong>{{ $servicio['codigo'] }}</strong></td>
                                        <td>{{ $servicio['tipo_servicio'] }}</td>
                                        <td class="text-center"><strong>{{ $servicio['cantidad'] }}</strong></td>
                                        <td class="text-right"><strong>₲ {{ number_format($servicio['precio_unitario'], 0, ',', '.') }}</strong></td>
                                        <td class="text-right"><strong>₲ {{ number_format($servicio['subtotal'], 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-right"><strong>SUBTOTAL SERVICIOS:</strong></td>
                                    <td class="text-right"><strong>₲ {{ number_format($subtotal_servicios, 0, ',', '.') }}</strong></td>
                                </tr>
                                @if($descuento_servicios > 0)
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Descuento Promoción:</strong></td>
                                        <td class="text-right text-danger"><strong>- ₲ {{ number_format($descuento_servicios, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endif
                                <tr class="bg-info text-white">
                                    <td colspan="4" class="text-right"><strong>TOTAL SERVICIOS:</strong></td>
                                    <td class="text-right"><strong>₲ {{ number_format($total_servicios, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Repuestos Necesarios --}}
        @if(count($detalles_repuestos) > 0)
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>
                        Repuestos Necesarios
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-warning text-dark">
                                <tr>
                                    <th style="width: 15%;">Código</th>
                                    <th style="width: 50%;">Repuesto</th>
                                    <th style="width: 10%;" class="text-center">Cantidad</th>
                                    <th style="width: 12%;" class="text-right">Precio Unitario</th>
                                    <th style="width: 13%;" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles_repuestos as $index => $repuesto)
                                    <tr>
                                        <td><strong>{{ $repuesto['codigo'] }}</strong></td>
                                        <td>{{ $repuesto['repuesto'] }}</td>
                                        <td class="text-center"><strong>{{ $repuesto['cantidad'] }}</strong></td>
                                        <td class="text-right"><strong>₲ {{ number_format($repuesto['precio_unitario'], 0, ',', '.') }}</strong></td>
                                        <td class="text-right"><strong>₲ {{ number_format($repuesto['subtotal'], 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-right"><strong>SUBTOTAL REPUESTOS:</strong></td>
                                    <td class="text-right"><strong>₲ {{ number_format($subtotal_repuestos, 0, ',', '.') }}</strong></td>
                                </tr>
                                @if($descuento_repuestos > 0)
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Descuento Aplicado:</strong></td>
                                        <td class="text-right text-danger"><strong>- ₲ {{ number_format($descuento_repuestos, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @endif
                                <tr class="bg-warning text-dark">
                                    <td colspan="4" class="text-right"><strong>TOTAL REPUESTOS:</strong></td>
                                    <td class="text-right"><strong>₲ {{ number_format($total_repuestos, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Total General --}}
        @if(count($detalles) > 0 || count($detalles_servicios) > 0 || count($detalles_repuestos) > 0)
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">TOTAL GENERAL</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td class="text-right"><strong>Subtotal General:</strong></td>
                            <td class="text-right" style="width: 200px;"><strong>₲ {{ number_format($subtotal, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-right"><strong>IVA 10%:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($iva_10, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-right"><strong>IVA 5%:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($iva_5, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-right"><strong>Exenta:</strong></td>
                            <td class="text-right"><strong>₲ {{ number_format($exenta, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr class="bg-success text-white">
                            <td class="text-right" style="font-size: 1.1rem;"><strong>TOTAL GENERAL:</strong></td>
                            <td class="text-right" style="font-size: 1.1rem;"><strong>₲ {{ number_format($total, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        {{-- Condición de Pago --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Condición de Pago
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de Pago <span class="text-danger">*</span></label>
                            <select wire:model.live="condicion_pago" class="form-control @error('condicion_pago') is-invalid @enderror">
                                <option value="CONTADO">CONTADO (Pago Inmediato)</option>
                                <option value="CREDITO">CRÉDITO (Pago en Cuotas)</option>
                            </select>
                            @error('condicion_pago') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($condicion_pago === 'CREDITO')
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cantidad de Cuotas <span class="text-danger">*</span></label>
                                <select wire:model.live="cantidad_cuotas" class="form-control @error('cantidad_cuotas') is-invalid @enderror">
                                    <option value="1">1 cuota</option>
                                    <option value="2">2 cuotas</option>
                                    <option value="3">3 cuotas</option>
                                    <option value="4">4 cuotas</option>
                                    <option value="5">5 cuotas</option>
                                    <option value="6">6 cuotas</option>
                                </select>
                                @error('cantidad_cuotas') <span class="text-danger small">{{ $message }}</span> @enderror
                                <small class="text-muted">Vencimiento cada 30 días</small>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tabla de Cuotas (solo si es CREDITO) --}}
                @if($condicion_pago === 'CREDITO' && count($cuotas) > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5><i class="fas fa-calendar-alt mr-2"></i>Plan de Cuotas</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th style="width: 20%;">Cuota N°</th>
                                            <th style="width: 40%;">Monto</th>
                                            <th style="width: 40%;">Fecha Vencimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cuotas as $cuota)
                                            <tr>
                                                <td class="text-center"><strong>Cuota {{ $cuota['numero_cuota'] }}</strong></td>
                                                <td class="text-right">₲ {{ number_format($cuota['monto'], 0, ',', '.') }}</td>
                                                <td class="text-center">{{ \Carbon\Carbon::parse($cuota['fecha_vencimiento'])->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th class="text-right">TOTAL:</th>
                                            <th class="text-right">₲ {{ number_format(array_sum(array_column($cuotas, 'monto')), 0, ',', '.') }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Formas de Pago (solo si es CONTADO) --}}
                @if($condicion_pago === 'CONTADO')
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5><i class="fas fa-cash-register mr-2"></i>Formas de Pago</h5>
                        </div>
                    </div>

                    {{-- Agregar Forma de Pago --}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Forma de Pago</label>
                                <select wire:model="forma_pago_temp.forma_pago" class="form-control">
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="CHEQUE">Cheque</option>
                                    <option value="TARJETA_DEBITO">Tarjeta Débito</option>
                                    <option value="TARJETA_CREDITO">Tarjeta Crédito</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="QR">QR</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Monto</label>
                                <input type="number"
                                       wire:model="forma_pago_temp.monto"
                                       class="form-control"
                                       step="0.01"
                                       min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Referencia</label>
                                <input type="text"
                                       wire:model="forma_pago_temp.referencia"
                                       class="form-control"
                                       placeholder="Nro. cheque, voucher, etc.">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button"
                                        wire:click="agregarFormaPago"
                                        class="btn btn-primary btn-block">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Lista de Formas de Pago --}}
                    @if(count($formas_pago) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Forma de Pago</th>
                                        <th>Monto</th>
                                        <th>Referencia</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formas_pago as $index => $fp)
                                        <tr>
                                            <td>{{ str_replace('_', ' ', $fp['forma_pago']) }}</td>
                                            <td>₲ {{ number_format($fp['monto'], 0, ',', '.') }}</td>
                                            <td>{{ $fp['referencia'] }}</td>
                                            <td>
                                                <button type="button"
                                                        wire:click="eliminarFormaPago({{ $index }})"
                                                        class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info">
                            <strong>Total Pagado:</strong> ₲ {{ number_format($total_pagado, 0, ',', '.') }}<br>
                            <strong>Saldo Pendiente:</strong> ₲ {{ number_format($saldo_pendiente, 0, ',', '.') }}
                            @if($saldo_pendiente > 0)
                                <span class="text-danger ml-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Debe completar el pago
                                </span>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Totales --}}
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width: 50%;">Subtotal:</th>
                                <td class="text-right"><strong>₲ {{ number_format($subtotal, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <th>IVA 10%:</th>
                                <td class="text-right"><strong>₲ {{ number_format($iva_10, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <th>IVA 5%:</th>
                                <td class="text-right"><strong>₲ {{ number_format($iva_5, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <th>Exenta:</th>
                                <td class="text-right"><strong>₲ {{ number_format($exenta, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr class="border-top">
                                <th><h4 class="mb-0">TOTAL:</h4></th>
                                <td class="text-right"><h4 class="mb-0 text-success"><strong>₲ {{ number_format($total, 0, ',', '.') }}</strong></h4></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('ventas.facturas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Factura
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
