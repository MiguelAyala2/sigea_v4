@extends('adminlte::page')

@section('title', 'Registrar Pago')

@section('content_header')
    <h1><i class="fas fa-dollar-sign"></i> Registrar Pago</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-file-invoice"></i>
                Información de la Cuenta por Cobrar
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Factura:</strong>
                    <p class="mb-1">
                        <a href="{{ route('ventas.facturas.show', $cuenta->factura_id) }}"
                           class="text-primary"
                           target="_blank">
                            {{ $cuenta->numero_factura }}
                            <i class="fas fa-external-link-alt fa-sm"></i>
                        </a>
                    </p>
                </div>
                <div class="col-md-3">
                    <strong>Cliente:</strong>
                    <p class="mb-1">{{ $cuenta->cliente->nombre_razon_social ?? 'N/A' }}</p>
                    @if($cuenta->cliente && $cuenta->cliente->ruc_ci)
                        <small class="text-muted">RUC/CI: {{ $cuenta->cliente->ruc_ci }}</small>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Fecha de Emisión:</strong>
                    <p class="mb-1">{{ \Carbon\Carbon::parse($cuenta->fecha_emision)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Fecha de Vencimiento:</strong>
                    <p class="mb-1">
                        {{ \Carbon\Carbon::parse($cuenta->fecha_vencimiento)->format('d/m/Y') }}
                        @php
                            $diasVencimiento = now()->diffInDays($cuenta->fecha_vencimiento, false);
                        @endphp
                        @if($diasVencimiento < 0)
                            <br><span class="badge badge-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                Vencida hace {{ abs($diasVencimiento) }} días
                            </span>
                        @elseif($diasVencimiento <= 7)
                            <br><span class="badge badge-warning">
                                <i class="fas fa-clock"></i>
                                Vence en {{ $diasVencimiento }} días
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Monto Total</span>
                            <span class="info-box-number">
                                ₲ {{ number_format($cuenta->monto_total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Monto Pagado</span>
                            <span class="info-box-number">
                                ₲ {{ number_format($cuenta->monto_pagado, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Saldo Pendiente</span>
                            <span class="info-box-number">
                                ₲ {{ number_format($cuenta->saldo_pendiente, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewire('ventas.pagar-cuenta', ['cuenta' => $cuenta])
@stop

@section('css')
    <style>
        .info-box-number {
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
@stop
