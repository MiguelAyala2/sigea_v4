<div>
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exchange-alt mr-2"></i>
                Movimientos de Caja
            </h3>
            <div class="card-tools">
                @if($tiene_apertura_abierta)
                    <button wire:click="abrirModal" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Nuevo Movimiento
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="punto_expedicion_id">Punto de Expedición (Caja)</label>
                        <select
                            wire:model.live="punto_expedicion_id"
                            id="punto_expedicion_id"
                            class="form-control">
                            <option value="">Seleccione un punto de expedición...</option>
                            @foreach($puntos_expedicion as $punto)
                                <option value="{{ $punto->id }}">
                                    {{ $punto->codigo }} - {{ $punto->nombre }} ({{ $punto->sucursal->nombre }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @if(!$tiene_apertura_abierta && $punto_expedicion_id)
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Caja Cerrada</h5>
                    Este punto de expedición no tiene una apertura de caja activa.
                    <a href="{{ route('ventas.caja.apertura') }}" class="btn btn-sm btn-warning mt-2">
                        <i class="fas fa-unlock mr-1"></i>
                        Abrir Caja
                    </a>
                </div>
            @endif

            @if($tiene_apertura_abierta)
                {{-- Información de la apertura actual --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-calendar mr-2"></i>Fecha apertura:</strong><br>
                                        {{ $apertura_actual->fecha_apertura->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-clock mr-2"></i>Hora apertura:</strong><br>
                                        {{ $apertura_actual->hora_apertura }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-user mr-2"></i>Usuario:</strong><br>
                                        {{ $apertura_actual->usuario->name }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="fas fa-wallet mr-2"></i>Saldo inicial:</strong><br>
                                        ₲ {{ number_format($apertura_actual->saldo_inicial, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen de movimientos --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Ingresos</span>
                                <span class="info-box-number">₲ {{ number_format($total_ingresos, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Egresos</span>
                                <span class="info-box-number">₲ {{ number_format($total_egresos, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-wallet"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Saldo Inicial</span>
                                <span class="info-box-number">₲ {{ number_format($apertura_actual->saldo_inicial, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-primary">
                            <span class="info-box-icon"><i class="fas fa-cash-register"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Saldo Actual</span>
                                <span class="info-box-number">₲ {{ number_format($saldo_actual, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabla de movimientos --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo de Facturación</th>
                                <th>Número de Factura</th>
                                <th>Forma de Pago</th>
                                <th>Cliente</th>
                                <th class="text-right">Monto</th>
                                <th>Cajero</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movimientos as $movimiento)
                                <tr>
                                    <td>
                                        {{ $movimiento->fecha_movimiento->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $movimiento->hora_movimiento }}</small>
                                    </td>
                                    <td>
                                        @if($movimiento->factura)
                                            @if($movimiento->factura->condicion_pago === 'CONTADO')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-money-bill-wave"></i> CONTADO
                                                </span>
                                            @else
                                                <span class="badge badge-info">
                                                    <i class="fas fa-calendar-alt"></i> CRÉDITO
                                                    <small>({{ $movimiento->factura->condicion_pago }})</small>
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-hand-holding-usd"></i>
                                                @if($movimiento->es_ingreso)
                                                    {{ $movimiento->tipo_movimiento_texto }}
                                                @else
                                                    {{ $movimiento->tipo_movimiento_texto }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movimiento->factura)
                                            <a href="{{ route('ventas.facturas.show', $movimiento->factura->id) }}"
                                               class="text-primary"
                                               title="Ver factura">
                                                <i class="fas fa-file-invoice"></i>
                                                {{ $movimiento->comprobante_numero }}
                                            </a>
                                        @else
                                            {{ $movimiento->comprobante_numero ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $movimiento->forma_pago === 'EFECTIVO' ? 'success' : 'info' }}">
                                            @if($movimiento->forma_pago === 'EFECTIVO')
                                                <i class="fas fa-money-bill"></i>
                                            @elseif($movimiento->forma_pago === 'TARJETA_DEBITO' || $movimiento->forma_pago === 'TARJETA_CREDITO')
                                                <i class="fas fa-credit-card"></i>
                                            @elseif($movimiento->forma_pago === 'TRANSFERENCIA')
                                                <i class="fas fa-exchange-alt"></i>
                                            @elseif($movimiento->forma_pago === 'CHEQUE')
                                                <i class="fas fa-money-check"></i>
                                            @elseif($movimiento->forma_pago === 'QR')
                                                <i class="fas fa-qrcode"></i>
                                            @endif
                                            {{ $movimiento->forma_pago_texto }}
                                        </span>
                                        @if($movimiento->referencia)
                                            <br><small class="text-muted">Ref: {{ $movimiento->referencia }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movimiento->factura && $movimiento->factura->cliente)
                                            <i class="fas fa-user"></i>
                                            {{ $movimiento->factura->cliente->nombre_razon_social }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <strong class="{{ $movimiento->es_ingreso ? 'text-success' : 'text-danger' }}">
                                            {{ $movimiento->monto_formateado }}
                                        </strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-tie"></i>
                                        {{ $movimiento->usuarioResponsable->name ?? 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No hay movimientos registrados para esta apertura de caja
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>

        @if(session()->has('success'))
            <div class="card-footer">
                <div class="alert alert-success alert-dismissible mb-0">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-check"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="card-footer">
                <div class="alert alert-danger alert-dismissible mb-0">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-ban"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif
    </div>

    {{-- Modal para nuevo movimiento --}}
    @if($showModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Registrar Movimiento de Caja
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form wire:submit.prevent="registrarMovimiento">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_movimiento">
                                            Tipo de Movimiento <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            wire:model="tipo_movimiento"
                                            id="tipo_movimiento"
                                            class="form-control @error('tipo_movimiento') is-invalid @enderror">
                                            <option value="INGRESO">Ingreso</option>
                                            <option value="EGRESO">Egreso</option>
                                            <option value="DEPOSITO">Depósito</option>
                                            <option value="RETIRO">Retiro</option>
                                        </select>
                                        @error('tipo_movimiento')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="forma_pago">
                                            Forma de Pago <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            wire:model="forma_pago"
                                            id="forma_pago"
                                            class="form-control @error('forma_pago') is-invalid @enderror">
                                            <option value="EFECTIVO">Efectivo</option>
                                            <option value="CHEQUE">Cheque</option>
                                            <option value="TARJETA_DEBITO">Tarjeta de Débito</option>
                                            <option value="TARJETA_CREDITO">Tarjeta de Crédito</option>
                                            <option value="TRANSFERENCIA">Transferencia</option>
                                            <option value="QR">QR</option>
                                            <option value="OTRO">Otro</option>
                                        </select>
                                        @error('forma_pago')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="monto">
                                            Monto <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">₲</span>
                                            </div>
                                            <input
                                                type="number"
                                                wire:model="monto"
                                                id="monto"
                                                class="form-control @error('monto') is-invalid @enderror"
                                                placeholder="0"
                                                min="0.01"
                                                step="0.01">
                                            @error('monto')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="descripcion">
                                            Descripción <span class="text-danger">*</span>
                                        </label>
                                        <textarea
                                            wire:model="descripcion"
                                            id="descripcion"
                                            class="form-control @error('descripcion') is-invalid @enderror"
                                            rows="3"
                                            placeholder="Descripción del movimiento..."
                                            maxlength="500"></textarea>
                                        @error('descripcion')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="referencia">Referencia</label>
                                        <input
                                            type="text"
                                            wire:model="referencia"
                                            id="referencia"
                                            class="form-control @error('referencia') is-invalid @enderror"
                                            placeholder="Nro. de comprobante, cheque, etc."
                                            maxlength="100">
                                        @error('referencia')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="cerrarModal">
                                <i class="fas fa-times mr-1"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                Registrar Movimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
