<div>
    {{-- Formulario --}}
    <x-adminlte-card theme="light" title="Añadir Rol" icon="fas fa-plus-circle" header-class="text-muted text-sm">
        <form class="row col-md-12 p-2" wire:submit="guardar">

            {{-- Nombre --}}
            <x-adminlte-input name="name" wire:model.blur="name" oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: ADMIN" label-class="text-lightblue" fgroup-class="col-md-12" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">Nombre *</div>
                </x-slot>
            </x-adminlte-input>

            {{-- Permisos - Interfaz con Checkboxes --}}
            <div class="col-md-12">
                <label class="text-lightblue">Permisos *</label>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th style="width: 200px;">Módulo / Entidad</th>
                                        <th class="text-center bg-warning" style="width: 80px;" title="Acceso completo a todo">TOTAL *</th>
                                        <th class="text-center" style="width: 80px;">Ver</th>
                                        <th class="text-center" style="width: 80px;">Crear</th>
                                        <th class="text-center" style="width: 80px;">Editar</th>
                                        <th class="text-center" style="width: 80px;">Eliminar</th>
                                        <th class="text-center" style="width: 80px;">Aprobar</th>
                                        <th class="text-center" style="width: 80px;">Rechazar</th>
                                        <th class="text-center" style="width: 80px;">Anular</th>
                                        <th class="text-center" style="width: 80px;">Imprimir</th>
                                        <th class="text-center" style="width: 80px;">Exportar</th>
                                        <th class="text-center" style="width: 100px;">Otros</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permisosAgrupados as $modulo => $entidades)
                                        {{-- Fila del módulo --}}
                                        <tr class="bg-light font-weight-bold">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="checkbox"
                                                           wire:click="toggleModulo('{{ $modulo }}')"
                                                           class="mr-2">
                                                    <span>{{ $modulo }}</span>
                                                </div>
                                            </td>

                                            {{-- TOTAL - Wildcard de módulo completo --}}
                                            <td class="text-center bg-warning-light">
                                                @php
                                                    $wildcardModulo = strtolower($modulo) . '.*';
                                                @endphp
                                                @if(in_array($wildcardModulo, collect($entidades['_wildcards'] ?? [])->toArray()))
                                                    <input type="checkbox"
                                                           wire:model.live="permisos"
                                                           value="{{ $wildcardModulo }}"
                                                           title="Acceso completo a {{ $modulo }}">
                                                @endif
                                            </td>

                                            <td colspan="10"></td>
                                        </tr>

                                        {{-- Filas de entidades --}}
                                        @foreach($entidades as $entidad => $acciones)
                                            @if($entidad !== '_wildcards')
                                                <tr>
                                                    <td class="pl-4">
                                                        <div class="d-flex align-items-center">
                                                            <input type="checkbox"
                                                                   wire:click="toggleEntidad('{{ $modulo }}', '{{ $entidad }}')"
                                                                   class="mr-2">
                                                            <span>{{ $entidad }}</span>
                                                        </div>
                                                    </td>

                                                    {{-- TOTAL (Wildcard) --}}
                                                    <td class="text-center bg-warning-light">
                                                        @php
                                                            $wildcardEntidad = strtolower(str_replace(' ', '_', $modulo)) . '.' . strtolower(str_replace(' ', '_', $entidad)) . '.*';
                                                        @endphp
                                                        @if(in_array($wildcardEntidad, collect($this->permisosAgrupados[$modulo]['_wildcards'] ?? [])->toArray()))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $wildcardEntidad }}"
                                                                   title="Acceso completo a {{ $entidad }}">
                                                        @endif
                                                    </td>

                                                    {{-- Ver --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['ver']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['ver'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Crear --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['crear']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['crear'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Editar --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['editar']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['editar'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Eliminar --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['eliminar']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['eliminar'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Aprobar --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['aprobar']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['aprobar'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Rechazar --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['rechazar']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['rechazar'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Anular --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['anular']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['anular'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Imprimir --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['imprimir']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['imprimir'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Exportar --}}
                                                    <td class="text-center">
                                                        @if(isset($acciones['exportar']))
                                                            <input type="checkbox"
                                                                   wire:model.live="permisos"
                                                                   value="{{ $acciones['exportar'] }}">
                                                        @endif
                                                    </td>

                                                    {{-- Otros permisos --}}
                                                    <td class="text-center">
                                                        @foreach($acciones as $accion => $nombrePermiso)
                                                            @if(!in_array($accion, ['ver', 'crear', 'editar', 'eliminar', 'aprobar', 'rechazar', 'anular', 'imprimir', 'exportar']) && $accion !== '*')
                                                                <div class="custom-control custom-checkbox d-inline-block mr-1" title="{{ $accion }}">
                                                                    <input type="checkbox"
                                                                           class="custom-control-input"
                                                                           id="{{ $nombrePermiso }}"
                                                                           wire:model.live="permisos"
                                                                           value="{{ $nombrePermiso }}">
                                                                    <label class="custom-control-label small" for="{{ $nombrePermiso }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $accion)) }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @error('permisos')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Botón de Volver --}}
            <div class="form-group col-md-3 d-flex align-items-end">
                <a href="{{ route('admin.roles.index') }}"
                    class="btn btn-block btn-outline-secondary text-decoration-none btn-sm"><i
                        class="fas fa-arrow-left mr-1"></i>Volver</a>
            </div>
            {{-- Botón de Guardar --}}
            <div class="form-group col-md-3 d-flex align-items-end">
                <x-adminlte-button type="submit" label="Guardar" theme="outline-success" icon="fas fa-lg fa-save"
                    class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>

    <style>
        /* Sticky header */
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Hover effects para las filas */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Estilo para las filas de módulo */
        .table tbody tr.bg-light {
            background-color: #e9ecef !important;
            border-top: 2px solid #dee2e6;
            border-bottom: 2px solid #dee2e6;
        }

        /* Checkboxes más grandes y visibles */
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Mejorar la visualización de los checkboxes en la columna "Otros" */
        .custom-control-input {
            width: 16px;
            height: 16px;
        }

        .custom-control-label {
            cursor: pointer;
            font-size: 0.85rem;
        }

        /* Padding consistente */
        .table td, .table th {
            vertical-align: middle;
            padding: 0.5rem;
        }

        /* Efecto hover en checkboxes */
        input[type="checkbox"]:hover {
            transform: scale(1.1);
            transition: transform 0.1s ease-in-out;
        }

        /* Texto de módulos más prominente */
        .font-weight-bold {
            font-weight: 600 !important;
            font-size: 0.95rem;
        }

        /* Indentación visual para entidades */
        .pl-4 {
            padding-left: 2rem !important;
        }

        /* Colores para mejor legibilidad */
        .text-muted {
            color: #6c757d !important;
            font-size: 0.85rem;
        }

        /* Columna TOTAL con fondo amarillo claro */
        .bg-warning-light {
            background-color: #fff3cd !important;
        }

        /* Header TOTAL con fondo amarillo */
        .bg-warning {
            background-color: #ffc107 !important;
            font-weight: 600 !important;
        }
    </style>
</div>
