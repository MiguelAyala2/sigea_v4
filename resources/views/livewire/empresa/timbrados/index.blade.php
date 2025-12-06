<div>
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

    <x-adminlte-card theme="light" title="Timbrados" icon="fas fa-file-invoice">
        <div class="row mb-3">
            <div class="col-md-4">
                <x-adminlte-input name="buscador" wire:model.live="buscador" placeholder="Buscar número de timbrado..."
                    fgroup-class="mb-0" igroup-size="sm">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-3">
                <x-adminlte-select name="buscarTipoDocumento" wire:model.live="buscarTipoDocumento" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos los tipos</option>
                    @foreach(\App\Models\Empresa\Timbrado::TIPOS_DOCUMENTO as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-2">
                <x-adminlte-select name="buscarActivo" wire:model.live="buscarActivo" igroup-size="sm" fgroup-class="mb-0">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-filter"></i>
                        </div>
                    </x-slot>
                </x-adminlte-select>
            </div>
            <div class="col-md-3">
                <a href="{{ route('empresa.timbrados.create') }}" class="btn btn-success btn-block btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Timbrado
                </a>
            </div>
        </div>

        @if($timbrados->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>N° Timbrado</th>
                            <th>Tipo Documento</th>
                            <th>Rango Numeración</th>
                            <th>Vigencia</th>
                            <th>Uso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timbrados as $timbrado)
                            <tr>
                                <td>
                                    <strong>{{ $timbrado->numero_timbrado }}</strong>
                                    @if($timbrado->es_electronico)
                                        <br><small class="badge badge-info">
                                            <i class="fas fa-laptop"></i> {{ $timbrado->ambiente_texto }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($timbrado->tipo_documento === 'factura')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-file-invoice"></i> {{ $timbrado->tipo_documento_texto }}
                                        </span>
                                    @elseif($timbrado->tipo_documento === 'nota_credito')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-file-invoice-dollar"></i> {{ $timbrado->tipo_documento_texto }}
                                        </span>
                                    @elseif($timbrado->tipo_documento === 'nota_debito')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-file-invoice-dollar"></i> {{ $timbrado->tipo_documento_texto }}
                                        </span>
                                    @elseif($timbrado->tipo_documento === 'remision')
                                        <span class="badge badge-info">
                                            <i class="fas fa-truck"></i> {{ $timbrado->tipo_documento_texto }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-receipt"></i> {{ $timbrado->tipo_documento_texto }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $timbrado->rango_numeracion }}</small>
                                    <br><small class="text-info">Actual: {{ number_format($timbrado->numero_actual, 0, '', '.') }}</small>
                                </td>
                                <td>
                                    <small>
                                        <strong>Inicio:</strong> {{ $timbrado->fecha_inicio_vigencia->format('d/m/Y') }}<br>
                                        <strong>Fin:</strong> {{ $timbrado->fecha_fin_vigencia->format('d/m/Y') }}
                                    </small>
                                    @if($timbrado->esta_vigente)
                                        @if($timbrado->proximo_a_vencer)
                                            <br><span class="badge badge-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Por vencer
                                            </span>
                                        @endif
                                    @else
                                        <br><span class="badge badge-secondary">
                                            <i class="fas fa-calendar-times"></i> No vigente
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar
                                            @if($timbrado->porcentaje_uso >= 90) bg-danger
                                            @elseif($timbrado->porcentaje_uso >= 70) bg-warning
                                            @else bg-success
                                            @endif"
                                            role="progressbar"
                                            style="width: {{ $timbrado->porcentaje_uso }}%"
                                            aria-valuenow="{{ $timbrado->porcentaje_uso }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                            {{ $timbrado->porcentaje_uso }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">Disponibles: {{ number_format($timbrado->numeros_disponibles, 0, '', '.') }}</small>
                                    @if($timbrado->esta_agotado)
                                        <br><span class="badge badge-danger">
                                            <i class="fas fa-ban"></i> Agotado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($timbrado->activo)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('empresa.timbrados.edit', $timbrado->id) }}"
                                           class="btn btn-warning btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($timbrado->activo)
                                            <button wire:click="inactivar({{ $timbrado->id }})"
                                                    class="btn btn-secondary btn-xs"
                                                    onclick="return confirm('¿Inactivar timbrado?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button wire:click="activar({{ $timbrado->id }})"
                                                    class="btn btn-success btn-xs">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button wire:click="eliminar({{ $timbrado->id }})"
                                                class="btn btn-danger btn-xs"
                                                onclick="return confirm('¿Eliminar timbrado? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <div>
                    Mostrando {{ $timbrados->firstItem() }} a {{ $timbrados->lastItem() }} de {{ $timbrados->total() }} registros
                </div>
                <div>
                    {{ $timbrados->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay timbrados registrados</h5>
                <p class="text-muted">Crea tu primer timbrado para comenzar a facturar</p>
                <a href="{{ route('empresa.timbrados.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Primer Timbrado
                </a>
            </div>
        @endif
    </x-adminlte-card>
</div>
