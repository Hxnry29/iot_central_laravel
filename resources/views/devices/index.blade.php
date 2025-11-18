@extends('layouts.app')

@section('title', 'Dispositivos')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-hdd-network me-2 text-primary"></i>Gestión de Dispositivos</h3>
        <a href="{{ route('devices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo dispositivo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($devices->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-cpu display-4 d-block mb-3"></i>
            <h5>No hay dispositivos registrados</h5>
            <p>Puedes crear tu primer dispositivo con el botón “Nuevo dispositivo”.</p>
        </div>
    @else
        <div class="row">
            @foreach($devices as $device)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0 fw-semibold text-primary">
                                    {{ $device->name ?? 'Sin nombre' }}
                                </h5>
                                <span class="badge 
                                    @if($device->status == 'online') bg-success
                                    @elseif($device->status == 'offline') bg-danger
                                    @else bg-secondary @endif
                                ">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </div>

                            <small class="text-muted d-block mb-2">
                                ID: <code>{{ $device->device_id }}</code>
                            </small>

                            <p class="mb-2">
                                <strong>Tipo:</strong> {{ ucfirst($device->kind) }}
                            </p>

                            <p class="text-muted small">
                                Último dato: 
                                @if($device->datas->count())
                                    <strong>{{ $device->datas->first()->type }}</strong> = 
                                    {{ $device->datas->first()->value }} {{ $device->datas->first()->unit }}
                                @else
                                    <em>Sin datos</em>
                                @endif
                            </p>
                        </div>

                        <div class="card-footer  d-flex justify-content-between">
                            <a href="{{ route('devices.show', $device) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ route('devices.edit', $device) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="{{ route('devices.data', $device->id) }}" class="btn btn-info btn-sm">
                             Ver datos
                            </a>

                            <form action="{{ route('devices.destroy', $device) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este dispositivo?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
