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

    <x-adminlte-card theme="light" title="Crear Timbrado" icon="fas fa-plus">
        <form class="row" wire:submit="guardar">

            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-2 mb-3">Información del Timbrado</h5>
            </div>

            <x-adminlte-input name="numero_timbrado" wire:model="numero_timbrado"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: 12345678901234"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Número Timbrado *</div></x-slot>
            </x-adminlte-input>

            <div class="form-group col-md-3">
                <label for="tipo_documento">Tipo Documento *</label>
                <select wire:model="tipo_documento" id="tipo_documento" class="form-control form-control-sm">
                    <option value="">Seleccione un tipo</option>
                    @foreach(\App\Models\Empresa\Timbrado::TIPOS_DOCUMENTO as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo_documento') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group col-md-3">
                <label>
                    <input type="checkbox" wire:model="es_electronico"> Es Electrónico
                </label>
            </div>

            <div class="col-md-12"><hr></div>
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-2 mb-3">Vigencia</h5>
            </div>

            <x-adminlte-input name="fecha_inicio_vigencia" wire:model="fecha_inicio_vigencia"
                type="date"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Fecha Inicio Vigencia *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="fecha_fin_vigencia" wire:model="fecha_fin_vigencia"
                type="date"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Fecha Fin Vigencia *</div></x-slot>
            </x-adminlte-input>

            <div class="col-md-12"><hr></div>
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-2 mb-3">Rango de Numeración</h5>
            </div>

            <x-adminlte-input name="numero_desde" wire:model="numero_desde"
                type="number"
                placeholder="1"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Número Desde *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="numero_hasta" wire:model="numero_hasta"
                type="number"
                placeholder="10000"
                fgroup-class="col-md-6" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Número Hasta *</div></x-slot>
            </x-adminlte-input>

            @if($es_electronico)
                <div class="col-md-12"><hr></div>
                <div class="col-md-12">
                    <h5 class="text-muted border-bottom pb-2 mb-3">Configuración Electrónica</h5>
                </div>

                <div class="form-group col-md-6">
                    <label for="cdc_ambiente">Ambiente CDC *</label>
                    <select wire:model="cdc_ambiente" id="cdc_ambiente" class="form-control form-control-sm">
                        <option value="">Seleccione un ambiente</option>
                        @foreach(\App\Models\Empresa\Timbrado::AMBIENTES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('cdc_ambiente') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6"></div>
            @endif

            <div class="col-md-12"><hr></div>

            <div class="form-group col-md-3">
                <a href="{{ route('empresa.timbrados.index') }}" class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit" label="Crear Timbrado" theme="outline-success" icon="fas fa-save" class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
