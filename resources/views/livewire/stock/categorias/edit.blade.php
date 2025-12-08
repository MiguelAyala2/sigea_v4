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

    <x-adminlte-card theme="light" title="Editar Categoría" icon="fas fa-edit">
        <form wire:submit="guardar" class="row">
            <div class="col-md-6">
                <x-adminlte-input name="codigo" label="Código"
                    wire:model.blur="codigo"
                    disabled
                    igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-barcode"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-6">
                <x-adminlte-input name="nivel" label="Nivel"
                    value="{{ $nivel == 1 ? 'Grupo' : ($nivel == 2 ? 'Subgrupo' : 'Especialización') }}"
                    disabled
                    igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-6">
                <x-adminlte-input name="nombre" label="Nombre *"
                    wire:model.blur="nombre"
                    igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-tag"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-6">
                <x-adminlte-select name="parent_id" label="Categoría Padre (Opcional)"
                    wire:model="parent_id"
                    igroup-size="sm">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-folder-tree"></i>
                        </div>
                    </x-slot>
                    <option value="">-- Sin Categoría Padre (Raíz) --</option>
                    @foreach($categoriasDisponibles as $cat)
                        <option value="{{ $cat->id }}">
                            {{ $cat->nombre_indentado }} ({{ $cat->codigo }})
                        </option>
                    @endforeach
                </x-adminlte-select>
            </div>

            <div class="col-md-12">
                <x-adminlte-textarea name="descripcion" label="Descripción"
                    wire:model="descripcion"
                    rows="3" igroup-size="sm">
                </x-adminlte-textarea>
            </div>

            <div class="col-md-12">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Advertencia:</strong> Si cambias la categoría padre, el nivel y código se recalcularán automáticamente en la próxima actualización del registro.
                </div>
            </div>

            <div class="col-md-12 mt-3">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('stock.categorias.index') }}"
                           class="btn btn-block btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                    <div class="col-md-3">
                        <x-adminlte-button type="submit" label="Actualizar"
                            theme="outline-success" icon="fas fa-save"
                            class="w-100 btn-sm" />
                    </div>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
