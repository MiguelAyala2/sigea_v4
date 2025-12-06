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

    @if($empresa)
        <x-adminlte-card theme="light" title="Datos de la Empresa" icon="fas fa-building">
            <div class="row">
                <div class="col-md-6">
                    <dl>
                        <dt>Razón Social</dt>
                        <dd>{{ $empresa->razon_social ?? 'N/A' }}</dd>

                        <dt>RUC</dt>
                        <dd>{{ $empresa->ruc ?? 'N/A' }}-{{ $empresa->dv ?? '0' }}</dd>

                        <dt>Dirección</dt>
                        <dd>{{ $empresa->direccion ?? 'N/A' }}</dd>

                        <dt>Teléfono</dt>
                        <dd>{{ $empresa->telefono ?? 'N/A' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl>
                        <dt>Email</dt>
                        <dd>{{ $empresa->email ?? 'N/A' }}</dd>

                        <dt>Ciudad</dt>
                        <dd>{{ $empresa->ciudad ?? 'N/A' }}</dd>

                        <dt>Departamento</dt>
                        <dd>{{ $empresa->departamento ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>

            <a href="{{ route('empresa.empresa.edit') }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar Datos
            </a>
        </x-adminlte-card>
    @else
        <x-adminlte-card theme="warning" title="Configuración Pendiente" icon="fas fa-exclamation-triangle">
            <p>No se ha configurado los datos de la empresa todavía.</p>
            <a href="{{ route('empresa.empresa.edit') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Configurar Empresa
            </a>
        </x-adminlte-card>
    @endif
</div>
