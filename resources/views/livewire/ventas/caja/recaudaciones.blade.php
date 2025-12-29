<div>
    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-hand-holding-usd mr-2"></i>
                Recaudaciones a Depositar
            </h3>
            <div class="card-tools">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Las recaudaciones se generan automáticamente al cerrar la caja
                </small>
            </div>
        </div>

        <div class="card-body">
            {{-- Filtros --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_estado">Estado</label>
                        <select
                            wire:model.live="filtro_estado"
                            id="filtro_estado"
                            class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="DEPOSITADO">Depositado</option>
                            <option value="RECHAZADO">Rechazado</option>
                            <option value="CANCELADO">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filtro_tipo">Tipo</label>
                        <select
                            wire:model.live="filtro_tipo"
                            id="filtro_tipo"
                            class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="CHEQUE">Cheque</option>
                            <option value="TARJETA">Tarjeta</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                            <option value="MIXTO">Mixto</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_desde">Desde</label>
                        <input
                            type="date"
                            wire:model.live="fecha_desde"
                            id="fecha_desde"
                            class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_hasta">Hasta</label>
                        <input
                            type="date"
                            wire:model.live="fecha_hasta"
                            id="fecha_hasta"
                            class="form-control">
                    </div>
                </div>
            </div>

            {{-- Resumen --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pendiente</span>
                            <span class="info-box-number">₲ {{ number_format($total_pendiente, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Depositado Hoy</span>
                            <span class="info-box-number">₲ {{ number_format($total_depositado_hoy, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Vencidas</span>
                            <span class="info-box-number">{{ $vencidas }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de recaudaciones --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Caja/Cierre</th>
                            <th>Tipo</th>
                            <th class="text-right">Monto</th>
                            <th>Fecha Prevista</th>
                            <th>Fecha Real</th>
                            <th>Estado</th>
                            <th>Comprobante</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recaudaciones as $recaudacion)
                            <tr class="{{ $recaudacion->fecha_prevista_deposito && $recaudacion->fecha_prevista_deposito < now()->toDateString() && $recaudacion->estado === 'PENDIENTE' ? 'table-warning' : '' }}">
                                <td>
                                    {{ \Carbon\Carbon::parse($recaudacion->fecha_recaudacion)->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $recaudacion->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($recaudacion->cierreCaja)
                                        <strong>{{ $recaudacion->cierreCaja->aperturaCaja->puntoExpedicion->codigo }}</strong><br>
                                        <small class="text-muted">Cierre #{{ $recaudacion->cierre_caja_id }}</small>
                                    @else
                                        <span class="text-muted">Sin cierre</span>
                                    @endif
                                </td>
                                <td>
                                    @if($recaudacion->tipo_recaudacion === 'EFECTIVO')
                                        <span class="badge badge-success"><i class="fas fa-money-bill-wave"></i> Efectivo</span>
                                    @elseif($recaudacion->tipo_recaudacion === 'CHEQUE')
                                        <span class="badge badge-info"><i class="fas fa-money-check"></i> Cheque</span>
                                    @elseif($recaudacion->tipo_recaudacion === 'TARJETA')
                                        <span class="badge badge-primary"><i class="fas fa-credit-card"></i> Tarjeta</span>
                                    @elseif($recaudacion->tipo_recaudacion === 'TRANSFERENCIA')
                                        <span class="badge badge-secondary"><i class="fas fa-exchange-alt"></i> Transferencia</span>
                                    @else
                                        <span class="badge badge-dark"><i class="fas fa-layer-group"></i> Mixto</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <strong>₲ {{ number_format($recaudacion->monto, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($recaudacion->fecha_prevista_deposito)
                                        {{ \Carbon\Carbon::parse($recaudacion->fecha_prevista_deposito)->format('d/m/Y') }}
                                        @if($recaudacion->fecha_prevista_deposito < now()->toDateString() && $recaudacion->estado === 'PENDIENTE')
                                            <br><span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Vencido</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($recaudacion->fecha_real_deposito)
                                        {{ \Carbon\Carbon::parse($recaudacion->fecha_real_deposito)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($recaudacion->estado === 'PENDIENTE')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($recaudacion->estado === 'DEPOSITADO')
                                        <span class="badge badge-success">Depositado</span>
                                    @elseif($recaudacion->estado === 'RECHAZADO')
                                        <span class="badge badge-danger">Rechazado</span>
                                    @else
                                        <span class="badge badge-secondary">Cancelado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($recaudacion->comprobante_deposito)
                                        <small><strong>{{ $recaudacion->comprobante_deposito }}</strong></small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                    @if($recaudacion->usuarioDeposita)
                                        <br><small class="text-success">{{ $recaudacion->usuarioDeposita->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($recaudacion->estado === 'PENDIENTE')
                                        <button
                                            wire:click="abrirModalDeposito({{ $recaudacion->id }})"
                                            class="btn btn-sm btn-success"
                                            title="Marcar como depositado">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Depositar
                                        </button>
                                    @elseif($recaudacion->estado === 'DEPOSITADO')
                                        <span class="text-success"><i class="fas fa-check"></i></span>
                                    @endif
                                    @if($recaudacion->observaciones)
                                        <br><small class="text-muted" title="{{ $recaudacion->observaciones }}">
                                            <i class="fas fa-comment"></i>
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-info-circle mr-2 text-muted"></i>
                                    <span class="text-muted">No hay recaudaciones registradas</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $recaudaciones->links() }}
            </div>
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

    {{-- Modal para marcar como depositado --}}
    @if($showDepositoModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle mr-2"></i>
                            Marcar como Depositado
                        </h5>
                        <button type="button" class="close text-white" wire:click="cerrarModalDeposito">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form wire:submit.prevent="marcarComoDepositado">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="comprobante_deposito">
                                    Comprobante de Depósito <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="comprobante_deposito"
                                    id="comprobante_deposito"
                                    class="form-control @error('comprobante_deposito') is-invalid @enderror"
                                    placeholder="Número de comprobante">
                                @error('comprobante_deposito')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="fecha_real_deposito">
                                    Fecha Real de Depósito <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    wire:model="fecha_real_deposito"
                                    id="fecha_real_deposito"
                                    class="form-control @error('fecha_real_deposito') is-invalid @enderror">
                                @error('fecha_real_deposito')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="cerrarModalDeposito">
                                <i class="fas fa-times mr-1"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check mr-1"></i>
                                Confirmar Depósito
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
