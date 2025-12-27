@extends('adminlte::page')

@section('title', 'Editar Reclamo')

@section('content_header')
    <h1><i class="fas fa-edit"></i> Editar Reclamo: {{ $reclamo->codigo }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h3 class="card-title">Editar Datos del Reclamo</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('servicios.reclamos.update', $reclamo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente *</label>
                            <select name="cliente_id" class="form-control @error('cliente_id') is-invalid @enderror" required>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id', $reclamo->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} - {{ $cliente->documento }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Servicio Relacionado (Opcional)</label>
                            <select name="orden_servicio_id" class="form-control">
                                <option value="">Ninguno</option>
                                @foreach($ordenes as $orden)
                                    <option value="{{ $orden->id }}" {{ old('orden_servicio_id', $reclamo->orden_servicio_id) == $orden->id ? 'selected' : '' }}>
                                        {{ $orden->codigo }} - {{ $orden->equipo }} ({{ $orden->fecha_orden->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de Reclamo *</label>
                            <select name="tipo_reclamo" class="form-control @error('tipo_reclamo') is-invalid @enderror" required>
                                <option value="calidad_servicio" {{ old('tipo_reclamo', $reclamo->tipo_reclamo) == 'calidad_servicio' ? 'selected' : '' }}>Calidad del Servicio</option>
                                <option value="demora_entrega" {{ old('tipo_reclamo', $reclamo->tipo_reclamo) == 'demora_entrega' ? 'selected' : '' }}>Demora en Entrega</option>
                                <option value="falla_post_servicio" {{ old('tipo_reclamo', $reclamo->tipo_reclamo) == 'falla_post_servicio' ? 'selected' : '' }}>Falla Post-Servicio</option>
                                <option value="atencion_cliente" {{ old('tipo_reclamo', $reclamo->tipo_reclamo) == 'atencion_cliente' ? 'selected' : '' }}>Atención al Cliente</option>
                                <option value="costo_facturacion" {{ old('tipo_reclamo', $reclamo->tipo_reclamo) == 'costo_facturacion' ? 'selected' : '' }}>Costo/Facturación</option>
                                <option value="otro" {{ old('tipo_reclamo', $reclamo->tipo_reclamo) == 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('tipo_reclamo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Prioridad *</label>
                            <select name="prioridad" class="form-control @error('prioridad') is-invalid @enderror" required>
                                <option value="baja" {{ old('prioridad', $reclamo->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ old('prioridad', $reclamo->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ old('prioridad', $reclamo->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="urgente" {{ old('prioridad', $reclamo->prioridad) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('prioridad') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado *</label>
                            <select name="estado" class="form-control @error('estado') is-invalid @enderror" required>
                                <option value="pendiente" {{ old('estado', $reclamo->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_revision" {{ old('estado', $reclamo->estado) == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                <option value="en_proceso" {{ old('estado', $reclamo->estado) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="resuelto" {{ old('estado', $reclamo->estado) == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                                <option value="cerrado" {{ old('estado', $reclamo->estado) == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                                <option value="rechazado" {{ old('estado', $reclamo->estado) == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                            </select>
                            @error('estado') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha del Reclamo *</label>
                            <input type="date" name="fecha_reclamo" class="form-control @error('fecha_reclamo') is-invalid @enderror" value="{{ old('fecha_reclamo', $reclamo->fecha_reclamo->format('Y-m-d')) }}" required>
                            @error('fecha_reclamo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Canal de Recepción *</label>
                            <select name="canal_recepcion" class="form-control @error('canal_recepcion') is-invalid @enderror" required>
                                <option value="presencial" {{ old('canal_recepcion', $reclamo->canal_recepcion) == 'presencial' ? 'selected' : '' }}>Presencial</option>
                                <option value="telefono" {{ old('canal_recepcion', $reclamo->canal_recepcion) == 'telefono' ? 'selected' : '' }}>Teléfono</option>
                                <option value="email" {{ old('canal_recepcion', $reclamo->canal_recepcion) == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="whatsapp" {{ old('canal_recepcion', $reclamo->canal_recepcion) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="web" {{ old('canal_recepcion', $reclamo->canal_recepcion) == 'web' ? 'selected' : '' }}>Web</option>
                            </select>
                            @error('canal_recepcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descripción del Reclamo *</label>
                            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4" required>{{ old('descripcion', $reclamo->descripcion) }}</textarea>
                            @error('descripcion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Responsable Asignado (Opcional)</label>
                            <select name="responsable_id" class="form-control">
                                <option value="">Sin asignar</option>
                                @foreach($responsables as $responsable)
                                    <option value="{{ $responsable->id }}" {{ old('responsable_id', $reclamo->responsable_id) == $responsable->id ? 'selected' : '' }}>
                                        {{ $responsable->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Solución (Opcional)</label>
                            <textarea name="solucion" class="form-control @error('solucion') is-invalid @enderror" rows="4" placeholder="Describa la solución aplicada al reclamo">{{ old('solucion', $reclamo->solucion) }}</textarea>
                            @error('solucion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Resolución (Opcional)</label>
                            <input type="datetime-local" name="fecha_resolucion" class="form-control @error('fecha_resolucion') is-invalid @enderror" value="{{ old('fecha_resolucion', $reclamo->fecha_resolucion?->format('Y-m-d\TH:i')) }}">
                            @error('fecha_resolucion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha de Cierre (Opcional)</label>
                            <input type="datetime-local" name="fecha_cierre" class="form-control @error('fecha_cierre') is-invalid @enderror" value="{{ old('fecha_cierre', $reclamo->fecha_cierre?->format('Y-m-d\TH:i')) }}">
                            @error('fecha_cierre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-danger btn-lg">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="{{ route('servicios.reclamos.seguimiento') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
@stop
