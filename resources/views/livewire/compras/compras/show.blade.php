<div>
    {{-- Header con información básica --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Compra #{{ $compra->numero_factura }}</h4>
                    <small class="text-muted">
                        Proveedor: {{ $compra->proveedor->razon_social }} | 
                        Fecha: {{ $compra->fecha_emision->format('d/m/Y') }} | 
                        Estado: {{ $compra->estado_texto }}
                    </small>
                </div>
                <div class="btn-group">
                    <a href="{{ route('compras.compras.edit', $compra) }}" 
                       class="btn btn-warning" {{ $compra->estado != 'BORRADOR' ? 'disabled' : '' }}>
                        <i class="fas fa-edit mr-1"></i> Editar
                    </a>
                    <a href="{{ route('compras.dashboard.flujo.show', $compra) }}" 
                       class="btn btn-info ml-1">
                        <i class="fas fa-project-diagram mr-1"></i> Ver Flujo
                    </a>
                    <a href="{{ route('compras.compras.index') }}" 
                       class="btn btn-secondary ml-1">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Pestañas --}}
    <div class="card">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" id="compra-tabs">
                @foreach($tabs as $tabId => $tab)
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == $tabId ? 'active' : '' }}" 
                           href="#" wire:click="cambiarTab('{{ $tabId }}')">
                            <i class="{{ $tab['icon'] }} mr-1"></i>
                            {{ $tab['label'] }}
                            @if($tab['badge'])
                                <span class="badge badge-primary ml-1">{{ $tab['badge'] }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card-body">
            {{-- Contenido de pestañas --}}
            @switch($activeTab)
                @case('detalles')
                    @include('livewire.compras.compras.tabs.detalles')
                    @break
                    
                @case('items')
                    @include('livewire.compras.compras.tabs.items')
                    @break
                    
                @case('recepciones')
                    @include('livewire.compras.compras.tabs.recepciones')
                    @break
                    
                @case('aprobaciones')
                    @include('livewire.compras.compras.tabs.aprobaciones')
                    @break
                    
                @case('trazabilidad')
                    @include('livewire.compras.compras.tabs.trazabilidad')
                    @break
                    
                @case('auditoria')
                    @include('livewire.compras.compras.tabs.auditoria')
                    @break
            @endswitch
        </div>
    </div>
</div>