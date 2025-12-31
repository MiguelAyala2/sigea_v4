<div>
    <form wire:submit.prevent="guardar">
        <div class="row">
            <div class="col-md-8">
                @if(!$compra)
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">1. Seleccionar Factura de Compra</h3></div>
                        <div class="card-body">
                            <input type="text" wire:model.live.debounce.300ms="search_compra" class="form-control" placeholder="Buscar factura...">
                            @if($mostrar_busqueda_compra && count($compras_encontradas) > 0)
                                <div class="list-group mt-2">
                                    @foreach($compras_encontradas as $item)
                                        <button type="button" wire:click="seleccionarCompra({{ $item->id }})" class="list-group-item list-group-item-action">
                                            <strong>{{ $item->numero_factura }}</strong> - {{ $item->proveedor->nombre }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">Factura: {{ $compra->numero_factura }}</div>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">2. Cantidades Recibidas</h3></div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead><tr><th>Producto</th><th>Esperada</th><th>Recibida</th></tr></thead>
                                <tbody>
                                    @foreach($detalles as $index => $detalle)
                                        <tr>
                                            <td>{{ $detalle['producto_nombre'] }}</td>
                                            <td>{{ $detalle['cantidad_esperada'] }}</td>
                                            <td><input type="number" wire:model="detalles.{{ $index }}.cantidad_recibida" class="form-control form-control-sm"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group"><label>Fecha</label><input type="date" wire:model="fecha_recepcion" class="form-control"></div>
                        <div class="form-group"><label>Depósito</label><select wire:model="deposito_id" class="form-control"><option value="">Seleccionar...</option>@foreach($depositos as $d)<option value="{{ $d->id }}">{{ $d->nombre }}</option>@endforeach</select></div>
                        <div class="form-group"><label>Remisión</label><input type="text" wire:model="numero_remision" class="form-control"></div>
                        @if($compra)<button type="submit" class="btn btn-primary btn-block">Guardar</button>@endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
