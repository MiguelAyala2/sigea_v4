@extends('adminlte::page')

@section('title', 'Cuentas a Cobrar')

@section('content_header')
    <h1><i class="fas fa-hand-holding-usd"></i> Cuentas a Cobrar</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Cuentas Pendientes de Cobro
            </h3>
        </div>
        <div class="card-body">
            @if($cuentas->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay cuentas por cobrar pendientes.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Fecha Emisión</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Pagado</th>
                                <th class="text-right">Saldo</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuentas as $cuenta)
                                @php
                                    $diasVencimiento = now()->diffInDays($cuenta->fecha_vencimiento, false);
                                    $esVencida = $diasVencimiento < 0;
                                    $porVencer = $diasVencimiento >= 0 && $diasVencimiento <= 7;
                                @endphp
                                <tr class="{{ $esVencida ? 'table-danger' : ($porVencer ? 'table-warning' : '') }}">
                                    <td>
                                        <a href="{{ route('ventas.facturas.show', $cuenta->factura_id) }}"
                                           class="text-primary"
                                           title="Ver factura">
                                            <i class="fas fa-file-invoice"></i>
                                            {{ $cuenta->numero_factura }}
                                        </a>
                                    </td>
                                    <td>{{ $cuenta->cliente->nombre_razon_social ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}</td>
                                    <td class="text-right">₲ {{ number_format($cuenta->monto_total, 0, ',', '.') }}</td>
                                    <td class="text-right">
                                        @if($cuenta->monto_pagado > 0)
                                            <span class="text-success">
                                                ₲ {{ number_format($cuenta->monto_pagado, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted">₲ 0</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <strong class="{{ $cuenta->saldo_pendiente > 0 ? 'text-danger' : 'text-success' }}">
                                            ₲ {{ number_format($cuenta->saldo_pendiente, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($cuenta->fecha_vencimiento)->format('d/m/Y') }}
                                        @if($esVencida)
                                            <br>
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Vencida hace {{ abs($diasVencimiento) }} días
                                            </small>
                                        @elseif($porVencer)
                                            <br>
                                            <small class="text-warning">
                                                <i class="fas fa-clock"></i>
                                                Vence en {{ $diasVencimiento }} días
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cuenta->estado === 'PENDIENTE')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i> Pendiente
                                            </span>
                                        @elseif($cuenta->estado === 'PARCIALMENTE_PAGADA')
                                            <span class="badge badge-info">
                                                <i class="fas fa-coins"></i> Parcial
                                            </span>
                                        @elseif($cuenta->estado === 'PAGADA')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Pagada
                                            </span>
                                        @elseif($cuenta->estado === 'VENCIDA')
                                            <span class="badge badge-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Vencida
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($cuenta->estado !== 'PAGADA')
                                            <a href="{{ route('ventas.cuentas-cobrar.cobrar', $cuenta->id) }}"
                                               class="btn btn-sm btn-success"
                                               title="Registrar pago">
                                                <i class="fas fa-dollar-sign"></i> Cobrar
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-check-circle"></i> Completada
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary font-weight-bold">
                                <td colspan="3" class="text-right">TOTALES:</td>
                                <td class="text-right">₲ {{ number_format($cuentas->sum('monto_total'), 0, ',', '.') }}</td>
                                <td class="text-right">₲ {{ number_format($cuentas->sum('monto_pagado'), 0, ',', '.') }}</td>
                                <td class="text-right text-danger">₲ {{ number_format($cuentas->sum('saldo_pendiente'), 0, ',', '.') }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Resumen estadístico --}}
    @if(!$cuentas->isEmpty())
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $cuentas->where('estado', 'PENDIENTE')->count() }}</h3>
                        <p>Cuentas Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $cuentas->where('estado', 'PARCIALMENTE_PAGADA')->count() }}</h3>
                        <p>Pagos Parciales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $cuentas->filter(fn($c) => now()->diffInDays($c->fecha_vencimiento, false) < 0)->count() }}</h3>
                        <p>Facturas Vencidas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>₲ {{ number_format($cuentas->sum('saldo_pendiente'), 0, ',', '.') }}</h3>
                        <p>Total a Cobrar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('css')
    <style>
        .table-sm td, .table-sm th {
            font-size: 0.875rem;
        }
    </style>
@stop
