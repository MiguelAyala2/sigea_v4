<?php

namespace App\Livewire\Compras\Dashboard;

use App\Models\Compras\Compra;
use App\Models\Compras\IntegracionDocumentos;
use Livewire\Component;

class FlujoCompra extends Component
{
    public $compraId;
    public $compra;
    public $documentosRelacionados = [];

    public function mount($compraId = null)
    {
        if ($compraId) {
            $this->compraId = $compraId;
            $this->loadCompraData();
        }
    }

    public function loadCompraData()
    {
        $this->compra = Compra::with([
            'proveedor',
            'sucursal',
            'timbrado',
            'detalles.producto',
            'recepciones.deposito',
            'aprobaciones.aprobador',
            'integracionesOrigen.documentoDestino',
            'integracionesDestino.documentoOrigen',
        ])->find($this->compraId);

        if ($this->compra) {
            $this->loadDocumentosRelacionados();
        }
    }

    private function loadDocumentosRelacionados()
    {
        $this->documentosRelacionados = IntegracionDocumentos::where(function ($query) {
            $query->where('documento_origen_tipo', 'COMPRA')
                  ->where('documento_origen_id', $this->compraId);
        })->orWhere(function ($query) {
            $query->where('documento_destino_tipo', 'COMPRA')
                  ->where('documento_destino_id', $this->compraId);
        })
        ->with(['documentoOrigen', 'documentoDestino'])
        ->get()
        ->map(function ($integracion) {
            return [
                'id' => $integracion->id,
                'origen' => $integracion->documento_origen_tipo_texto,
                'origen_id' => $integracion->documento_origen_id,
                'destino' => $integracion->documento_destino_tipo_texto,
                'destino_id' => $integracion->documento_destino_id,
                'relacion' => $integracion->tipo_relacion_texto,
                'porcentaje' => $integracion->porcentaje_relacion_formateado,
                'es_completa' => $integracion->es_completa,
                'fecha' => $integracion->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    public function getEstadosFlujoProperty()
    {
        if (!$this->compra) {
            return [];
        }

        return [
            [
                'nombre' => 'Pedido de Compra',
                'estado' => $this->compra->integracionesOrigen
                    ->where('documento_destino_tipo', 'PEDIDO_COMPRA')
                    ->where('tipo_relacion', 'GENERA')
                    ->isNotEmpty() ? 'completado' : 'pendiente',
                'fecha' => $this->compra->integracionesOrigen
                    ->where('documento_destino_tipo', 'PEDIDO_COMPRA')
                    ->first()?->created_at?->format('d/m/Y'),
                'icono' => 'fas fa-clipboard-list',
                'color' => 'primary',
            ],
            [
                'nombre' => 'Presupuesto',
                'estado' => $this->compra->integracionesOrigen
                    ->where('documento_destino_tipo', 'PRESUPUESTO')
                    ->where('tipo_relacion', 'SE_CONVIERTE_EN')
                    ->isNotEmpty() ? 'completado' : 'pendiente',
                'fecha' => $this->compra->integracionesOrigen
                    ->where('documento_destino_tipo', 'PRESUPUESTO')
                    ->first()?->created_at?->format('d/m/Y'),
                'icono' => 'fas fa-file-invoice-dollar',
                'color' => 'info',
            ],
            [
                'nombre' => 'Orden de Compra',
                'estado' => $this->compra->integracionesOrigen
                    ->where('documento_destino_tipo', 'ORDEN_COMPRA')
                    ->where('tipo_relacion', 'DEPENDE_DE')
                    ->isNotEmpty() ? 'completado' : 'pendiente',
                'fecha' => $this->compra->integracionesOrigen
                    ->where('documento_destino_tipo', 'ORDEN_COMPRA')
                    ->first()?->created_at?->format('d/m/Y'),
                'icono' => 'fas fa-file-signature',
                'color' => 'warning',
            ],
            [
                'nombre' => 'Compra/Factura',
                'estado' => 'actual',
                'fecha' => $this->compra->fecha_emision->format('d/m/Y'),
                'icono' => 'fas fa-file-invoice',
                'color' => 'success',
            ],
            [
                'nombre' => 'Recepción',
                'estado' => $this->compra->recepcion_completa ? 'completado' : 'pendiente',
                'fecha' => $this->compra->recepciones->first()?->fecha_recepcion?->format('d/m/Y'),
                'icono' => 'fas fa-box-open',
                'color' => 'indigo',
            ],
            [
                'nombre' => 'Pago',
                'estado' => $this->compra->estaPagada() ? 'completado' : 'pendiente',
                'fecha' => $this->compra->integracionesDestino
                    ->where('documento_origen_tipo', 'PAGO')
                    ->first()?->created_at?->format('d/m/Y'),
                'icono' => 'fas fa-money-bill-wave',
                'color' => 'danger',
            ],
        ];
    }

    public function getProgresoTotalProperty()
    {
        $estados = $this->estadosFlujo;
        $completados = collect($estados)->where('estado', 'completado')->count();
        
        return $estados ? round(($completados / count($estados)) * 100) : 0;
    }

    public function render()
    {
        return view('livewire.compras.dashboard.flujo-compra', [
            'estadosFlujo' => $this->estadosFlujo,
            'progresoTotal' => $this->progresoTotal,
            'documentosRelacionados' => $this->documentosRelacionados,
        ]);
    }
}
