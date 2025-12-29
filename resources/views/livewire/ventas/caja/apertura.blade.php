<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cash-register mr-2"></i>
                Apertura de Caja
            </h3>
        </div>

        <div class="card-body">
            @if($tiene_apertura_abierta)
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-info"></i> Caja Ya Abierta</h5>
                    Este punto de expedición ya tiene una apertura de caja activa.
                    <div class="mt-2">
                        <strong>Fecha apertura:</strong> {{ $apertura_actual->fecha_apertura->format('d/m/Y') }}<br>
                        <strong>Hora apertura:</strong> {{ $apertura_actual->hora_apertura }}<br>
                        <strong>Saldo inicial:</strong> ₲ {{ number_format($apertura_actual->saldo_inicial, 0, ',', '.') }}<br>
                        <strong>Usuario:</strong> {{ $apertura_actual->usuario->name }}
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="abrirCaja">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="punto_expedicion_id">
                                Punto de Expedición (Caja) <span class="text-danger">*</span>
                            </label>
                            <select
                                wire:model.live="punto_expedicion_id"
                                id="punto_expedicion_id"
                                class="form-control @error('punto_expedicion_id') is-invalid @enderror"
                                @if($tiene_apertura_abierta) disabled @endif>
                                <option value="">Seleccione un punto de expedición...</option>
                                @foreach($puntos_expedicion as $punto)
                                    <option value="{{ $punto->id }}">
                                        {{ $punto->codigo }} - {{ $punto->nombre }} ({{ $punto->sucursal->nombre }})
                                    </option>
                                @endforeach
                            </select>
                            @error('punto_expedicion_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="saldo_inicial">
                                Saldo Inicial <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₲</span>
                                </div>
                                <input
                                    type="number"
                                    wire:model="saldo_inicial"
                                    id="saldo_inicial"
                                    class="form-control @error('saldo_inicial') is-invalid @enderror"
                                    placeholder="0"
                                    min="0"
                                    step="1"
                                    @if($tiene_apertura_abierta) disabled @endif>
                                @error('saldo_inicial')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Ingrese el monto inicial con el que abre la caja
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
                                placeholder="Observaciones o notas sobre esta apertura de caja..."
                                maxlength="500"
                                @if($tiene_apertura_abierta) disabled @endif></textarea>
                            @error('observaciones')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Máximo 500 caracteres
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-info-circle text-info mr-2"></i>
                                    Información de la Apertura
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong><i class="fas fa-user mr-2"></i>Usuario:</strong><br>
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong><i class="fas fa-calendar mr-2"></i>Fecha:</strong><br>
                                        {{ now()->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong><i class="fas fa-clock mr-2"></i>Hora:</strong><br>
                                        {{ now()->format('H:i:s') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            @if($tiene_apertura_abierta) disabled @endif>
                            <i class="fas fa-unlock mr-2"></i>
                            Abrir Caja
                        </button>
                        @if($tiene_apertura_abierta)
                            <a href="{{ route('ventas.caja.movimientos') }}" class="btn btn-success">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Ir a Movimientos
                            </a>
                        @endif
                        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
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
