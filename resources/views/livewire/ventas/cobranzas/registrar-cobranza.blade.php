<div>
    {{-- Mensajes Flash --}}
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="¡Éxito!" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="light" title="Registrar Cobranza" icon="fas fa-dollar-sign">
        <form wire:submit.prevent="guardar">
            <div class="row">
                {{-- Columna Izquierda - Selección de Cuenta --}}
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">
                                <i class="fas fa-search mr-2"></i>
                                1. Seleccionar Cuenta por Cobrar
                            </h3>
                        </div>
                        <div class="card-body">
                            @if(!$cuenta_seleccionada)
                                <div class="form-group">
                                    <label>Buscar por N° Factura o Cliente</label>
                                    <input type="text"
                                           wire:model.live.debounce.300ms="search_cuenta"
                                           class="form-control"
                                           placeholder="Ingrese número de factura o nombre del cliente...">
                                </div>

                                @if($mostrar_busqueda && count($cuentas_encontradas) > 0)
                                    <div class="list-group mt-2">
                                        @foreach($cuentas_encontradas as $cuenta)
                                            <button type="button"
                                                    wire:click="seleccionarCuenta({{ $cuenta->id }})"
                                                    class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">
                                                        <i class="fas fa-file-invoice mr-2"></i>
                                                        {{ $cuenta->numero_factura }}
                                                    </h5>
                                                    <span class="badge badge-warning badge-lg">
                                                        ₲ {{ number_format($cuenta->saldo_pendiente, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                <p class="mb-1">
                                                    <i class="fas fa-user mr-2"></i>
                                                    <strong>Cliente:</strong> {{ $cuenta->cliente->nombre ?? 'N/A' }}
                                                </p>
                                                <small>
                                                    <i class="fas fa-calendar mr-2"></i>
                                                    Vencimiento: {{ $cuenta->fecha_vencimiento->format('d/m/Y') }}
                                                    <span class="ml-3">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        Estado: {{ $cuenta->estado }}
                                                    </span>
                                                </small>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif($mostrar_busqueda && count($cuentas_encontradas) === 0)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        No se encontraron cuentas pendientes con ese criterio de búsqueda.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-success">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5><i class="fas fa-file-invoice mr-2"></i>{{ $factura_numero }}</h5>
                                            <p class="mb-0">
                                                <i class="fas fa-user mr-2"></i>
                                                <strong>Cliente:</strong> {{ $cliente_nombre }}
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <h4 class="text-danger">
                                                ₲ {{ number_format($saldo_pendiente, 0, ',', '.') }}
                                            </h4>
                                            <small>Saldo Pendiente</small>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="limpiarSeleccion" class="btn btn-sm btn-warning mt-2">
                                        <i class="fas fa-times mr-1"></i> Cambiar Cuenta
                                    </button>
                                </div>

                                {{-- Formulario de Cobranza --}}
                                <div class="card mt-3">
                                    <div class="card-header bg-success">
                                        <h3 class="card-title">
                                            <i class="fas fa-money-bill-wave mr-2"></i>
                                            2. Datos de la Cobranza
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <x-adminlte-input name="fecha_cobranza" label="Fecha Cobranza *"
                                                    type="date" wire:model="fecha_cobranza"/>
                                                @error('fecha_cobranza')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <x-adminlte-select name="forma_pago" label="Forma de Pago *"
                                                    wire:model="forma_pago">
                                                    <option value="">Seleccionar...</option>
                                                    @foreach(\App\Models\Ventas\MovimientoCaja::FORMAS_PAGO as $key => $valor)
                                                        <option value="{{ $key }}">{{ $valor }}</option>
                                                    @endforeach
                                                </x-adminlte-select>
                                                @error('forma_pago')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <x-adminlte-input name="monto_cobrado" label="Monto Cobrado *"
                                                    type="number" step="0.01" wire:model="monto_cobrado"
                                                    placeholder="0.00">
                                                    <x-slot name="prependSlot">
                                                        <div class="input-group-text bg-success">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </div>
                                                    </x-slot>
                                                </x-adminlte-input>
                                                @error('monto_cobrado')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <x-adminlte-input name="comprobante_numero" label="N° Comprobante"
                                                    wire:model="comprobante_numero"
                                                    placeholder="Número de comprobante..."/>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <x-adminlte-input name="referencia" label="Referencia"
                                                    wire:model="referencia"
                                                    placeholder="Referencia, N° cheque, N° transferencia..."/>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <x-adminlte-textarea name="observaciones" label="Observaciones"
                                                    wire:model="observaciones" rows="2"
                                                    placeholder="Observaciones adicionales..."/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Columna Derecha - Información y Acciones --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>
                                Información
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($cuenta_seleccionada)
                                <dl class="row">
                                    <dt class="col-sm-6">Saldo Pendiente:</dt>
                                    <dd class="col-sm-6 text-danger">
                                        <strong>₲ {{ number_format($saldo_pendiente, 0, ',', '.') }}</strong>
                                    </dd>

                                    <dt class="col-sm-6">Monto a Cobrar:</dt>
                                    <dd class="col-sm-6 text-success">
                                        <strong>₲ {{ number_format($monto_cobrado ?: 0, 0, ',', '.') }}</strong>
                                    </dd>

                                    <dt class="col-sm-6">Saldo Restante:</dt>
                                    <dd class="col-sm-6 text-primary">
                                        <strong>₲ {{ number_format(max(0, $saldo_pendiente - ($monto_cobrado ?: 0)), 0, ',', '.') }}</strong>
                                    </dd>
                                </dl>

                                <hr>

                                <button type="submit" class="btn btn-success btn-block btn-lg">
                                    <i class="fas fa-save mr-2"></i> Registrar Cobranza
                                </button>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Instrucciones:</strong>
                                    <ol class="mb-0 pl-3 mt-2">
                                        <li>Busque la cuenta por cobrar</li>
                                        <li>Seleccione de la lista</li>
                                        <li>Complete los datos de cobranza</li>
                                        <li>Guarde el registro</li>
                                    </ol>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Ayuda --}}
                    <div class="card">
                        <div class="card-header bg-secondary">
                            <h3 class="card-title">
                                <i class="fas fa-question-circle mr-2"></i>
                                Ayuda
                            </h3>
                        </div>
                        <div class="card-body">
                            <small>
                                <ul class="pl-3">
                                    <li>Solo se muestran cuentas con saldo pendiente</li>
                                    <li>El monto no puede exceder el saldo</li>
                                    <li>Debe tener una caja abierta para registrar cobranzas</li>
                                    <li>La cobranza se registra en el movimiento de caja</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
