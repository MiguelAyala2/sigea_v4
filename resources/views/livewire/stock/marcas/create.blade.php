<div>
    <x-adminlte-card theme="light" title="Nueva Marca" icon="fas fa-tag">
        <form wire:submit="guardar" class="row">
            <div class="col-md-12">
                <x-adminlte-input name="nombre" label="Nombre *"
                    wire:model.blur="nombre"
                    placeholder="Ej: Grundfos"
                    igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-tag"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-12">
                <x-adminlte-textarea name="descripcion" label="Descripción"
                    wire:model="descripcion"
                    placeholder="Descripción de la marca..."
                    rows="3" igroup-size="sm">
                </x-adminlte-textarea>
            </div>

            <div class="col-md-12 mt-3">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('stock.marcas.index') }}"
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