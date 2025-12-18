<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- INFORMACIÓN BÁSICA -->
            <div class="col-md-12">
                <h5 class="mb-3"><i class="fas fa-id-card"></i> Información Básica</h5>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="razon_social">Razón Social <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('razon_social') is-invalid @enderror"
                           id="razon_social" wire:model.blur="razon_social">
                    @error('razon_social') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre_fantasia">Nombre de Fantasía</label>
                    <input type="text" class="form-control @error('nombre_fantasia') is-invalid @enderror"
                           id="nombre_fantasia" wire:model.blur="nombre_fantasia">
                    @error('nombre_fantasia') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="ruc">RUC <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('ruc') is-invalid @enderror"
                           id="ruc" wire:model.blur="ruc">
                    @error('ruc') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label for="dv">DV</label>
                    <input type="text" class="form-control @error('dv') is-invalid @enderror"
                           id="dv" wire:model.blur="dv" maxlength="2">
                    @error('dv') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo_persona">Tipo de Persona</label>
                    <select class="form-control @error('tipo_persona') is-invalid @enderror"
                            id="tipo_persona" wire:model.live="tipo_persona">
                        <option value="FISICA">Persona Física</option>
                        <option value="JURIDICA">Persona Jurídica</option>
                    </select>
                    @error('tipo_persona') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo_proveedor">Tipo de Proveedor</label>
                    <select class="form-control @error('tipo_proveedor') is-invalid @enderror"
                            id="tipo_proveedor" wire:model.live="tipo_proveedor">
                        <option value="PRODUCTOS">Productos</option>
                        <option value="SERVICIOS">Servicios</option>
                        <option value="AMBOS">Productos y Servicios</option>
                    </select>
                    @error('tipo_proveedor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- CONTACTO -->
            <div class="col-md-12 mt-3">
                <h5 class="mb-3"><i class="fas fa-phone"></i> Información de Contacto</h5>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                           id="telefono" wire:model.blur="telefono">
                    @error('telefono') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" class="form-control @error('celular') is-invalid @enderror"
                           id="celular" wire:model.blur="celular">
                    @error('celular') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" wire:model.blur="email">
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="sitio_web">Sitio Web</label>
                    <input type="url" class="form-control @error('sitio_web') is-invalid @enderror"
                           id="sitio_web" wire:model.blur="sitio_web">
                    @error('sitio_web') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- DIRECCIÓN -->
            <div class="col-md-12 mt-3">
                <h5 class="mb-3"><i class="fas fa-map-marker-alt"></i> Dirección</h5>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea class="form-control @error('direccion') is-invalid @enderror"
                              id="direccion" wire:model.blur="direccion" rows="2"></textarea>
                    @error('direccion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" class="form-control @error('ciudad') is-invalid @enderror"
                           id="ciudad" wire:model.blur="ciudad">
                    @error('ciudad') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="departamento">Departamento</label>
                    <input type="text" class="form-control @error('departamento') is-invalid @enderror"
                           id="departamento" wire:model.blur="departamento">
                    @error('departamento') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="pais">País</label>
                    <input type="text" class="form-control @error('pais') is-invalid @enderror"
                           id="pais" wire:model.blur="pais">
                    @error('pais') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- PERSONA DE CONTACTO -->
            <div class="col-md-12 mt-3">
                <h5 class="mb-3"><i class="fas fa-user"></i> Persona de Contacto</h5>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="contacto_nombre">Nombre</label>
                    <input type="text" class="form-control @error('contacto_nombre') is-invalid @enderror"
                           id="contacto_nombre" wire:model.blur="contacto_nombre">
                    @error('contacto_nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="contacto_cargo">Cargo</label>
                    <input type="text" class="form-control @error('contacto_cargo') is-invalid @enderror"
                           id="contacto_cargo" wire:model.blur="contacto_cargo">
                    @error('contacto_cargo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="contacto_telefono">Teléfono</label>
                    <input type="text" class="form-control @error('contacto_telefono') is-invalid @enderror"
                           id="contacto_telefono" wire:model.blur="contacto_telefono">
                    @error('contacto_telefono') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="contacto_email">Email</label>
                    <input type="email" class="form-control @error('contacto_email') is-invalid @enderror"
                           id="contacto_email" wire:model.blur="contacto_email">
                    @error('contacto_email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- CONDICIONES COMERCIALES -->
            <div class="col-md-12 mt-3">
                <h5 class="mb-3"><i class="fas fa-handshake"></i> Condiciones Comerciales</h5>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="dias_plazo_pago">Plazo de Pago (días)</label>
                    <input type="number" class="form-control @error('dias_plazo_pago') is-invalid @enderror"
                           id="dias_plazo_pago" wire:model.blur="dias_plazo_pago" min="0">
                    @error('dias_plazo_pago') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">0 = Contado</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="limite_credito">Límite de Crédito (₲)</label>
                    <input type="number" class="form-control @error('limite_credito') is-invalid @enderror"
                           id="limite_credito" wire:model.blur="limite_credito" min="0" step="0.01">
                    @error('limite_credito') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="descuento_habitual">Descuento Habitual (%)</label>
                    <input type="number" class="form-control @error('descuento_habitual') is-invalid @enderror"
                           id="descuento_habitual" wire:model.blur="descuento_habitual" min="0" max="100" step="0.01">
                    @error('descuento_habitual') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- INFORMACIÓN BANCARIA -->
            <div class="col-md-12 mt-3">
                <h5 class="mb-3"><i class="fas fa-university"></i> Información Bancaria</h5>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="banco">Banco</label>
                    <input type="text" class="form-control @error('banco') is-invalid @enderror"
                           id="banco" wire:model.blur="banco">
                    @error('banco') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="tipo_cuenta">Tipo de Cuenta</label>
                    <input type="text" class="form-control @error('tipo_cuenta') is-invalid @enderror"
                           id="tipo_cuenta" wire:model.blur="tipo_cuenta">
                    @error('tipo_cuenta') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="numero_cuenta">Número de Cuenta</label>
                    <input type="text" class="form-control @error('numero_cuenta') is-invalid @enderror"
                           id="numero_cuenta" wire:model.blur="numero_cuenta">
                    @error('numero_cuenta') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- CONFIGURACIÓN -->
            <div class="col-md-12 mt-3">
                <h5 class="mb-3"><i class="fas fa-cog"></i> Configuración</h5>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="activo" wire:model.live="activo">
                        <label class="custom-control-label" for="activo">Activo</label>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="es_nacional" wire:model.live="es_nacional">
                        <label class="custom-control-label" for="es_nacional">Es Nacional</label>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="contribuyente" wire:model.live="contribuyente">
                        <label class="custom-control-label" for="contribuyente">Contribuyente</label>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror"
                              id="observaciones" wire:model.blur="observaciones" rows="3"></textarea>
                    @error('observaciones') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="row mt-4">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <a href="{{ route('compras.proveedores.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
