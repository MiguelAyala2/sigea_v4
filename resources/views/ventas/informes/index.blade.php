@extends('adminlte::page')
@section('title', 'Informes de Ventas')
@section('content_header')<h1><i class="fas fa-chart-line"></i> Informes de Ventas</h1>@stop
@section('content')
<div class="row">
<div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>₲ 45.820.000</h3><p>Ventas del Mes</p></div><div class="icon"><i class="fas fa-shopping-cart"></i></div></div></div>
<div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>₲ 38.650.000</h3><p>Total Cobrado</p></div><div class="icon"><i class="fas fa-money-bill-wave"></i></div></div></div>
<div class="col-lg-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3>₲ 7.170.000</h3><p>Pendiente Cobro</p></div><div class="icon"><i class="fas fa-clock"></i></div></div></div>
<div class="col-lg-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3>248</h3><p>Facturas Emitidas</p></div><div class="icon"><i class="fas fa-file-invoice"></i></div></div></div>
</div>

<div class="row">
<div class="col-md-6">
<div class="card"><div class="card-header bg-primary"><h3 class="card-title">Ventas por Mes</h3></div><div class="card-body"><div style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f4f6f9;"><i class="fas fa-chart-bar fa-3x text-muted"></i><p class="ml-3 text-muted">Gráfico de barras - Ventas mensuales</p></div></div></div>
</div>
<div class="col-md-6">
<div class="card"><div class="card-header bg-success"><h3 class="card-title">Cobranzas por Forma de Pago</h3></div><div class="card-body"><div style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f4f6f9;"><i class="fas fa-chart-pie fa-3x text-muted"></i><p class="ml-3 text-muted">Gráfico circular - Métodos de pago</p></div></div></div>
</div>
</div>

<div class="card"><div class="card-header bg-secondary text-white"><h3 class="card-title">Reportes Disponibles</h3></div><div class="card-body">
<div class="row">
<div class="col-md-4"><div class="info-box bg-gradient-info"><span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span><div class="info-box-content"><span class="info-box-text">Reporte de Ventas</span><span class="info-box-number">Por período</span><button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-pdf"></i> PDF</button> <button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-excel"></i> Excel</button></div></div></div>
<div class="col-md-4"><div class="info-box bg-gradient-success"><span class="info-box-icon"><i class="fas fa-users"></i></span><div class="info-box-content"><span class="info-box-text">Ventas por Cliente</span><span class="info-box-number">Ranking</span><button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-pdf"></i> PDF</button> <button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-excel"></i> Excel</button></div></div></div>
<div class="col-md-4"><div class="info-box bg-gradient-warning"><span class="info-box-icon"><i class="fas fa-boxes"></i></span><div class="info-box-content"><span class="info-box-text">Productos Más Vendidos</span><span class="info-box-number">Top 10</span><button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-pdf"></i> PDF</button> <button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-excel"></i> Excel</button></div></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="info-box bg-gradient-danger"><span class="info-box-icon"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Cuentas por Cobrar</span><span class="info-box-number">Vencidas/Vigentes</span><button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-pdf"></i> PDF</button> <button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-excel"></i> Excel</button></div></div></div>
<div class="col-md-4"><div class="info-box bg-gradient-primary"><span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span><div class="info-box-content"><span class="info-box-text">Cobranzas Realizadas</span><span class="info-box-number">Por período</span><button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-pdf"></i> PDF</button> <button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-excel"></i> Excel</button></div></div></div>
<div class="col-md-4"><div class="info-box bg-gradient-secondary"><span class="info-box-icon"><i class="fas fa-cash-register"></i></span><div class="info-box-content"><span class="info-box-text">Movimientos de Caja</span><span class="info-box-number">Histórico</span><button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-pdf"></i> PDF</button> <button class="btn btn-sm btn-light mt-2"><i class="fas fa-file-excel"></i> Excel</button></div></div></div>
</div>
</div></div>

<div class="card"><div class="card-header bg-info text-white"><h3 class="card-title">Generar Reporte Personalizado</h3></div><div class="card-body">
<div class="row">
<div class="col-md-3"><label>Tipo de Reporte</label><select class="form-control"><option>Ventas Generales</option><option>Ventas por Cliente</option><option>Ventas por Producto</option><option>Cobranzas</option><option>Cuentas por Cobrar</option><option>Libro de Ventas IVA</option></select></div>
<div class="col-md-3"><label>Fecha Desde</label><input type="date" class="form-control" value="2025-12-01"></div>
<div class="col-md-3"><label>Fecha Hasta</label><input type="date" class="form-control" value="2025-12-16"></div>
<div class="col-md-3"><label>&nbsp;</label><button class="btn btn-primary btn-block"><i class="fas fa-play"></i> Generar</button></div>
</div>
</div></div>

<div class="alert alert-info"><i class="fas fa-info-circle"></i> <strong>Plantilla Visual de Ejemplo</strong></div>
@stop
