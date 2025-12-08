<div>
    <x-adminlte-card theme="light" icon="fas fa-box">
        <x-slot name="title">
            {{ $producto->nombre }}
            @if(!$producto->activo)
                <span class="badge badge-danger ml-2">Inactivo</span>
            @endif
        </x-slot>

        <x-slot name="toolsSlot">
            <a href="{{ route('stock.productos.edit', $producto->id) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('stock.productos.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </x-slot>

        {{-- Tabs Navigation --}}
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $tabActiva === 'informacion' ? 'active' : '' }}"
                   wire:click="cambiarTab('informacion')"
                   role="tab"
                   style="cursor: pointer;">
                    <i class="fas fa-info-circle"></i> Información
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabActiva === 'stock' ? 'active' : '' }}"
                   wire:click="cambiarTab('stock')"
                   role="tab"
                   style="cursor: pointer;">
                    <i class="fas fa-boxes"></i> Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabActiva === 'precios' ? 'active' : '' }}"
                   wire:click="cambiarTab('precios')"
                   role="tab"
                   style="cursor: pointer;">
                    <i class="fas fa-dollar-sign"></i> Precios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabActiva === 'movimientos' ? 'active' : '' }}"
                   wire:click="cambiarTab('movimientos')"
                   role="tab"
                   style="cursor: pointer;">
                    <i class="fas fa-exchange-alt"></i> Últimos Movimientos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabActiva === 'imagenes' ? 'active' : '' }}"
                   wire:click="cambiarTab('imagenes')"
                   role="tab"
                   style="cursor: pointer;">
                    <i class="fas fa-images"></i> Imágenes
                </a>
            </li>
        </ul>

        {{-- Tabs Content --}}
        <div class="tab-content mt-3">
            {{-- TAB: INFORMACIÓN --}}
            @if($tabActiva === 'informacion')
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Código:</th>
                                <td><span class="badge badge-secondary">{{ $producto->codigo }}</span></td>
                            </tr>
                            @if($producto->codigo_barras)
                                <tr>
                                    <th>Código de Barras:</th>
                                    <td><i class="fas fa-barcode"></i> {{ $producto->codigo_barras }}</td>
                                </tr>
                            @endif
                            @if($producto->codigo_fabricante)
                                <tr>
                                    <th>Código Fabricante:</th>
                                    <td>{{ $producto->codigo_fabricante }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Tipo:</th>
                                <td>
                                    @if($producto->tipo == 'PRODUCTO')
                                        <span class="badge badge-primary">Producto</span>
                                    @elseif($producto->tipo == 'INSUMO')
                                        <span class="badge badge-info">Insumo</span>
                                    @elseif($producto->tipo == 'SERVICIO')
                                        <span class="badge badge-warning">Servicio</span>
                                    @else
                                        <span class="badge badge-secondary">Kit</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Origen:</th>
                                <td>{{ $producto->origen }}</td>
                            </tr>
                            <tr>
                                <th>Unidad de Medida:</th>
                                <td>{{ $producto->unidadMedida->nombre }} ({{ $producto->unidadMedida->simbolo }})</td>
                            </tr>
                            @if($producto->categoria)
                                <tr>
                                    <th>Categoría:</th>
                                    <td>{{ $producto->categoria->nombre }}</td>
                                </tr>
                            @endif
                            @if($producto->marca)
                                <tr>
                                    <th>Marca:</th>
                                    <td>{{ $producto->marca->nombre }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            @if($producto->modelo)
                                <tr>
                                    <th width="40%">Modelo:</th>
                                    <td>{{ $producto->modelo }}</td>
                                </tr>
                            @endif
                            @if($producto->descripcion)
                                <tr>
                                    <th>Descripción:</th>
                                    <td>{{ $producto->descripcion }}</td>
                                </tr>
                            @endif
                            @if($producto->aplicacion)
                                <tr>
                                    <th>Aplicación:</th>
                                    <td>{{ $producto->aplicacion }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Permite Venta:</th>
                                <td>
                                    @if($producto->permite_venta)
                                        <span class="badge badge-success">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Permite Compra:</th>
                                <td>
                                    @if($producto->permite_compra)
                                        <span class="badge badge-success">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Maneja Stock:</th>
                                <td>
                                    @if($producto->maneja_stock)
                                        <span class="badge badge-success">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Stock Mínimo:</th>
                                <td>{{ $producto->stock_minimo }}</td>
                            </tr>
                            @if($producto->stock_maximo)
                                <tr>
                                    <th>Stock Máximo:</th>
                                    <td>{{ $producto->stock_maximo }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    {{-- Atributos --}}
                    @if($producto->atributos->count() > 0)
                        <div class="col-12 mt-3">
                            <h5 class="text-primary border-bottom pb-2">
                                <i class="fas fa-list-ul"></i> Atributos / Especificaciones
                            </h5>
                            <div class="row">
                                @foreach($producto->atributos as $atributo)
                                    <div class="col-md-4">
                                        <strong>{{ $atributo->nombre }}:</strong>
                                        {{ $atributo->pivot->valor }}
                                        @if($atributo->unidad)
                                            {{ $atributo->unidad }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- TAB: STOCK --}}
            @if($tabActiva === 'stock')
                <div class="row">
                    @if($producto->stock->count() > 0)
                        @foreach($producto->stock as $stock)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-warehouse"></i> {{ $stock->deposito->nombre }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <th width="50%">Stock Actual:</th>
                                                <td>
                                                    <span class="badge badge-{{ $stock->estado_badge }} badge-lg">
                                                        {{ $stock->stock_actual }} {{ $producto->unidadMedida->simbolo }}
                                                    </span>
                                                    <small class="text-muted">({{ ucfirst($stock->estado) }})</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Stock Mínimo:</th>
                                                <td>{{ $stock->stock_minimo }} {{ $producto->unidadMedida->simbolo }}</td>
                                            </tr>
                                            @if($stock->stock_maximo)
                                                <tr>
                                                    <th>Stock Máximo:</th>
                                                    <td>{{ $stock->stock_maximo }} {{ $producto->unidadMedida->simbolo }}</td>
                                                </tr>
                                            @endif
                                            @if($stock->ubicacion)
                                                <tr>
                                                    <th>Ubicación:</th>
                                                    <td>{{ $stock->ubicacion }}</td>
                                                </tr>
                                            @endif
                                            @if($stock->lote)
                                                <tr>
                                                    <th>Lote:</th>
                                                    <td>{{ $stock->lote }}</td>
                                                </tr>
                                            @endif
                                            @if($stock->fecha_vencimiento)
                                                <tr>
                                                    <th>Fecha Vencimiento:</th>
                                                    <td>{{ $stock->fecha_vencimiento->format('d/m/Y') }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Este producto no tiene stock registrado en ningún depósito.
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- TAB: PRECIOS --}}
            @if($tabActiva === 'precios')
                <div class="row">
                    @if($producto->precioActual)
                        <div class="col-md-6">
                            <h5 class="text-primary">Precio Actual</h5>
                            <table class="table table-sm table-striped">
                                <tr>
                                    <th width="50%">Precio de Compra:</th>
                                    <td>{{ $producto->precioActual->precio_compra_formateado }}</td>
                                </tr>
                                <tr>
                                    <th>Precio de Venta:</th>
                                    <td><strong class="text-success">{{ $producto->precioActual->precio_venta_formateado }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Margen:</th>
                                    <td>{{ number_format($producto->precioActual->margen_porcentaje, 2) }}%</td>
                                </tr>
                                <tr>
                                    <th>IVA:</th>
                                    <td>{{ $producto->precioActual->iva_texto }}</td>
                                </tr>
                                <tr>
                                    <th>Moneda:</th>
                                    <td>{{ $producto->precioActual->moneda }}</td>
                                </tr>
                                @if($producto->precioActual->tipo_cambio)
                                    <tr>
                                        <th>Tipo de Cambio:</th>
                                        <td>{{ $producto->precioActual->tipo_cambio }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Este producto no tiene precio configurado.
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- TAB: MOVIMIENTOS --}}
            @if($tabActiva === 'movimientos')
                @if($producto->movimientosStock->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Depósito</th>
                                    <th class="text-right">Cantidad</th>
                                    <th class="text-right">Stock Anterior</th>
                                    <th class="text-right">Stock Posterior</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($producto->movimientosStock as $movimiento)
                                    <tr>
                                        <td>
                                            <small>{{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $movimiento->tipo_badge }}">
                                                {{ $movimiento->tipo_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $movimiento->deposito->nombre }}</small>
                                        </td>
                                        <td class="text-right">
                                            @if($movimiento->es_entrada)
                                                <span class="text-success">+{{ $movimiento->cantidad }}</span>
                                            @else
                                                <span class="text-danger">-{{ $movimiento->cantidad }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $movimiento->stock_anterior }}</td>
                                        <td class="text-right"><strong>{{ $movimiento->stock_posterior }}</strong></td>
                                        <td>
                                            <small>{{ $movimiento->usuario->name }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('stock.productos.kardex', $producto->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-list"></i> Ver Kardex Completo
                        </a>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Este producto no tiene movimientos de stock registrados.
                    </div>
                @endif
            @endif

            {{-- TAB: IMÁGENES --}}
            @if($tabActiva === 'imagenes')
                @if($producto->imagenes->count() > 0)
                    <div class="row">
                        @foreach($producto->imagenes as $imagen)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <img src="{{ asset('storage/' . $imagen->path) }}"
                                         class="card-img-top"
                                         alt="{{ $producto->nombre }}"
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-2 text-center">
                                        @if($imagen->es_principal)
                                            <span class="badge badge-success">Principal</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $imagen->nombre_original }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Este producto no tiene imágenes.
                    </div>
                @endif
            @endif
        </div>
    </x-adminlte-card>
</div>
