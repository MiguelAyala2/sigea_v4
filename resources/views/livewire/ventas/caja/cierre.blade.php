<div>
    <div class="card card-danger card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lock mr-2"></i>
                Cierre de Caja
            </h3>
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
                    Este punto de expedición no tiene una apertura de caja activa para cerrar.
                    <a href="{{ route('ventas.caja.apertura') }}" class="btn btn-sm btn-warning mt-2">
                        <i class="fas fa-unlock mr-1"></i>
                        Abrir Caja
                    </a>
                </div>
            @endif

            @if($tiene_apertura_abierta)
                {{-- Información de la apertura --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Información de la Apertura
                                </h5>
                            </div>
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
                                        ₲ {{ number_format($saldo_inicial, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen de movimientos --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-info">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator mr-2"></i>
                                    Resumen de Movimientos
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box bg-white">
                                            <span class="info-box-icon bg-success"><i class="fas fa-plus"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Ingresos</span>
                                                <span class="info-box-number">₲ {{ number_format($total_ingresos, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-white">
                                            <span class="info-box-icon bg-danger"><i class="fas fa-minus"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Egresos</span>
                                                <span class="info-box-number">₲ {{ number_format($total_egresos, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-white">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-wallet"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Saldo Inicial</span>
                                                <span class="info-box-number">₲ {{ number_format($saldo_inicial, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-white">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-equals"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Saldo Calculado</span>
                                                <span class="info-box-number">₲ {{ number_format($saldo_calculado, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Totales por forma de pago --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-secondary">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Detalle por Forma de Pago
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6">
                                        <strong><i class="fas fa-money-bill-wave mr-2 text-success"></i>Efectivo:</strong><br>
                                        ₲ {{ number_format($total_efectivo, 0, ',', '.') }}
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <strong><i class="fas fa-money-check mr-2 text-info"></i>Cheques:</strong><br>
                                        ₲ {{ number_format($total_cheques, 0, ',', '.') }}
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <strong><i class="fas fa-credit-card mr-2 text-primary"></i>Tarjeta Débito:</strong><br>
                                        ₲ {{ number_format($total_tarjeta_debito, 0, ',', '.') }}
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <strong><i class="fas fa-credit-card mr-2 text-warning"></i>Tarjeta Crédito:</strong><br>
                                        ₲ {{ number_format($total_tarjeta_credito, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3 col-sm-6">
                                        <strong><i class="fas fa-exchange-alt mr-2 text-secondary"></i>Transferencias:</strong><br>
                                        ₲ {{ number_format($total_transferencias, 0, ',', '.') }}
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <strong><i class="fas fa-qrcode mr-2 text-dark"></i>QR:</strong><br>
                                        ₲ {{ number_format($total_qr, 0, ',', '.') }}
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <strong><i class="fas fa-ellipsis-h mr-2 text-muted"></i>Otros:</strong><br>
                                        ₲ {{ number_format($total_otros, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Arqueo de caja --}}
                @if($arqueo)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-check-circle"></i> Arqueo Realizado</h5>
                                Se ha realizado el arqueo de caja. Total arqueo: <strong>₲ {{ number_format($arqueo->total_arqueo, 0, ',', '.') }}</strong>
                                <a href="{{ route('ventas.caja.arqueo') }}" class="btn btn-sm btn-success ml-2">
                                    <i class="fas fa-eye mr-1"></i>Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Arqueo Pendiente</h5>
                                No se ha realizado el arqueo de caja. Se recomienda realizar el arqueo antes del cierre.
                                <a href="{{ route('ventas.caja.arqueo') }}" class="btn btn-sm btn-warning ml-2">
                                    <i class="fas fa-coins mr-1"></i>Realizar Arqueo
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Formulario de cierre --}}
                <form wire:submit.prevent="cerrarCaja">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="saldo_declarado">
                                    Saldo Declarado (Contado) <span class="text-muted">(Opcional)</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₲</span>
                                    </div>
                                    <input
                                        type="number"
                                        wire:model.live="saldo_declarado"
                                        id="saldo_declarado"
                                        class="form-control @error('saldo_declarado') is-invalid @enderror"
                                        placeholder="Dejar vacío si no hay diferencia"
                                        min="0"
                                        step="1">
                                    @error('saldo_declarado')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Ingrese el saldo físico contado solo si hay diferencia. Si se deja vacío, se asume que no hay diferencia.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Diferencia</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₲</span>
                                    </div>
                                    <input
                                        type="text"
                                        class="form-control {{ $diferencia > 0 ? 'bg-success' : ($diferencia < 0 ? 'bg-danger' : 'bg-light') }} text-white font-weight-bold"
                                        value="{{ number_format($diferencia, 0, ',', '.') }}"
                                        readonly>
                                </div>
                                <small class="form-text text-muted">
                                    @if($diferencia > 0)
                                        <span class="text-success"><i class="fas fa-arrow-up"></i> Sobrante</span>
                                    @elseif($diferencia < 0)
                                        <span class="text-danger"><i class="fas fa-arrow-down"></i> Faltante</span>
                                    @else
                                        <span class="text-muted"><i class="fas fa-check"></i> Sin diferencia</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea
                                    wire:model="observaciones"
                                    id="observaciones"
                                    class="form-control @error('observaciones') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Observaciones sobre el cierre de caja..."
                                    maxlength="1000"></textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Máximo 1000 caracteres
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-lock mr-2"></i>
                                Cerrar Caja
                            </button>
                            <a href="{{ route('ventas.caja.movimientos') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Volver a Movimientos
                            </a>
                        </div>
                    </div>
                </form>
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
</div>
