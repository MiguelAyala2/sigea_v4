<div>
    <x-adminlte-card theme="primary" title="Nuevo Descuento" icon="fas fa-plus">
        <form wire:submit.prevent="guardar">
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="descripcion" label="Descripción del Descuento *"
                        wire:model="descripcion" placeholder="Ingrese la descripción">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-percent"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <x-adminlte-select name="tipo_descuento" label="Tipo de Descuento *" wire:model="tipo_descuento">
                        <option value="">Seleccione...</option>
                        <option value="porcentaje">Porcentaje</option>
                        <option value="monto_fijo">Monto Fijo</option>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-list"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-select>
                    @error('tipo_descuento') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <x-adminlte-input name="valor" label="Valor *" type="number"
                        wire:model="valor" placeholder="0" min="0" step="0.01">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-calculator"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('valor') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-input name="aplicable_a" label="Aplicable a *"
                        wire:model="aplicable_a" placeholder="Ej: Todos los servicios, Servicios mayores a ₲500.000">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-tags"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    @error('aplicable_a') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="fecha_inicio" label="Fecha de Inicio (opcional)" type="date"
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
                    <x-adminlte-input name="fecha_fin" label="Fecha de Fin (opcional)" type="date"
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
                    <x-adminlte-textarea name="observaciones" label="Observaciones (opcional)"
                        wire:model="observaciones" placeholder="Observaciones adicionales" rows="3">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-align-left"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-textarea>
                    @error('observaciones') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Registrar Descuento
                    </button>
                    <a href="{{ route('servicios.descuentos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
