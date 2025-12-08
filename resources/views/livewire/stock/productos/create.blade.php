<div>
    <x-adminlte-card theme="light" title="Nuevo Producto" icon="fas fa-box">
        <form wire:submit="guardar">
            <div class="row">
                {{-- SECCIÓN: INFORMACIÓN BÁSICA --}}
                <div class="col-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-info-circle"></i> Información Básica
                    </h5>
                </div>

                <div class="col-md-2">
                    <x-adminlte-input name="codigo" label="Código"
                        value="Auto"
                        disabled
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-barcode"></i></div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-6">
                    <x-adminlte-input name="nombre" label="Nombre *"
                        wire:model.blur="nombre"
                        placeholder="Ej: Motobomba Centrífuga"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-tag"></i></div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-4">
                    <x-adminlte-input name="modelo" label="Modelo"
                        wire:model="modelo"
                        placeholder="Ej: CP-150"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-tag"></i></div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-6">
                    <x-adminlte-textarea name="descripcion" label="Descripción"
                        wire:model="descripcion"
                        placeholder="Descripción detallada del producto..."
                        rows="2" igroup-size="sm">
                    </x-adminlte-textarea>
                </div>

                <div class="col-md-6">
                    <x-adminlte-textarea name="aplicacion" label="Aplicación"
                        wire:model="aplicacion"
                        placeholder="Uso o aplicación del producto..."
                        rows="2" igroup-size="sm">
                    </x-adminlte-textarea>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="tiene_codigos"
                                wire:model.live="tiene_codigos">
                            <label class="custom-control-label" for="tiene_codigos">
                                Agregar Códigos (Barras / Fabricante)
                            </label>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN: CÓDIGOS --}}
                @if($tiene_codigos)
                    <div class="col-md-6">
                        <x-adminlte-input name="codigo_barras" label="Código de Barras"
                            wire:model="codigo_barras"
                            placeholder="Ej: 7501234567890"
                            igroup-size="sm">
                            <x-slot name="prependSlot">
                                <div class="input-group-text"><i class="fas fa-barcode"></i></div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>

                    <div class="col-md-6">
                        <x-adminlte-input name="codigo_fabricante" label="Código del Fabricante"
                            wire:model="codigo_fabricante"
                            placeholder="Ej: MFG-12345"
                            igroup-size="sm">
                            <x-slot name="prependSlot">
                                <div class="input-group-text"><i class="fas fa-industry"></i></div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                @endif

                {{-- SECCIÓN: CLASIFICACIÓN --}}
                <div class="col-12 mt-3">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-sitemap"></i> Clasificación
                    </h5>
                </div>

                <div class="col-md-4">
                    <x-adminlte-select name="tipo" label="Tipo *"
                        wire:model="tipo"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-layer-group"></i></div>
                        </x-slot>
                        <option value="PRODUCTO">Producto</option>
                        <option value="INSUMO">Insumo</option>
                        <option value="SERVICIO">Servicio</option>
                        <option value="KIT">Kit</option>
                    </x-adminlte-select>
                </div>

                <div class="col-md-4">
                    <x-adminlte-select name="origen" label="Origen *"
                        wire:model="origen"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-globe"></i></div>
                        </x-slot>
                        <option value="NACIONAL">Nacional</option>
                        <option value="IMPORTADO">Importado</option>
                    </x-adminlte-select>
                </div>

                <div class="col-md-4">
                    <x-adminlte-select name="unidad_medida_id" label="Unidad de Medida *"
                        wire:model="unidad_medida_id"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-ruler"></i></div>
                        </x-slot>
                        <option value="">-- Seleccione --</option>
                        @foreach($unidadesMedida as $unidad)
                            <option value="{{ $unidad->id }}">{{ $unidad->nombre }} ({{ $unidad->simbolo }})</option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-6">
                    <x-adminlte-select name="categoria_id" label="Categoría"
                        wire:model="categoria_id"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-folder"></i></div>
                        </x-slot>
                        <option value="">-- Sin categoría --</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre_indentado }}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-6">
                    <x-adminlte-select name="marca_id" label="Marca"
                        wire:model="marca_id"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-tag"></i></div>
                        </x-slot>
                        <option value="">-- Sin marca --</option>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                {{-- SECCIÓN: GESTIÓN --}}
                <div class="col-12 mt-3">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-cogs"></i> Configuración
                    </h5>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="permite_venta"
                                wire:model="permite_venta">
                            <label class="custom-control-label" for="permite_venta">
                                Permite Venta
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="permite_compra"
                                wire:model="permite_compra">
                            <label class="custom-control-label" for="permite_compra">
                                Permite Compra
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="maneja_stock"
                                wire:model="maneja_stock">
                            <label class="custom-control-label" for="maneja_stock">
                                Maneja Stock
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <x-adminlte-input name="stock_minimo" label="Stock Mínimo"
                        wire:model="stock_minimo"
                        type="number" step="0.01" min="0"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-box-open"></i></div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-4">
                    <x-adminlte-input name="stock_maximo" label="Stock Máximo"
                        wire:model="stock_maximo"
                        type="number" step="0.01" min="0"
                        igroup-size="sm">
                        <x-slot name="prependSlot">
                            <div class="input-group-text"><i class="fas fa-boxes"></i></div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="tiene_atributos"
                                wire:model.live="tiene_atributos">
                            <label class="custom-control-label" for="tiene_atributos">
                                Cargar Atributos Especiales
                            </label>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN: ATRIBUTOS --}}
                @if($tiene_atributos && $atributosTipo->count() > 0)
                    <div class="col-12 mt-3">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-list-ul"></i> Atributos / Especificaciones
                        </h5>
                    </div>

                    @foreach($atributosTipo as $atributo)
                        <div class="col-md-4">
                            <x-adminlte-input
                                name="atributos.{{ $atributo->id }}"
                                label="{{ $atributo->nombre_con_unidad }}"
                                wire:model="atributos.{{ $atributo->id }}"
                                placeholder="Ej: {{ $atributo->unidad ? '100' : 'Valor' }}"
                                igroup-size="sm">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text"><i class="fas fa-info-circle"></i></div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                    @endforeach
                @endif

                {{-- SECCIÓN: IMÁGENES --}}
                <div class="col-12 mt-3">
                    <h5 class="text-primary border-bottom pb-2 mb-3">
                        <i class="fas fa-images"></i> Galería de Imágenes
                    </h5>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Seleccionar Imágenes</label>
                        <input type="file" wire:model="imagenes" multiple accept="image/*" class="form-control-file">
                        @error('imagenes.*') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        <small class="text-muted">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB por imagen.</small>
                    </div>
                </div>

                @if(!empty($imagenes))
                    <div class="col-md-12">
                        <div class="row">
                            @foreach($imagenes as $index => $imagen)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <img src="{{ $imagen->temporaryUrl() }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio"
                                                           class="custom-control-input"
                                                           id="principal_{{ $index }}"
                                                           wire:click="establecerPrincipal({{ $index }})"
                                                           {{ $imagen_principal_index == $index ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="principal_{{ $index }}">
                                                        <small>Principal</small>
                                                    </label>
                                                </div>
                                                <button type="button"
                                                        wire:click="eliminarImagen({{ $index }})"
                                                        class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- BOTONES --}}
                <div class="col-md-12 mt-4">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('stock.productos.index') }}"
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
            </div>
        </form>
    </x-adminlte-card>
</div>
