<div>
    <x-adminlte-card theme="light" title="Nueva Unidad de Medida" icon="fas fa-ruler">
        <form wire:submit="guardar" class="row">
            <div class="col-md-6">
                <x-adminlte-input name="nombre" label="Nombre *"
                    wire:model.blur="nombre"
                    placeholder="Ej: Unidad, Metro, Litro"
                    igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-tag"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-6">
                <x-adminlte-input name="simbolo" label="Símbolo *"
                    wire:model.blur="simbolo"
                    placeholder="Ej: Un, m, L, kg"
                    igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-signature"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="permite_decimales"
                               wire:model="permite_decimales">
                        <label class="custom-control-label" for="permite_decimales">
                            Permite decimales (para kg, litros, metros, etc.)
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-3">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('stock.unidades-medida.index') }}"
                           class="btn btn-block btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-button type="submit" label="Guardar"
                            theme="outline-success" icon="fas fa-save"
                            class="w-100 btn-sm" />
                    </div>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
