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

    <x-adminlte-card theme="light" title="Mis Aprobaciones Pendientes" icon="fas fa-tasks">
        {{-- Estadísticas --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['total_pendientes'] }}</h3>
                        <p>Total Pendientes</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['vencidas'] }}</h3>
                        <p>Vencidas</p>
                    </div>
                    <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['hoy'] }}</h3>
                        <p>Vencen Hoy</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-day"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $stats['proximas'] }}</h3>
                        <p>Próximas (3 días)</p>
                    </div>
                    <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <x-adminlte-input name="buscador" wire:model.live.debounce.300ms="buscador"
                    placeholder="Buscar por rol, comentarios..."
                    igroup-size="sm" fgroup-class="mb-0">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 mb-2">
                <x-adminlte-select name="tipo_documento" wire:model.live="tipo_documento"
                    igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los tipos</option>
                    @foreach(App\Models\Compras\AprobacionFlujo::TIPOS_DOCUMENTO as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
                </x-adminlte-select>
            </div>

            <div class="col-md-2 mb-2">
                <button wire:click="limpiarFiltros" class="btn btn-secondary btn-sm btn-block">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>
        </div>

        {{-- Tabla --}}
        @if($aprobaciones->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Tipo</th>
                            <th>Nivel</th>
                            <th>Asignado</th>
                            <th>Vence</th>
                            <th>Comentarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aprobaciones as $aprobacion)
                            <tr class="{{ $aprobacion->esta_vencida ? 'table-danger' : '' }}">
                                <td>
                                    <span class="badge badge-info">{{ $aprobacion->documento_tipo_texto }}</span>
                                </td>
                                <td>{{ $aprobacion->nivel_aprobacion_texto }}</td>
                                <td>
                                    {{ $aprobacion->fecha_asignacion->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $aprobacion->fecha_asignacion->format('H:i') }}</small>
                                </td>
                                <td>
                                    {{ $aprobacion->fecha_vencimiento_formateada }}
                                    @if($aprobacion->tiempo_restante)
                                        <br><small class="{{ $aprobacion->esta_vencida ? 'text-danger' : 'text-muted' }}">
                                            {{ $aprobacion->tiempo_restante }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $aprobacion->comentarios ?? 'Sin comentarios' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="abrirModalAprobar({{ $aprobacion->id }})"
                                                class="btn btn-success" title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:click="abrirModalRechazar({{ $aprobacion->id }})"
                                                class="btn btn-danger" title="Rechazar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $aprobaciones->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5 class="text-success">¡No tienes aprobaciones pendientes!</h5>
                <p class="text-muted">Todas tus aprobaciones están al día.</p>
            </div>
        @endif
    </x-adminlte-card>

    {{-- Modal Aprobar --}}
    @if($mostrar_modal_aprobar && $aprobacion_seleccionada)
        <x-adminlte-modal id="modalAprobar" title="Aprobar Documento" theme="success"
                          size="lg" v-centered static-backdrop scrollable wire:model="mostrar_modal_aprobar">
            <div class="mb-3">
                <strong>Tipo:</strong> {{ $aprobacion_seleccionada->documento_tipo_texto }}<br>
                <strong>Nivel:</strong> {{ $aprobacion_seleccionada->nivel_aprobacion_texto }}<br>
                <strong>Rol requerido:</strong> {{ $aprobacion_seleccionada->rol_requerido }}
            </div>

            <x-adminlte-textarea name="comentarios_aprobacion" wire:model="comentarios_aprobacion"
                label="Comentarios (opcional)" rows="3" placeholder="Ingrese comentarios adicionales..."/>

            <x-slot name="footerSlot">
                <button wire:click="cerrarModalAprobar" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button wire:click="aprobar" class="btn btn-success">
                    <i class="fas fa-check"></i> Aprobar
                </button>
            </x-slot>
        </x-adminlte-modal>
    @endif

    {{-- Modal Rechazar --}}
    @if($mostrar_modal_rechazar && $aprobacion_seleccionada)
        <x-adminlte-modal id="modalRechazar" title="Rechazar Documento" theme="danger"
                          size="lg" v-centered static-backdrop scrollable wire:model="mostrar_modal_rechazar">
            <div class="mb-3">
                <strong>Tipo:</strong> {{ $aprobacion_seleccionada->documento_tipo_texto }}<br>
                <strong>Nivel:</strong> {{ $aprobacion_seleccionada->nivel_aprobacion_texto }}<br>
                <strong>Rol requerido:</strong> {{ $aprobacion_seleccionada->rol_requerido }}
            </div>

            <x-adminlte-textarea name="observaciones_rechazo" wire:model="observaciones_rechazo"
                label="Motivo del rechazo (*)" rows="3" placeholder="Indique el motivo del rechazo..."/>
            @error('observaciones_rechazo')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <x-slot name="footerSlot">
                <button wire:click="cerrarModalRechazar" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button wire:click="rechazar" class="btn btn-danger">
                    <i class="fas fa-ban"></i> Rechazar
                </button>
            </x-slot>
        </x-adminlte-modal>
    @endif
</div>
