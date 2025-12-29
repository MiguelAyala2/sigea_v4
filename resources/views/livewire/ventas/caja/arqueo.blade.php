<div>
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-coins mr-2"></i>
                Arqueo de Caja
            </h3>
            <div class="card-tools">
                @if($tiene_apertura_abierta)
                    <button wire:click="limpiarFormulario" class="btn btn-sm btn-secondary">
                        <i class="fas fa-eraser mr-1"></i>
                        Limpiar
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
                    Este punto de expedición no tiene una apertura de caja activa para realizar el arqueo.
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
                                        <strong><i class="fas fa-wallet mr-2"></i>Saldo calculado:</strong><br>
                                        ₲ {{ number_format($apertura_actual->saldo_actual, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($arqueo_existente)
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Arqueo Existente</h5>
                        Ya existe un arqueo registrado para esta apertura. Puede modificarlo si es necesario.
                    </div>
                @endif

                <form wire:submit.prevent="guardarArqueo">
                    {{-- Billetes --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        Billetes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label>₲ 100.000</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_billetes_100000"
                                                    class="form-control text-center @error('cantidad_billetes_100000') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_billetes_100000 * 100000, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label>₲ 50.000</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_billetes_50000"
                                                    class="form-control text-center @error('cantidad_billetes_50000') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_billetes_50000 * 50000, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label>₲ 20.000</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_billetes_20000"
                                                    class="form-control text-center @error('cantidad_billetes_20000') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_billetes_20000 * 20000, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label>₲ 10.000</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_billetes_10000"
                                                    class="form-control text-center @error('cantidad_billetes_10000') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_billetes_10000 * 10000, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label>₲ 5.000</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_billetes_5000"
                                                    class="form-control text-center @error('cantidad_billetes_5000') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_billetes_5000 * 5000, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label>₲ 2.000</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_billetes_2000"
                                                    class="form-control text-center @error('cantidad_billetes_2000') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_billetes_2000 * 2000, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-primary mb-0">
                                                <strong>Subtotal Billetes:</strong>
                                                <span class="float-right font-weight-bold">
                                                    ₲ {{ number_format($subtotal_billetes, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Monedas --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-coins mr-2"></i>
                                        Monedas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label>₲ 1.000</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_monedas_1000"
                                                    class="form-control text-center @error('cantidad_monedas_1000') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_monedas_1000 * 1000, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label>₲ 500</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_monedas_500"
                                                    class="form-control text-center @error('cantidad_monedas_500') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_monedas_500 * 500, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label>₲ 100</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_monedas_100"
                                                    class="form-control text-center @error('cantidad_monedas_100') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_monedas_100 * 100, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label>₲ 50</label>
                                                <input
                                                    type="number"
                                                    wire:model.live="cantidad_monedas_50"
                                                    class="form-control text-center @error('cantidad_monedas_50') is-invalid @enderror"
                                                    min="0"
                                                    step="1"
                                                    placeholder="0">
                                                <small class="form-text text-muted text-center">
                                                    ₲ {{ number_format($cantidad_monedas_50 * 50, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-success mb-0">
                                                <strong>Subtotal Monedas:</strong>
                                                <span class="float-right font-weight-bold">
                                                    ₲ {{ number_format($subtotal_monedas, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card bg-gradient-warning">
                                <div class="card-body">
                                    <h3 class="mb-0">
                                        <strong>TOTAL ARQUEO:</strong>
                                        <span class="float-right">
                                            ₲ {{ number_format($total_arqueo, 0, ',', '.') }}
                                        </span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea
                                    wire:model="observaciones"
                                    id="observaciones"
                                    class="form-control @error('observaciones') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Observaciones sobre el arqueo de caja..."
                                    maxlength="500"></textarea>
                                @error('observaciones')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save mr-2"></i>
                                {{ $arqueo_existente ? 'Actualizar Arqueo' : 'Guardar Arqueo' }}
                            </button>
                            <a href="{{ route('ventas.caja.cierre') }}" class="btn btn-danger btn-lg">
                                <i class="fas fa-lock mr-2"></i>
                                Ir a Cierre de Caja
                            </a>
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
