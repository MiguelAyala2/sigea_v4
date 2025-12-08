<div>
    <x-adminlte-card theme="light" icon="fas fa-plus">
        <x-slot name="title">
            Crear Nuevo Atributo Tipo
        </x-slot>

        <x-slot name="toolsSlot">
            <a href="{{ route('stock.atributos-tipo.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </x-slot>

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <form wire:submit.prevent="guardar">
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input
                        name="nombre"
                        label="Nombre *"
                        placeholder="Ej: Potencia, Diámetro, Material..."
                        wire:model="nombre"
                        enable-old-support
                    />
                    @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <x-adminlte-input
                        name="unidad"
                        label="Unidad"
                        placeholder="Ej: HP, mm, kg..."
                        wire:model="unidad"
                        enable-old-support
                    />
                    @error('unidad') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-3">
                    <x-adminlte-input
                        name="orden"
                        label="Orden *"
                        type="number"
                        min="0"
                        wire:model="orden"
                        enable-old-support
                    />
                    @error('orden') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <x-adminlte-textarea
                        name="descripcion"
                        label="Descripción"
                        rows="3"
                        placeholder="Descripción opcional del atributo..."
                        wire:model="descripcion"
                        enable-old-support
                    />
                    @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="es_filtrable"
                               wire:model="es_filtrable">
                        <label class="custom-control-label" for="es_filtrable">
                            <strong>Es Filtrable</strong>
                            <br><small class="text-muted">Permite filtrar productos por este atributo</small>
                        </label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="es_requerido"
                               wire:model="es_requerido">
                        <label class="custom-control-label" for="es_requerido">
                            <strong>Es Requerido</strong>
                            <br><small class="text-muted">Debe especificarse en productos</small>
                        </label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="activo"
                               wire:model="activo">
                        <label class="custom-control-label" for="activo">
                            <strong>Activo</strong>
                            <br><small class="text-muted">Disponible para usar</small>
                        </label>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('stock.atributos-tipo.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
