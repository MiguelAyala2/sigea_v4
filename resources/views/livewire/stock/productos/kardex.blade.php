<div>
    <x-adminlte-card theme="light" icon="fas fa-list">
        <x-slot name="title">
            Kardex de {{ $producto->nombre }}
        </x-slot>

        <x-slot name="toolsSlot">
            <a href="{{ route('stock.productos.show', $producto->id) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Producto
            </a>
        </x-slot>

        {{-- Información del Producto --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Código:</strong> {{ $producto->codigo }}
                        </div>
                        <div class="col-md-3">
                            <strong>Nombre:</strong> {{ $producto->nombre }}
                        </div>
                        <div class="col-md-3">
                            <strong>Unidad:</strong> {{ $producto->unidadMedida->nombre }}
                        </div>
                        <div class="col-md-3">
                            <strong>Tipo:</strong> {{ $producto->tipo }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter"></i> Filtros
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <x-adminlte-input
                            name="fechaDesde"
                            label="Fecha Desde"
                            type="date"
                            wire:model="fechaDesde"
                        />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-input
                            name="fechaHasta"
                            label="Fecha Hasta"
                            type="date"
                            wire:model="fechaHasta"
                        />
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-select
                            name="tipoMovimiento"
                            label="Tipo de Movimiento"
                            wire:model="tipoMovimiento"
                        >
                            <option value="">Todos</option>
                            <option value="ENTRADA_COMPRA">Entrada por Compra</option>
                            <option value="SALIDA_VENTA">Salida por Venta</option>
                            <option value="AJUSTE_POSITIVO">Ajuste Positivo</option>
                            <option value="AJUSTE_NEGATIVO">Ajuste Negativo</option>
                            <option value="TRANSFERENCIA_ORIGEN">Transferencia (Origen)</option>
                            <option value="TRANSFERENCIA_DESTINO">Transferencia (Destino)</option>
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-select
                            name="depositoId"
                            label="Depósito"
                            wire:model="depositoId"
                        >
                            <option value="">Todos</option>
                            @foreach($depositos as $deposito)
                                <option value="{{ $deposito->id }}">{{ $deposito->nombre }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button wire:click="aplicarFiltros" class="btn btn-primary">
                            <i class="fas fa-search"></i> Aplicar Filtros
                        </button>
                        <button wire:click="limpiarFiltros" class="btn btn-secondary">
                            <i class="fas fa-eraser"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Totales --}}
        <div class="row mt-3 mb-3">
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Entradas</span>
                        <span class="info-box-number">{{ number_format($totales['entradas'], 2) }} {{ $producto->unidadMedida->simbolo }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Salidas</span>
                        <span class="info-box-number">{{ number_format($totales['salidas'], 2) }} {{ $producto->unidadMedida->simbolo }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Saldo Neto</span>
                        <span class="info-box-number">{{ number_format($totales['saldo'], 2) }} {{ $producto->unidadMedida->simbolo }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de Movimientos --}}
        @if($movimientos->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="120">Fecha</th>
                            <th width="180">Tipo</th>
                            <th>Depósito</th>
                            <th width="100" class="text-right">Entradas</th>
                            <th width="100" class="text-right">Salidas</th>
                            <th width="100" class="text-right">Stock Ant.</th>
                            <th width="100" class="text-right">Stock Post.</th>
                            <th>Documento</th>
                            <th>Usuario</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $movimiento)
                            <tr>
                                <td>
                                    <small>{{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $movimiento->tipo_badge }}">
                                        {{ $movimiento->tipo_label }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $movimiento->deposito->nombre }}</small>
                                </td>
                                <td class="text-right">
                                    @if($movimiento->es_entrada)
                                        <span class="text-success font-weight-bold">
                                            +{{ number_format($movimiento->cantidad, 2) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($movimiento->es_salida)
                                        <span class="text-danger font-weight-bold">
                                            -{{ number_format($movimiento->cantidad, 2) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{ number_format($movimiento->stock_anterior, 2) }}
                                </td>
                                <td class="text-right">
                                    <strong>{{ number_format($movimiento->stock_posterior, 2) }}</strong>
                                </td>
                                <td>
                                    @if($movimiento->documento_tipo)
                                        <small>
                                            {{ $movimiento->documento_tipo }}
                                            @if($movimiento->documento_id)
                                                #{{ $movimiento->documento_id }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $movimiento->usuario->name }}</small>
                                </td>
                                <td>
                                    @if($movimiento->motivo)
                                        <small>{{ $movimiento->motivo }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $movimientos->links() }}
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No se encontraron movimientos con los filtros aplicados.
            </div>
        @endif

        {{-- Botones de Exportación --}}
        <div class="mt-3 text-right">
            <button class="btn btn-success" wire:click="exportarExcel" disabled>
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
            <button class="btn btn-danger" wire:click="exportarPDF" disabled>
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </button>
            <small class="text-muted">(Funcionalidad de exportación pendiente)</small>
        </div>
    </x-adminlte-card>
</div>
