<div>
    <form wire:submit.prevent="guardar">
        <!-- Mensajes de error/éxito -->
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <!-- Buscar Presupuesto -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Presupuesto Seleccionado (Opcional)</label>
                    <div class="position-relative">
                        <input type="text"
                               wire:model.live.debounce.500ms="search_presupuesto"
                               class="form-control"
                               placeholder="Buscar presupuesto aprobado por número o proveedor..."
                               autocomplete="off">

                        <!-- Dropdown de presupuestos -->
                        @if($mostrar_presupuestos && count($presupuestos_encontrados) > 0)
                        <div class="list-group position-absolute w-100" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                            @foreach($presupuestos_encontrados as $presupuesto)
                            <a href="javascript:void(0)"
                               wire:click="seleccionarPresupuesto({{ $presupuesto['id'] }})"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $presupuesto['numero_presupuesto'] }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $presupuesto['proveedor_nombre'] }}</small>
                                        <br>
                                        <small class="badge badge-{{ $presupuesto['estado'] === 'APROBADO' ? 'success' : 'warning' }}">{{ $presupuesto['estado'] }}</small>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-primary font-weight-bold">₲ {{ number_format($presupuesto['total'], 0, ',', '.') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $presupuesto['fecha_solicitud'] }}</small>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    @if($presupuesto_seleccionado)
                        <small class="text-success mt-2 d-block">
                            <i class="fas fa-check-circle"></i> Presupuesto seleccionado: {{ $presupuesto_seleccionado->numero_presupuesto }}
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Proveedor (solo si hay presupuesto seleccionado) -->
        @if($proveedor_seleccionado)
        <div class="card bg-light mb-3">
            <div class="card-body">
                <h6 class="card-title text-primary"><i class="fas fa-building"></i> Datos del Proveedor</h6>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Nombre Fantasía:</strong> {{ $proveedor_seleccionado->nombre_fantasia }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Razón Social:</strong> {{ $proveedor_seleccionado->razon_social }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>RUC:</strong> {{ $proveedor_seleccionado->ruc }}{{ $proveedor_seleccionado->dv ? '-' . $proveedor_seleccionado->dv : '' }}</p>
                    </div>
                    <div class="col-md-2">
                        <p class="mb-1"><strong>Celular:</strong> {{ $proveedor_seleccionado->telefono_celular ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Información de la Orden -->
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Orden *</label>
                    <input type="date" wire:model="fecha_orden" class="form-control @error('fecha_orden') is-invalid @enderror" required>
                    @error('fecha_orden') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Entrega Esperada</label>
                    <input type="date" wire:model="fecha_entrega_esperada" class="form-control @error('fecha_entrega_esperada') is-invalid @enderror">
                    @error('fecha_entrega_esperada') <span class="text-danger">{{ $message }}</span> @enderror
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
                    <label>Tipo de Orden *</label>
                    <select wire:model="tipo_orden" class="form-control" required>
                        <option value="NORMAL">Normal</option>
                        <option value="URGENTE">Urgente</option>
                        <option value="SERVICIO">Servicio</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea wire:model="observaciones" class="form-control" rows="2" placeholder="Observaciones de la orden">
                </div>
            </div>
        </div>

        <hr>

        <!-- Productos de la Orden -->
        <h4>Productos de la Orden de Compra</h4>

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
                        <td>{{ $detalle['marca'] ?? '-' }}</td>

                        @if($editando_index === $index)
                            <td class="text-right">
                                <input type="number" wire:model.defer="cantidad_ordenada_edit" class="form-control form-control-sm" step="0.01" min="0" placeholder="Cantidad">
                            </td>
                            <td class="text-right">
                                <input type="number" wire:model.defer="precio_unitario_edit" class="form-control form-control-sm" step="0.01" min="0" placeholder="Precio">
                            </td>
                        @else
                            <td class="text-right">{{ number_format($detalle['cantidad_ordenada'], 2) }}</td>
                            <td class="text-right">₲ {{ number_format($detalle['precio_unitario'], 0, ',', '.') }}</td>
                        @endif

                        <td class="text-center">{{ $detalle['iva_porcentaje'] }}%</td>
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
            <i class="fas fa-info-circle"></i> No hay productos agregados. Seleccione un presupuesto para cargar los productos automáticamente.
        </div>
        @endif

        <!-- Observaciones y Condiciones -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea wire:model="observaciones" class="form-control" rows="3" placeholder="Observaciones de la orden..."></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Condiciones Especiales</label>
                    <textarea wire:model="condiciones_especiales" class="form-control" rows="3" placeholder="Condiciones especiales..."></textarea>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Guardar Orden de Compra
                </button>
                <a href="{{ route('compras.ordenes.index') }}" class="btn btn-secondary btn-lg">
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
</div>
