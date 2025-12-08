<div>
    <x-adminlte-card theme="light" title="Nueva Categoría" icon="fas fa-sitemap">
        <form wire:submit="guardar" class="row">
            <div class="col-md-6">
                <x-adminlte-input name="nombre" label="Nombre *"
                    wire:model.blur="nombre"
                    placeholder="Ej: Motobombas"
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
                    placeholder="Descripción de la categoría..."
                    rows="3" igroup-size="sm">
                </x-adminlte-textarea>
            </div>

            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Información:</strong>
                    <ul class="mb-0 mt-2">
                        <li>El código se generará automáticamente según la jerarquía</li>
                        <li><strong>Sin padre:</strong> Código formato "01" (Grupo)</li>
                        <li><strong>Con padre nivel 1:</strong> Código formato "01.01" (Subgrupo)</li>
                        <li><strong>Con padre nivel 2:</strong> Código formato "01.01.01" (Especialización)</li>
                    </ul>
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
                        <x-adminlte-button type="submit" label="Guardar"
                            theme="outline-success" icon="fas fa-save"
                            class="w-100 btn-sm" />
                    </div>
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>
