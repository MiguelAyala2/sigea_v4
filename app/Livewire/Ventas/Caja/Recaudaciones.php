<?php

namespace App\Livewire\Ventas\Caja;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ventas\RecaudacionADepositar;
use Illuminate\Support\Facades\DB;

class Recaudaciones extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Para marcar como depositado
    public $recaudacion_id;
    public $comprobante_deposito;
    public $fecha_real_deposito;

    // Filtros
    public $filtro_estado = '';
    public $filtro_tipo = '';
    public $fecha_desde;
    public $fecha_hasta;

    // Modalidades
    public $showDepositoModal = false;

    public function mount()
    {
        $this->fecha_desde = now()->subDays(30)->toDateString();
        $this->fecha_hasta = now()->toDateString();
        $this->fecha_real_deposito = now()->toDateString();
    }

    public function updatedFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatedFiltroTipo()
    {
        $this->resetPage();
    }

    public function abrirModalDeposito($recaudacionId)
    {
        $this->recaudacion_id = $recaudacionId;
        $this->comprobante_deposito = '';
        $this->fecha_real_deposito = now()->toDateString();
        $this->showDepositoModal = true;
    }

    public function cerrarModalDeposito()
    {
        $this->showDepositoModal = false;
        $this->resetValidation();
    }

    public function marcarComoDepositado()
    {
        $this->validate([
            'comprobante_deposito' => 'required|string|max:100',
            'fecha_real_deposito' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $recaudacion = RecaudacionADepositar::findOrFail($this->recaudacion_id);

            if ($recaudacion->estado !== 'PENDIENTE') {
                session()->flash('error', 'Esta recaudación ya no está en estado pendiente.');
                $this->cerrarModalDeposito();
                return;
            }

            $recaudacion->update([
                'estado' => 'DEPOSITADO',
                'fecha_real_deposito' => $this->fecha_real_deposito,
                'comprobante_deposito' => $this->comprobante_deposito,
                'usuario_deposita_id' => auth()->id(),
            ]);

            DB::commit();

            session()->flash('success', 'Recaudación marcada como depositada exitosamente.');

            $this->cerrarModalDeposito();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al marcar como depositado: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = RecaudacionADepositar::with([
                'cierreCaja.aperturaCaja.puntoExpedicion',
                'usuarioRegistra',
                'usuarioDeposita'
            ])
            ->where('activo', true);

        if ($this->filtro_estado) {
            $query->where('estado', $this->filtro_estado);
        }

        if ($this->filtro_tipo) {
            $query->where('tipo_recaudacion', $this->filtro_tipo);
        }

        if ($this->fecha_desde) {
            $query->whereDate('fecha_recaudacion', '>=', $this->fecha_desde);
        }

        if ($this->fecha_hasta) {
            $query->whereDate('fecha_recaudacion', '<=', $this->fecha_hasta);
        }

        $recaudaciones = $query->orderBy('fecha_prevista_deposito', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calcular totales
        $total_pendiente = RecaudacionADepositar::where('estado', 'PENDIENTE')
            ->where('activo', true)
            ->sum('monto');

        $total_depositado_hoy = RecaudacionADepositar::where('estado', 'DEPOSITADO')
            ->where('activo', true)
            ->whereDate('fecha_real_deposito', now())
            ->sum('monto');

        $vencidas = RecaudacionADepositar::where('estado', 'PENDIENTE')
            ->where('activo', true)
            ->where('fecha_prevista_deposito', '<', now())
            ->count();

        return view('livewire.ventas.caja.recaudaciones', [
            'recaudaciones' => $recaudaciones,
            'total_pendiente' => $total_pendiente,
            'total_depositado_hoy' => $total_depositado_hoy,
            'vencidas' => $vencidas,
        ]);
    }
}
