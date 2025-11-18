@extends('layouts.app')

@section('title', 'Crear Dispositivo')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Nuevo Dispositivo</h5>
            <a href="{{ route('devices.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('devices.store') }}" method="POST">
                @csrf

                {{-- Información general --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="device_id" class="form-label">ID del Dispositivo</label>
                        <input type="text" name="device_id" id="device_id" class="form-control" 
                               placeholder="Ej: sensor_001" required value="{{ old('device_id') }}">
                        @error('device_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control"
                               placeholder="Ej: Sensor de temperatura" required value="{{ old('name') }}">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kind" class="form-label">Tipo</label>
                        <select name="kind" id="kind" class="form-select">
                            <option value="real" {{ old('kind') == 'real' ? 'selected' : '' }}>Real</option>
                            <option value="api" {{ old('kind') == 'api' ? 'selected' : '' }}>API</option>
                            <option value="digital_twin" {{ old('kind') == 'digital_twin' ? 'selected' : '' }}>Digital Twin</option>
                            <option value="dataset" {{ old('kind') == 'dataset' ? 'selected' : '' }}>Dataset</option>
                            <option value="virtual" {{ old('kind') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                        </select>
                        @error('kind')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select name="status" id="status" class="form-select">
                            <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="stopped" {{ old('status') == 'stopped' ? 'selected' : '' }}>Stopped</option>
                        </select>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Parámetros del dispositivo --}}
                <hr>
                <h6 class="text-secondary mb-3"><i class="bi bi-sliders me-2"></i>Parámetros del dispositivo</h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="min_value" class="form-label">Valor mínimo</label>
                        <input type="number" step="any" name="min_value" id="min_value" class="form-control"
                               placeholder="Ej: 0" value="{{ old('min_value') }}">
                        @error('min_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="max_value" class="form-label">Valor máximo</label>
                        <input type="number" step="any" name="max_value" id="max_value" class="form-control"
                               placeholder="Ej: 100" value="{{ old('max_value') }}">
                        @error('max_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="unit" class="form-label">Unidad de medida</label>
                        <input type="text" name="unit" id="unit" class="form-control"
                               placeholder="Ej: °C, %, Pa, etc." value="{{ old('unit') }}">
                        @error('unit')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
