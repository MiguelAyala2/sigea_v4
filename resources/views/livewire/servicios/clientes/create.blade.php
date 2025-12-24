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

    <x-adminlte-card theme="light" title="Registrar Cliente" icon="fas fa-user-plus">
        <form class="row" wire:submit="guardar">

            {{-- Sección: Información Principal --}}
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-2 mb-3">Información Principal</h5>
            </div>

            <div class="form-group col-md-4">
                <label for="tipo_cliente">Tipo de Cliente *</label>
                <select wire:model="tipo_cliente" id="tipo_cliente" class="form-control form-control-sm">
                    <option value="">Seleccione</option>
                    <option value="fisica">Persona Física</option>
                    <option value="juridica">Persona Jurídica</option>
                </select>
                @error('tipo_cliente') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <x-adminlte-input name="documento" wire:model="documento"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="EJ: 12345678-9"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Documento/RUC *</div></x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="nombre" wire:model="nombre"
                oninput="this.value = this.value.toUpperCase()"
                placeholder="Nombre completo o razón social"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot"><div class="input-group-text">Nombre / Razón Social *</div></x-slot>
            </x-adminlte-input>

            {{-- Sección: Información de Contacto --}}
            <div class="col-md-12 mt-3">
                <h5 class="text-muted border-bottom pb-2 mb-3">Información de Contacto</h5>
            </div>

            <x-adminlte-input name="telefono" wire:model="telefono"
                placeholder="(021) 123-4567"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-phone"></i> Teléfono
                    </div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="celular" wire:model="celular"
                placeholder="0981 123-456"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-mobile-alt"></i> Celular
                    </div>
                </x-slot>
            </x-adminlte-input>

            <x-adminlte-input name="email" wire:model="email"
                type="email"
                placeholder="cliente@email.com"
                fgroup-class="col-md-4" igroup-size="sm">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-envelope"></i> Email
                    </div>
                </x-slot>
            </x-adminlte-input>

            {{-- Sección: Información Adicional --}}
            <div class="col-md-12 mt-3">
                <h5 class="text-muted border-bottom pb-2 mb-3">Información Adicional</h5>
            </div>

            <x-adminlte-textarea name="direccion" wire:model="direccion"
                placeholder="Dirección completa (opcional)"
                fgroup-class="col-md-12" igroup-size="sm" rows="2">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-map-marker-alt"></i> Dirección
                    </div>
                </x-slot>
            </x-adminlte-textarea>

            <x-adminlte-textarea name="observaciones" wire:model="observaciones"
                placeholder="Notas adicionales sobre el cliente (opcional)"
                fgroup-class="col-md-12" igroup-size="sm" rows="3">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-sticky-note"></i> Observaciones
                    </div>
                </x-slot>
            </x-adminlte-textarea>

            <div class="col-md-12"><hr></div>

            {{-- Botones --}}
            <div class="form-group col-md-3">
                <a href="{{ route('servicios.clientes.index') }}"
                   class="btn btn-block btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-group col-md-3">
                <x-adminlte-button type="submit"
                    label="Guardar Cliente"
                    theme="outline-success"
                    icon="fas fa-save"
                    class="w-100 btn-sm" />
            </div>
        </form>
    </x-adminlte-card>
</div>
