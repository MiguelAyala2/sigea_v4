<div>
    {{-- Alertas --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-success">
            <h3 class="card-title">
                <i class="fas fa-money-bill-wave"></i>
                Registrar Nuevo Pago
            </h3>
        </div>
        <div class="card-body">
            {{-- Tipo de Pago --}}
            <div class="form-group">
                <label>Tipo de Pago <span class="text-danger">*</span></label>
                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                    <label class="btn btn-outline-primary {{ $tipo_pago === 'CUOTA' ? 'active' : '' }}">
                        <input type="radio" wire:model.live="tipo_pago" value="CUOTA"> Pago por Cuota(s)
                    </label>
                    <label class="btn btn-outline-success {{ $tipo_pago === 'TOTAL' ? 'active' : '' }}">
                        <input type="radio" wire:model.live="tipo_pago" value="TOTAL"> Pago Total
                    </label>
                </div>
            </div>

            {{-- Selección de Cuotas --}}
            @if($tipo_pago === 'CUOTA' && $cuotas->isNotEmpty())
                <div class="form-group">
                    <label>Seleccione las Cuotas a Pagar <span class="text-danger">*</span></label>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" class="text-center">
                                        <i class="fas fa-check-square"></i>
                                    </th>
                                    <th>Cuota N°</th>
                                    <th>Monto Cuota</th>
                                    <th>Pagado</th>
                                    <th>Saldo Pendiente</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cuotas as $cuota)
                                    <tr class="cursor-pointer {{ in_array($cuota->id, $cuotas_seleccionadas) ? 'table-success' : '' }}"
                                        wire:click="toggleCuota({{ $cuota->id }})"
                                        style="cursor: pointer;">
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   {{ in_array($cuota->id, $cuotas_seleccionadas) ? 'checked' : '' }}
                                                   onclick="event.stopPropagation();">
                                        </td>
                                        <td><strong>Cuota {{ $cuota->numero_cuota }}</strong></td>
                                        <td>₲ {{ number_format($cuota->monto, 0, ',', '.') }}</td>
                                        <td class="text-success">₲ {{ number_format($cuota->monto_pagado, 0, ',', '.') }}</td>
                                        <td class="text-danger"><strong>₲ {{ number_format($cuota->saldo_pendiente, 0, ',', '.') }}</strong></td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}
                                            @php
                                                $diasVencimiento = now()->diffInDays($cuota->fecha_vencimiento, false);
                                            @endphp
                                            @if($diasVencimiento < 0)
                                                <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Vencida</small>
                                            @elseif($diasVencimiento <= 7)
                                                <br><small class="text-warning"><i class="fas fa-clock"></i> Por vencer</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cuota->estado === 'PENDIENTE')
                                                <span class="badge badge-warning">Pendiente</span>
                                            @elseif($cuota->estado === 'PARCIALMENTE_PAGADA')
                                                <span class="badge badge-info">Parcial</span>
                                            @elseif($cuota->estado === 'VENCIDA')
                                                <span class="badge badge-danger">Vencida</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($cuotas_seleccionadas) === 0)
                        <small class="text-muted">Haga clic en una cuota para seleccionarla</small>
                    @endif
                </div>
            @endif

            @if($tipo_pago === 'CUOTA' && $cuotas->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Esta factura no tiene cuotas definidas. Use "Pago Total" para registrar el pago completo.
                </div>
            @endif

            {{-- Resumen del Monto a Pagar --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-{{ $total_a_pagar > 0 ? 'primary' : 'secondary' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-calculator"></i>
                            Total a Pagar:
                            <strong class="float-right">₲ {{ number_format($total_a_pagar, 0, ',', '.') }}</strong>
                        </h5>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Formas de Pago --}}
            <h5 class="mt-3 mb-3">
                <i class="fas fa-wallet"></i> Formas de Pago
                <span class="text-danger">*</span>
            </h5>

            {{-- Agregar Forma de Pago --}}
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label>Forma de Pago</label>
                                <select wire:model="forma_pago_temp.forma_pago" class="form-control form-control-sm">
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="CHEQUE">Cheque</option>
                                    <option value="TARJETA_DEBITO">Tarjeta de Débito</option>
                                    <option value="TARJETA_CREDITO">Tarjeta de Crédito</option>
                                    <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                                    <option value="QR">QR / Billetera Digital</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label>Monto</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₲</span>
                                    </div>
                                    <input type="number"
                                           wire:model="forma_pago_temp.monto"
                                           class="form-control"
                                           min="0"
                                           step="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label>Referencia</label>
                                <input type="text"
                                       wire:model="forma_pago_temp.referencia"
                                       class="form-control form-control-sm"
                                       placeholder="Nro. cheque, voucher, etc.">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button"
                                    wire:click="agregarFormaPago"
                                    class="btn btn-success btn-sm btn-block">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Formas de Pago Agregadas --}}
            @if(count($formas_pago) > 0)
                <div class="table-responsive mt-3">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-secondary">
                            <tr>
                                <th>Forma de Pago</th>
                                <th class="text-right">Monto</th>
                                <th>Referencia</th>
                                <th width="80" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formas_pago as $index => $fp)
                                <tr>
                                    <td>
                                        @switch($fp['forma_pago'])
                                            @case('EFECTIVO')
                                                <i class="fas fa-money-bill-wave text-success"></i> Efectivo
                                                @break
                                            @case('CHEQUE')
                                                <i class="fas fa-money-check text-info"></i> Cheque
                                                @break
                                            @case('TARJETA_DEBITO')
                                                <i class="fas fa-credit-card text-primary"></i> Tarjeta de Débito
                                                @break
                                            @case('TARJETA_CREDITO')
                                                <i class="fas fa-credit-card text-warning"></i> Tarjeta de Crédito
                                                @break
                                            @case('TRANSFERENCIA')
                                                <i class="fas fa-exchange-alt text-info"></i> Transferencia
                                                @break
                                            @case('QR')
                                                <i class="fas fa-qrcode text-success"></i> QR / Billetera Digital
                                                @break
                                            @default
                                                <i class="fas fa-dollar-sign"></i> {{ $fp['forma_pago'] }}
                                        @endswitch
                                    </td>
                                    <td class="text-right"><strong>₲ {{ number_format($fp['monto'], 0, ',', '.') }}</strong></td>
                                    <td>{{ $fp['referencia'] ?? '-' }}</td>
                                    <td class="text-center">
                                        <button type="button"
                                                wire:click="eliminarFormaPago({{ $index }})"
                                                class="btn btn-danger btn-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td class="text-right"><strong>TOTAL:</strong></td>
                                <td class="text-right">
                                    <strong class="{{ abs($total_pagado - $total_a_pagar) < 0.01 ? 'text-success' : 'text-danger' }}">
                                        ₲ {{ number_format($total_pagado, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            @if(abs($saldo_restante) >= 0.01)
                                <tr>
                                    <td class="text-right"><strong>{{ $saldo_restante < 0 ? 'EXCEDENTE:' : 'FALTA:' }}</strong></td>
                                    <td class="text-right">
                                        <strong class="text-danger">
                                            ₲ {{ number_format(abs($saldo_restante), 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> Debe agregar al menos una forma de pago.
                </div>
            @endif

            {{-- Observaciones --}}
            <div class="form-group mt-3">
                <label for="observaciones">Observaciones</label>
                <textarea wire:model="observaciones"
                          class="form-control"
                          id="observaciones"
                          rows="2"
                          placeholder="Comentarios adicionales sobre el pago..."></textarea>
            </div>

            <hr>

            {{-- Botones de Acción --}}
            <div class="form-group mb-0">
                <button type="button"
                        wire:click="registrarPago"
                        class="btn btn-success"
                        {{ (abs($total_pagado - $total_a_pagar) >= 0.01 || count($formas_pago) === 0) ? 'disabled' : '' }}>
                    <i class="fas fa-save"></i> Registrar Pago
                </button>
                <a href="{{ route('ventas.cuentas-cobrar.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>

                @if($total_a_pagar > 0 && abs($total_pagado - $total_a_pagar) >= 0.01)
                    <span class="text-muted ml-3">
                        <i class="fas fa-info-circle"></i>
                        Complete las formas de pago para habilitar el botón de registro
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
