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

    <x-adminlte-card theme="light" title="Editar Depósito" icon="fas fa-edit">
        <form class="row" wire:submit="guardar">

            <div class="form-group col-md-6">
                <label for="sucursal_id">Sucursal *</label>
                <select wire:model="sucursal_id" id="sucursal_id" class="form-control form-control-sm">
                    <option value="">Seleccione una sucursal</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">
                            {{ $sucursal->codigo_establecimiento }} - {{ $sucursal->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('sucursal_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6"></div>

            <x-adminlte-input name="codigo" wire:model="codigo"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: DEP01"
                fgroup-class="col-md-3" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Código *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="nombre" wire:model="nombre"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: DEPÓSITO PRINCIPAL"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Nombre *</div></x-slot>
            </x-adminlte-input>

            <div class="form-group col-md-3">
                <label>
                    <input type="checkbox" wire:model="es_principal"> Es Principal
                </label>
            </div>

            <x-adminlte-textarea name="descripcion" wire:model="descripcion"
                placeholder="Descripción opcional del depósito"
                fgroup-class="col-md-9" igroup-size="sm" rows="2">
                <x-slot name="prependSlot"><div class="input-group-text">Descripción</div></x-slot>
            </x-adminlte-textarea>

            <div class="form-group col-md-3">
                <label>
                    <input type="checkbox" wire:model="permite_venta"> Permite Venta
                </label>
            </div>

            <div class="col-md-12"><hr></div>

            <div class="form-group col-md-3">
                <a href="{{ route('empresa.depositos.index') }}" class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit" label="Actualizar Depósito" theme="outline-success" icon="fas fa-save" class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
