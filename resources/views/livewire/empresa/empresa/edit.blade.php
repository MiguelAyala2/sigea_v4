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

    <x-adminlte-card theme="light" title="Configurar Empresa" icon="fas fa-building">
        <form class="row" wire:submit="guardar">

            <x-adminlte-input name="razon_social" wire:model="razon_social"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: MI EMPRESA S.A."
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Razón Social *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="ruc" wire:model="ruc" placeholder="EJ: 80012345"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">RUC *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="dv" wire:model="dv" placeholder="DV" maxlength="1"
                fgroup-class="col-md-2" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">DV *</div></x-slot>
            </x-adminlte-input>

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
                <a href="{{ route('empresa.empresa.index') }}" class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit" label="Guardar" theme="outline-success" icon="fas fa-save" class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
