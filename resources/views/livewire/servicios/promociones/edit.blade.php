<div>
    <x-adminlte-card theme="warning" title="Editar Promoción" icon="fas fa-edit">
        <form wire:submit.prevent="actualizar">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Código:</strong> {{ $codigo }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="nombre" label="Nombre de la Promoción *"
                        wire:model="nombre" placeholder="Ingrese el nombre">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <x-adminlte-select name="tipo" label="Tipo de Promoción *" wire:model="tipo">
                        <option value="">Seleccione...</option>
                        <option value="general">General</option>
                        <option value="servicio">Servicio</option>
                        <option value="producto">Producto</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-list"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                    @error('tipo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="descuento" label="Descuento (%) *" type="number"
                        wire:model="descuento" placeholder="0" min="0" max="100" step="0.01">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('descuento') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="fecha_inicio" label="Fecha de Inicio *" type="date"
                        wire:model="fecha_inicio">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('fecha_inicio') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6">
                    <x-adminlte-input name="fecha_fin" label="Fecha de Fin *" type="date"
                        wire:model="fecha_fin">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('fecha_fin') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea name="descripcion" label="Descripción"
                        wire:model="descripcion" placeholder="Descripción de la promoción (opcional)" rows="3">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-align-left"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-textarea>
                    @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Actualizar Promoción
                    </button>
                    <a href="{{ route('servicios.promociones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
