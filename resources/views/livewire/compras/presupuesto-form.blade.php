<div>
    <form wire:submit.prevent="guardar">
        <!-- Mensajes de error/éxito -->
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <!-- Buscar Proveedor -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>Proveedor *</label>
                    <div class="position-relative">
                        <input type="text"
                               wire:model.live.debounce.500ms="search_proveedor"
                               class="form-control"
                               placeholder="Buscar proveedor por nombre o RUC..."
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
                        <small class="text-success">
                            <i class="fas fa-check"></i> {{ $proveedor_seleccionado->nombre_fantasia }}
                        </small>
                    @endif
                </div>
            </div>

            <!-- Buscar Pedido de Compra -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>Pedido de Compra (Opcional)</label>
                    <div class="position-relative">
                        <input type="text"
                               wire:model.live.debounce.500ms="search_pedido"
                               class="form-control"
                               placeholder="Buscar pedido de compra..."
                               autocomplete="off">

                        <!-- Dropdown de pedidos -->
                        @if($mostrar_pedidos && count($pedidos_encontrados) > 0)
                        <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                            @foreach($pedidos_encontrados as $pedido)
                            <a href="javascript:void(0)"
                               wire:click="seleccionarPedido({{ $pedido['id'] }})"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $pedido['numero_pedido'] }}</strong> - {{ $pedido['tipo_pedido'] }}
                                        <br>
                                        <small class="text-muted">{{ $pedido['fecha_pedido'] }}</small>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-primary">₲ {{ number_format($pedido['total_estimado'], 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    @if($pedido_seleccionado)
                        <small class="text-success">
                            <i class="fas fa-check"></i> {{ $pedido_seleccionado->numero_pedido }}
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Presupuesto -->
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Solicitud *</label>
                    <input type="date" wire:model="fecha_solicitud" class="form-control @error('fecha_solicitud') is-invalid @enderror" required>
                    @error('fecha_solicitud') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Vencimiento</label>
                    <input type="date" wire:model="fecha_vencimiento" class="form-control">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Condición de Pago *</label>
                    <select wire:model.live="condicion_pago" class="form-control" required>
                        <option value="CONTADO">Contado</option>
                        <option value="CREDITO">Crédito</option>
                    </select>
                </div>
            </div>

            @if($condicion_pago === 'CREDITO')
            <div class="col-md-3">
                <div class="form-group">
                    <label>Plazo de Pago *</label>
                    <select wire:model="plazo_pago" class="form-control" required>
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

            <div class="col-md-3">
                <div class="form-group">
                    <label>Días Entrega</label>
                    <input type="number" wire:model="dias_entrega" class="form-control" min="0" placeholder="0">
                </div>
            </div>
        </div>

        <hr>

        <!-- Productos del Presupuesto -->
        <h4>Productos Cotizados</h4>

        @if(session()->has('error_producto'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error_producto') }}
            </div>
        @endif

        @if(count($detalles) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Marca</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Precio Unit.</th>
                        <th class="text-center">IVA %</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center" width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $index => $detalle)
                    <tr class="{{ $editando_index === $index ? 'table-warning' : '' }}">
                        <td>{{ $detalle['producto_codigo'] }}</td>
                        <td>{{ $detalle['producto_nombre'] }}</td>

                        @if($editando_index === $index)
                            <td>
                                <input type="text" wire:model.defer="marca_ofrecida_edit" class="form-control form-control-sm" placeholder="Marca">
                            </td>
                        @else
                            <td>{{ $detalle['marca_ofrecida'] ?? '-' }}</td>
                        @endif

                        <td class="text-right">{{ number_format($detalle['cantidad_cotizada'], 2) }}</td>

                        @if($editando_index === $index)
                            <td class="text-right">
                                <input type="number" wire:model.defer="precio_unitario_edit" class="form-control form-control-sm" step="0.01" min="0" placeholder="Precio">
                            </td>
                            <td class="text-center">
                                <select wire:model.defer="iva_porcentaje_edit" class="form-control form-control-sm">
                                    <option value="0">Exenta</option>
                                    <option value="5">5%</option>
                                    <option value="10">10%</option>
                                </select>
                            </td>
                        @else
                            <td class="text-right">₲ {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detalle['iva_porcentaje'] }}%</td>
                        @endif

                        <td class="text-right">₲ {{ number_format($this->calcularSubtotal($detalle) + $this->calcularIva($detalle), 0, ',', '.') }}</td>

                        <td class="text-center">
                            @if($editando_index === $index)
                                <button type="button" wire:click.prevent="guardarEdicionDetalle" class="btn btn-sm btn-success" title="Guardar cambios">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" wire:click.prevent="cancelarEdicion" class="btn btn-sm btn-secondary" title="Cancelar">
                                    <i class="fas fa-times"></i>
                                </button>
                            @else
                                <button type="button" wire:click.prevent="editarDetalle({{ $index }})" class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" wire:click.prevent="eliminarDetalle({{ $index }})" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Eliminar este producto?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $totales = $this->calcularTotal();
                    @endphp
                    <tr class="font-weight-bold">
                        <td colspan="6" class="text-right">TOTAL:</td>
                        <td class="text-right">₲ {{ number_format($totales['total'], 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No hay productos agregados. Seleccione un pedido de compra para cargar los productos automáticamente.
        </div>
        @endif

        <!-- Observaciones -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea wire:model="observaciones" class="form-control" rows="3" placeholder="Observaciones del presupuesto..."></textarea>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Guardar Presupuesto
                </button>
                <a href="{{ route('compras.presupuestos.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>

    @if($editando_index !== null)
    <div class="alert alert-info mt-2">
        <i class="fas fa-info-circle"></i> Editando producto en posición {{ $editando_index + 1 }}
    </div>
    @endif

    <style>
    .list-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .table-warning {
        background-color: #fff3cd !important;
    }
    </style>

    @push('js')
    <script>
        // Debug: Mostrar cuando se hace clic en editar
        document.addEventListener('livewire:load', function () {
            console.log('Livewire cargado correctamente');
        });
    </script>
    @endpush
</div>
