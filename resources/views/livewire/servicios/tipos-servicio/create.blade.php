<div>
    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="primary" title="Datos del Tipo de Servicio" icon="fas fa-list-ul" collapsible>
        <form wire:submit.prevent="guardar">
            <div class="row">
                <div class="col-md-3">
                    <x-adminlte-input name="codigo" label="Código *" wire:model="codigo"
                        placeholder="Autogenerado" disabled enable-old-support error-key="codigo">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-secondary">
                                <i class="fas fa-barcode"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-6">
                    <x-adminlte-input name="descripcion" label="Descripción *" wire:model="descripcion"
                        placeholder="Ej: VERIFICACIÓN (DIAGNÓSTICO)" enable-old-support error-key="descripcion">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-align-left"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="costo" label="Costo (₲) *" type="number" step="0.01" min="0"
                        wire:model="costo" placeholder="0.00" enable-old-support error-key="costo">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="activo" wire:model="activo">
                            <label class="custom-control-label" for="activo">
                                {{ $activo ? 'Activo' : 'Inactivo' }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('servicios.tipos-servicio.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
