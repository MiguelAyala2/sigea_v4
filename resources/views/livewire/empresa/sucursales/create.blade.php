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

    <x-adminlte-card theme="light" title="Crear Sucursal" icon="fas fa-plus">
        <form class="row" wire:submit="guardar">

            <x-adminlte-input name="codigo_establecimiento" wire:model="codigo_establecimiento"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: 001"
                fgroup-class="col-md-3" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Código Est. *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="nombre" wire:model="nombre"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: CASA CENTRAL"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Nombre *</div></x-slot>
            </x-adminlte-input>

            <div class="form-group col-md-3">
                <label>
                    <input type="checkbox" wire:model="es_casa_central"> Es Casa Central
                </label>
            </div>

            <x-adminlte-input name="direccion" wire:model="direccion"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: AV. ESPAÑA 1234"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Dirección *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="ciudad" wire:model="ciudad"
                oninput="this.value = this.value.toUpperCase()"
                fgroup-class="col-md-3" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Ciudad *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="departamento" wire:model="departamento"
                oninput="this.value = this.value.toUpperCase()"
                fgroup-class="col-md-3" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Departamento *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="telefono" wire:model="telefono"
                fgroup-class="col-md-3" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Teléfono</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input type="email" name="email" wire:model="email"
                fgroup-class="col-md-3" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Email</div></x-slot>
            </x-adminlte-input>

            <div class="col-md-12"><hr></div>

            <div class="form-group col-md-3">
                <a href="{{ route('empresa.sucursales.index') }}" class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit" label="Crear Sucursal" theme="outline-success" icon="fas fa-save" class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
