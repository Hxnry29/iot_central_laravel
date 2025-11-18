@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Panel de Dispositivos</h2>
        <span class="text-muted">Última actualización: {{ now()->format('d/m/Y H:i') }}</span>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="bi bi-cpu" style="font-size: 2.2rem; color: #0d6efd;"></i>
                    <h6 class="mt-2 text-muted">Total Dispositivos</h6>
                    <h2 class="fw-bold">{{ $totalDevices ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="bi bi-wifi" style="font-size: 2.2rem; color: #198754;"></i>
                    <h6 class="mt-2 text-muted">Activos</h6>
                    <h2 class="fw-bold">{{ $activeDevices ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="bi bi-exclamation-octagon" style="font-size: 2.2rem; color: #dc3545;"></i>
                    <h6 class="mt-2 text-muted">Inactivos</h6>
                    <h2 class="fw-bold">{{ $inactiveDevices ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de dispositivos --}}
    <h4 class="fw-semibold mb-3">Dispositivos Registrados</h4>

    <div class="row">
        @forelse($devices as $device)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 device-card">
                    <div class="card-body">

                        {{-- Título + Estado --}}
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

                        {{-- ID --}}
                        <small class="text-muted d-block mb-2">
                            ID: <code>{{ $device->device_id }}</code>
                        </small>

                        {{-- Tipo --}}
                        <p class="mb-2">
                            <strong>Tipo:</strong> {{ ucfirst($device->kind) }}
                        </p>

                        {{-- Último dato --}}
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
                </div>
            </div>
        @empty
            <p class="text-muted">No hay dispositivos registrados.</p>
        @endforelse
    </div>

</div>

{{-- Efecto hover opcional --}}
<style>
.device-card {
    transition: transform .15s ease, box-shadow .15s ease;
}
.device-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
}
</style>

@endsection
