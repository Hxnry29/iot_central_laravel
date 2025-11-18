@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-hdd-network me-2"></i>Datos del dispositivo: {{ $device->name }}
        </h4>
        <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h5 class="mb-3">📡 Datos técnicos recibidos (tiempo real)</h5>

            <!-- Lista scrollable -->
            <div id="dataContainer"
                 class="border rounded p-3 "
                 style="height: 450px; overflow-y: auto; font-size: 14px;">
                <p class="text-muted">Cargando datos...</p>
            </div>

        </div>
    </div>
</div>

<script>
    const dataUrl = "{{ route('devices.data.json', $device->id) }}";
    const listContainer = document.getElementById('dataContainer');

    // Renderizar la lista completa
    function renderData(data) {
        if (!data.labels || !data.datasets || data.datasets.length === 0) {
            listContainer.innerHTML = "<p class='text-muted'>No hay datos disponibles todavía.</p>";
            return;
        }

        const values = data.datasets[0].data;
        const timestamps = data.labels;

        // Revisar si backend envía extra metadata
        const meta = data.meta ?? []; // meta: [ { id, type, unit, observed_at } ]

        let html = "";

        // RECORRER DE MÁS RECIENTE A MÁS ANTIGUO
        for (let i = timestamps.length - 1; i >= 0; i--) {

            const item = meta[i] ?? {};  

            html += `
                <div class="p-3 mb-2 border rounded shadow-sm">

                    <strong class="text-primary">Valor:</strong> 
                    <span class="fw-bold">${values[i]}</span>

                    <div class="mt-1">
                        <strong>Tipo:</strong> ${item.type ?? 'N/A'}
                    </div>

                    <div>
                        <strong>Unidad:</strong> ${item.unit ?? 'N/A'}
                    </div>

                    <div>
                        <strong>Fecha/Hora:</strong> 
                        <span class="text-muted">${timestamps[i]}</span>
                    </div>

                    <div class="mt-1">
                        <strong>ID Registro:</strong> ${item.id ?? 'N/A'}
                    </div>

                    <div>
                        <strong>Device ID:</strong> {{ $device->device_id }}
                    </div>

                </div>
            `;
        }

        listContainer.innerHTML = html;
    }


    // Llamada al servidor
    async function loadData() {
        try {
            const res = await fetch(dataUrl);
            if (!res.ok) throw new Error("Error en la petición");
            const data = await res.json();

            // data.meta debe venir del backend
            renderData(data);

        } catch (e) {
            console.error("Error cargando datos:", e);
        }
    }

    // Actualización automática
    setInterval(loadData, 5000);
    loadData();
</script>
@endsection
