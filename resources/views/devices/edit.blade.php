@extends('layouts.app')

@section('title', 'Editar Dispositivo')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="bi bi-pencil-square me-2 text-warning"></i>Editar Dispositivo
        </h3>
        <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('devices.update', $device) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ID del dispositivo (no editable) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Device ID</label>
                    <input type="text" class="form-control" value="{{ $device->device_id }}" disabled>
                    <small class="text-muted">El identificador no puede modificarse.</small>
                </div>

                {{-- Nombre --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $device->name) }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Tipo --}}
                <div class="mb-3">
                    <label for="kind" class="form-label fw-semibold">Tipo</label>
                    <select name="kind" id="kind" class="form-select">
                        @foreach(['real','api','digital_twin','dataset','virtual'] as $kind)
                            <option value="{{ $kind }}" @selected($device->kind == $kind)>
                                {{ ucfirst(str_replace('_',' ',$kind)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('kind')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Estado --}}
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Estado</label>
                    <select name="status" id="status" class="form-select">
                        @foreach(['online','offline','stopped'] as $st)
                            <option value="{{ $st }}" @selected($device->status == $st)>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Parámetros del dispositivo --}}
                <hr>
                <h5 class="fw-semibold text-primary mb-3">
                    <i class="bi bi-sliders me-2"></i>Parámetros del dispositivo
                </h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="min_value" class="form-label">Valor mínimo</label>
                        <input type="number" step="any" name="min_value" id="min_value" class="form-control"
                            placeholder="Ej: 0" value="{{ old('min_value', $device->min_value) }}">
                        @error('min_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="max_value" class="form-label">Valor máximo</label>
                        <input type="number" step="any" name="max_value" id="max_value" class="form-control"
                            placeholder="Ej: 100" value="{{ old('max_value', $device->max_value) }}">
                        @error('max_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="unit" class="form-label">Unidad de medida</label>
                        <input type="text" name="unit" id="unit" class="form-control"
                            placeholder="Ej: °C, %, m/s" value="{{ old('unit', $device->unit) }}">
                        @error('unit')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Botones --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="bi bi-save"></i> Guardar cambios
                    </button>
                    <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
