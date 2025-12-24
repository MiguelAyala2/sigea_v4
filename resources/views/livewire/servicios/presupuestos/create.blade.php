<div>
    @if (session()->has('success'))
        <x-adminlte-alert theme="success" title="Éxito" dismissible>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session()->has('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissible>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    @if($presupuestoGuardado)
        <div id="header-buttons-container" class="mb-3">
            <button type="button" class="btn btn-info" onclick="imprimirPresupuesto()">
                <i class="fas fa-print"></i> Imprimir PDF
            </button>
            <a href="{{ route('servicios.presupuestos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    @endif

    <x-adminlte-card theme="primary" title="Nuevo Presupuesto" icon="fas fa-plus">
        <form wire:submit.prevent="guardar">
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-input name="fecha_presupuesto" label="Fecha de Presupuesto *" type="date"
                        wire:model="fecha_presupuesto">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="buscarCliente">Buscar Diagnóstico</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control"
                                   id="buscarCliente" wire:model.live="buscarCliente"
                                   placeholder="Busque por código de diagnóstico o nombre del cliente" autocomplete="off">
                        </div>

                        @if(count($diagnosticos) > 0)
                            <div class="list-group mt-1" style="position: absolute; z-index: 1000; width: 48%;">
                                @foreach($diagnosticos as $diag)
                                    <button type="button" class="list-group-item list-group-item-action"
                                            wire:click="seleccionarDiagnostico({{ $diag->id }})">
                                        <strong>{{ $diag->codigo }}</strong> - {{ $diag->recepcion->solicitud->cliente->nombre }}
                                        <br>
                                        <small class="text-muted">{{ $diag->recepcion->producto->nombre ?? 'N/A' }}</small>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($diagnosticoSeleccionado)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Información del Diagnóstico</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p class="mb-2"><strong>N° Diagnóstico:</strong><br>{{ $diagnostico_codigo }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>N° Solicitud:</strong><br>{{ $solicitud_codigo }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Cliente:</strong><br>{{ $cliente_nombre }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Equipo:</strong><br>{{ $equipo_nombre }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Problema Detectado:</strong><br>{{ $problema_detectado }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-adminlte-select name="promocion_id" label="Promoción (opcional)" wire:model.live="promocion_id">
                            <option value="">Sin promoción</option>
                            @foreach($promociones as $promo)
                                <option value="{{ $promo->id }}">
                                    {{ $promo->codigo }} - {{ $promo->nombre }} ({{ $promo->descuento }}%)
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>

                    <div class="col-md-6">
                        <x-adminlte-select name="descuento_id" label="Descuento (opcional)" wire:model.live="descuento_id">
                            <option value="">Sin descuento</option>
                            @foreach($descuentos as $desc)
                                <option value="{{ $desc->id }}">
                                    {{ $desc->codigo }} - {{ $desc->descripcion }} ({{ $desc->valor_formateado }})
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Detalle del Presupuesto</h5>
                    </div>
                    <div class="card-body">
                        {{-- Tipos de Servicio --}}
                        <h6 class="mb-2"><strong>Tipos de Servicio</strong></h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Tipo de Servicio</th>
                                        <th class="text-center" width="100">Cantidad</th>
                                        <th class="text-right" width="150">Precio Unitario</th>
                                        <th class="text-right" width="150">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $diagnostico = \App\Models\Servicios\Diagnostico::with('tiposServicio.tipoServicio')->find($diagnosticoSeleccionado);
                                    @endphp
                                    @if($diagnostico && $diagnostico->tiposServicio->count() > 0)
                                        @foreach($diagnostico->tiposServicio as $ts)
                                            <tr>
                                                <td>{{ $ts->tipoServicio->codigo ?? 'N/A' }}</td>
                                                <td>{{ $ts->tipoServicio->descripcion ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $ts->cantidad }}</td>
                                                <td class="text-right">₲ {{ number_format($ts->costo_unitario, 0, ',', '.') }}</td>
                                                <td class="text-right">₲ {{ number_format($ts->cantidad * $ts->costo_unitario, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No hay tipos de servicio registrados</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="4" class="text-right">SUBTOTAL SERVICIOS:</td>
                                        <td class="text-right">₲ {{ number_format($subtotal_servicios, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($descuento_promocion > 0)
                                    <tr>
                                        <td colspan="4" class="text-right">Descuento Promoción:</td>
                                        <td class="text-right text-danger">- ₲ {{ number_format($descuento_promocion, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="font-weight-bold">
                                        <td colspan="4" class="text-right">TOTAL SERVICIOS:</td>
                                        <td class="text-right">₲ {{ number_format($total_servicios, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Repuestos Necesarios --}}
                        <h6 class="mb-2 mt-4"><strong>Repuestos Necesarios</strong></h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Repuesto</th>
                                        <th class="text-center" width="100">Cantidad</th>
                                        <th class="text-right" width="150">Precio Unitario</th>
                                        <th class="text-right" width="150">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $repuestos = \App\Models\Servicios\DiagnosticoRepuesto::with('producto')
                                            ->where('diagnostico_id', $diagnosticoSeleccionado)
                                            ->get();
                                    @endphp
                                    @if($repuestos->count() > 0)
                                        @foreach($repuestos as $rep)
                                            <tr>
                                                <td>{{ $rep->producto->codigo ?? 'N/A' }}</td>
                                                <td>{{ $rep->producto->nombre ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $rep->cantidad }}</td>
                                                <td class="text-right">₲ {{ number_format($rep->costo, 0, ',', '.') }}</td>
                                                <td class="text-right">₲ {{ number_format($rep->cantidad * $rep->costo, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No hay repuestos registrados</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="4" class="text-right">SUBTOTAL REPUESTOS:</td>
                                        <td class="text-right">₲ {{ number_format($subtotal_repuestos, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($descuento_descuento > 0)
                                    <tr>
                                        <td colspan="4" class="text-right">Descuento Aplicado:</td>
                                        <td class="text-right text-danger">- ₲ {{ number_format($descuento_descuento, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr class="font-weight-bold">
                                        <td colspan="4" class="text-right">TOTAL REPUESTOS:</td>
                                        <td class="text-right">₲ {{ number_format($total_repuestos, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Total General --}}
                        <div class="border-top border-dark pt-2">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-right"><h5 class="mb-0"><strong>TOTAL GENERAL:</strong></h5></td>
                                    <td class="text-right" width="150"><h5 class="mb-0"><strong>₲ {{ number_format($monto_total, 0, ',', '.') }}</strong></h5></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Condiciones del Presupuesto --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0"><strong>Condiciones</strong></h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0" style="list-style: none; padding-left: 0;">
                            <li class="mb-1">• Importes expresados en guaraníes – IVA incluido.</li>
                            <li class="mb-1">• Presupuesto válido por 7 días.</li>
                            <li class="mb-1">• La ejecución del servicio queda sujeta a aprobación del cliente.</li>
                            <li class="mb-0">• No incluye trabajos adicionales no contemplados en el diagnóstico inicial.</li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <x-adminlte-textarea name="observaciones" label="Observaciones (opcional)"
                            wire:model="observaciones" rows="2"></x-adminlte-textarea>
                    </div>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-12">
                    @if($diagnosticoSeleccionado && !$presupuestoGuardado)
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Generar Presupuesto
                        </button>
                    @endif
                    @if(!$presupuestoGuardado)
                        <a href="{{ route('servicios.presupuestos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </x-adminlte-card>
</div>

@push('js')
<script>
// Mover botones al header cuando se cargue el componente
document.addEventListener('DOMContentLoaded', function() {
    moveButtonsToHeader();
});

// También después de actualizaciones de Livewire
document.addEventListener('livewire:update', function() {
    moveButtonsToHeader();
});

function moveButtonsToHeader() {
    const buttonsContainer = document.getElementById('header-buttons-container');
    const headerStack = document.querySelector('[data-header-buttons]');

    if (buttonsContainer && headerStack) {
        // Mover los botones al header
        headerStack.innerHTML = '';
        headerStack.appendChild(buttonsContainer);
        buttonsContainer.classList.remove('mb-3');
    }
}

async function imprimirPresupuesto() {
    // Mostrar mensaje de carga
    const btnImprimir = event.target.closest('button');
    const textoOriginal = btnImprimir.innerHTML;
    btnImprimir.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
    btnImprimir.disabled = true;

    try {
        // Crear elemento temporal para el PDF
        const elemento = document.createElement('div');
        elemento.style.width = '210mm'; // Ancho A4
        elemento.style.padding = '15mm 10mm';
        elemento.style.backgroundColor = 'white';
        elemento.style.position = 'absolute';
        elemento.style.left = '-9999px';

        // Obtener datos
        const codigo = document.querySelector('[wire\\:model="codigo"]')?.value || 'N/A';
        const fecha = document.querySelector('[wire\\:model="fecha_presupuesto"]')?.value || new Date().toLocaleDateString('es-PY');
        const cliente = document.querySelector('.card-body .row:first-child .col-md-6:nth-child(3) p')?.textContent.split(':')[1]?.trim() || 'N/A';
        const equipo = document.querySelector('.card-body .row:nth-child(2) .col-md-6:first-child p')?.textContent.split(':')[1]?.trim() || 'N/A';
        const problema = document.querySelector('.card-body .row:nth-child(2) .col-md-6:last-child p')?.textContent.split(':')[1]?.trim() || 'N/A';
        const totalGeneral = document.querySelector('.border-top h5 strong')?.textContent || '₲ 0';

        // Obtener fecha y hora actual
        const ahora = new Date();
        const fechaHora = ahora.toLocaleString('es-PY', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).replace(',', '');

        // Generar HTML del PDF
        elemento.innerHTML = `
            <div style="font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.3; color: #000; border: 2px solid #000; padding: 10mm; min-height: 277mm;">
                <!-- Encabezado con logos -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; padding-bottom: 8px;">
                    <div style="width: 60px; height: 60px;">
                        <svg viewBox="0 0 100 100" style="width: 100%; height: 100%;">
                            <circle cx="50" cy="50" r="45" fill="#1e88e5"/>
                            <path d="M 30 60 Q 50 30 70 60" stroke="white" stroke-width="6" fill="none"/>
                            <circle cx="50" cy="65" r="8" fill="white"/>
                        </svg>
                    </div>
                    <div style="text-align: center; flex: 1;">
                        <h1 style="font-size: 12pt; margin: 0; font-weight: bold;">SIGEA S.A. - AGUATERÍA Y PLOMERÍA</h1>
                        <p style="font-size: 7.5pt; margin: 2px 0;">ventas@aguateriasigea.com.py | aguateriasigea.com.py | Contacto: 021 555-124 | Av. Eusebio Ayala Km 4.5</p>
                        <p style="font-size: 7pt; margin: 2px 0;">Generado el: ${fechaHora} Hs</p>
                    </div>
                    <div style="width: 60px; height: 60px;">
                        <svg viewBox="0 0 100 100" style="width: 100%; height: 100%;">
                            <circle cx="50" cy="50" r="45" fill="#1e88e5"/>
                            <path d="M 30 60 Q 50 30 70 60" stroke="white" stroke-width="6" fill="none"/>
                            <circle cx="50" cy="65" r="8" fill="white"/>
                        </svg>
                    </div>
                </div>

                <!-- Título del documento -->
                <div style="text-align: center; background-color: #f0f0f0; padding: 8px; margin-bottom: 10px; border: 1px solid #000;">
                    <h2 style="font-size: 11pt; margin: 0; font-weight: bold;">Presupuesto de Servicio Técnico</h2>
                </div>

                <div style="margin: 8px 0; padding: 5px; background-color: #f5f5f5;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; padding: 4px; font-size: 8.5pt;">
                                <strong style="font-size: 7.5pt; color: #666;">N° Presupuesto:</strong><br>${codigo}
                            </td>
                            <td style="width: 50%; padding: 4px; font-size: 8.5pt;">
                                <strong style="font-size: 7.5pt; color: #666;">Fecha:</strong><br>${fecha}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; font-size: 8.5pt;">
                                <strong style="font-size: 7.5pt; color: #666;">Cliente:</strong><br>${cliente}
                            </td>
                            <td style="padding: 4px; font-size: 8.5pt;">
                                <strong style="font-size: 7.5pt; color: #666;">Equipo:</strong><br>${equipo}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 4px; font-size: 8.5pt;">
                                <strong style="font-size: 7.5pt; color: #666;">Problema Detectado:</strong><br>${problema}
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="font-size: 10pt; font-weight: bold; margin: 8px 0 4px 0; padding-bottom: 2px; border-bottom: 1px solid #333;">
                    Tipos de Servicio
                </div>

                ${document.querySelector('.table-responsive table')?.outerHTML || '<p>No hay servicios</p>'}

                <div style="font-size: 10pt; font-weight: bold; margin: 8px 0 4px 0; padding-bottom: 2px; border-bottom: 1px solid #333;">
                    Repuestos Necesarios
                </div>

                ${document.querySelector('.table-responsive:nth-child(2) table')?.outerHTML || '<p>No hay repuestos</p>'}

                <div style="margin: 10px 0; padding: 6px; background-color: #f0f0f0; border: 1px solid #000;">
                    <div style="display: flex; justify-content: space-between; font-size: 11pt; font-weight: bold; border-top: 2px solid #000; padding-top: 5px; margin-top: 5px;">
                        <span>TOTAL GENERAL:</span>
                        <span>${totalGeneral}</span>
                    </div>
                </div>

                <div style="margin: 8px 0; font-size: 7.5pt; line-height: 1.4;">
                    <strong style="font-size: 9pt;">Condiciones:</strong>
                    <ul style="list-style: none; padding-left: 0; margin: 4px 0;">
                        <li>• Importes expresados en guaraníes – IVA incluido.</li>
                        <li>• Presupuesto válido por 7 días.</li>
                        <li>• La ejecución del servicio queda sujeta a aprobación del cliente.</li>
                        <li>• No incluye trabajos adicionales no contemplados en el diagnóstico inicial.</li>
                    </ul>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: space-around;">
                    <div style="text-align: center; width: 40%;">
                        <div style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-size: 8pt;">
                            <strong>Firma del Cliente</strong><br>
                            <small>Nombre y CI</small>
                        </div>
                    </div>
                    <div style="text-align: center; width: 40%;">
                        <div style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-size: 8pt;">
                            <strong>Firma Autorizada</strong><br>
                            <small>SIGEA S.A.</small>
                        </div>
                    </div>
                </div>

                <!-- Pie de página -->
                <div style="position: absolute; bottom: 5mm; right: 10mm; font-size: 7pt; text-align: right;">
                    Emitido por: Sistema SIGEA
                </div>
            </div>
        `;

        document.body.appendChild(elemento);

        // Generar PDF usando html2canvas y jsPDF
        const canvas = await html2canvas(elemento, {
            scale: 2,
            useCORS: true,
            logging: false,
            width: elemento.scrollWidth,
            height: elemento.scrollHeight
        });

        document.body.removeChild(elemento);

        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');

        const imgWidth = 210; // A4 width in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
        pdf.save(`Presupuesto_${codigo}_${fecha}.pdf`);

    } catch (error) {
        console.error('Error al generar PDF:', error);
        alert('Error al generar el PDF. Por favor, intente nuevamente.');
    } finally {
        // Restaurar botón
        btnImprimir.innerHTML = textoOriginal;
        btnImprimir.disabled = false;
    }
}
</script>
@endpush
