@extends('layouts.app')

@section('title', 'Datos del Dispositivo')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-graph-up-arrow me-2"></i>Datos del dispositivo: {{ $device->name }}
        </h4>
        <a href="{{ route('devices.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div id="alertContainer"></div>

            <!-- Selector de rango histórico -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Rango de historial</label>
                <select id="historyRange" class="form-select w-auto d-inline-block">
                    <option value="1h">Última hora</option>
                    <option value="24h">Últimas 24 horas</option>
                    <option value="7d">Últimos 7 días</option>
                    <option value="30d">Últimos 30 días</option>
                    <option value="all" selected>Todo el historial</option>
                </select>
            </div>

            <div class="chart-container" style="position: relative; height:60vh; width:100%">
                <canvas id="deviceChart"></canvas>
            </div>

            <div class="mt-4">
                <p><strong>Unidad:</strong> {{ $device->unit ?? 'N/A' }}</p>
                <p><strong>Rango permitido:</strong> 
                    {{ $device->min_value ?? 'N/A' }} - {{ $device->max_value ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.2.1/dist/chartjs-plugin-annotation.min.js"></script>
<script>
    const chartData = @json($chartData);
    const deviceId = {{ $device->id }};
    const dataUrl = "{{ route('devices.data.json', $device->id) }}";
    const historyUrl = "{{ route('devices.data.history', $device->id) }}";
    const minValue = {{ $device->min_value ?? 'null' }};
    const maxValue = {{ $device->max_value ?? 'null' }};

    // Función para detectar valores fuera de rango
    function detectOutOfRange(data) {
        return data.datasets.some(dataset =>
            dataset.data.some(v => (minValue && v < minValue) || (maxValue && v > maxValue))
        );
    }

    // Función para resaltar puntos fuera del rango
    function highlightOutOfRangePoints(data) {
        return data.datasets.map(ds => ({
            ...ds,
            pointBackgroundColor: ds.data.map(v => 
                (minValue && v < minValue) || (maxValue && v > maxValue)
                    ? 'rgba(255,0,0,0.8)'
                    : 'rgba(54, 162, 235, 0.8)'
            ),
            borderColor: 'rgba(54,162,235,1)',
            borderWidth: 2,
            tension: 0.3,
            fill: false
        }));
    }

    // Mostrar alerta si hay valores fuera de rango
    function showAlertIfOutOfRange(data) {
        const outOfRange = detectOutOfRange(data);
        if (outOfRange) {
            const alert = `
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Se detectaron valores fuera del rango permitido.
                </div>
            `;
            document.getElementById('alertContainer').innerHTML = alert;
        } else {
            document.getElementById('alertContainer').innerHTML = '';
        }
    }

    // ====== LTTB DOWNSAMPLING FUNCTION ======
    function downsampleLTTB(data, threshold) {
        if (data.length <= threshold) return data;

        const sampled = [];
        const bucketSize = (data.length - 2) / (threshold - 2);

        let a = 0;
        sampled.push(data[a]);

        for (let i = 0; i < threshold - 2; i++) {
            const rangeStart = Math.floor((i + 1) * bucketSize) + 1;
            const rangeEnd = Math.floor((i + 2) * bucketSize) + 1;

            let avgX = 0, avgY = 0;
            for (let j = rangeStart; j < rangeEnd; j++) {
                avgX += data[j].x;
                avgY += data[j].y;
            }
            avgX /= (rangeEnd - rangeStart);
            avgY /= (rangeEnd - rangeStart);

            let maxArea = -1;
            let maxAreaIndex = -1;

            for (let j = Math.floor(i * bucketSize) + 1; j < Math.floor((i + 1) * bucketSize) + 1; j++) {
                const area = Math.abs(
                    (data[a].x - avgX) * (data[j].y - data[a].y)
                    - (data[a].x - data[j].x) * (avgY - data[a].y)
                ) * 0.5;

                if (area > maxArea) {
                    maxArea = area;
                    maxAreaIndex = j;
                }
            }

            sampled.push(data[maxAreaIndex]);
            a = maxAreaIndex;
        }

        sampled.push(data[data.length - 1]);

        return sampled;
    }

    // 🔄 Cargar historial con downsampling
    async function loadHistory() {
        const range = document.getElementById('historyRange').value;
        const url = `${historyUrl}?range=${range}`;

        try {
            const res = await fetch(url);
            const data = await res.json();

            // Convertimos datos a pares {x, y}
            const pairedData = data.labels.map((label, index) => ({
                x: index,
                y: data.values[index]
            }));

            // Aplicar downsampling solo si es necesario
            const MAX_POINTS = 1500; // Ajustable según necesidades
            const reduced = pairedData.length > MAX_POINTS ? 
                downsampleLTTB(pairedData, MAX_POINTS) : 
                pairedData;

            // Convertimos de vuelta a arrays para Chart.js
            const finalLabels = reduced.map(d => data.labels[d.x]);
            const finalValues = reduced.map(d => d.y);

            // Aplicar a la gráfica
            deviceChart.data.labels = finalLabels;
            deviceChart.data.datasets[0].data = finalValues;

            deviceChart.data.datasets[0].pointBackgroundColor = finalValues.map(v =>
                (minValue && v < minValue) || (maxValue && v > maxValue)
                    ? 'rgba(255,0,0,0.8)'
                    : 'rgba(54, 162, 235, 0.8)'
            );

            showAlertIfOutOfRange({datasets: [{data: finalValues}]});
            deviceChart.update();
            
        } catch (err) {
            console.error('Error al cargar historial:', err);
        }
    }

    // Procesar datos iniciales
    const processedChartData = {
        labels: chartData.labels,
        datasets: highlightOutOfRangePoints(chartData)
    };

    showAlertIfOutOfRange(chartData);

    const ctx = document.getElementById('deviceChart').getContext('2d');
    const deviceChart = new Chart(ctx, {
        type: 'line',
        data: processedChartData,
        options: {
            responsive: true,
            animation: false,
            interaction: { mode: 'index', intersect: false },
            stacked: false,
            scales: {
                x: { 
                    title: { display: true, text: 'Hora de observación' }
                },
                y: { 
                    title: { display: true, text: '{{ $device->unit ?? "Valor medido" }}' }, 
                    beginAtZero: true 
                }
            },
            plugins: {
                legend: { 
                    position: 'top' 
                },
                title: { 
                    display: true, 
                    text: 'Evolución de datos del dispositivo' 
                },
                annotation: {
                    annotations: {
                        ...(minValue ? {
                            minLine: {
                                type: 'line',
                                yMin: minValue,
                                yMax: minValue,
                                borderColor: 'rgba(255, 99, 132, 0.6)',
                                borderWidth: 2,
                                label: {
                                    content: 'Mínimo',
                                    enabled: true,
                                    position: 'end'
                                }
                            }
                        } : {}),
                        ...(maxValue ? {
                            maxLine: {
                                type: 'line',
                                yMin: maxValue,
                                yMax: maxValue,
                                borderColor: 'rgba(0, 200, 0, 0.6)',
                                borderWidth: 2,
                                label: {
                                    content: 'Máximo',
                                    enabled: true,
                                    position: 'end'
                                }
                            }
                        } : {})
                    }
                }
            }
        },
        plugins: [Chart.registry.getPlugin('annotation')]
    });

    // 🔄 Actualización automática SOLO agrega nuevo registro
    async function refreshNewData() {
        try {
            const res = await fetch(dataUrl);
            const newData = await res.json();

            // Verificar si es un dato nuevo
            const lastLabel = deviceChart.data.labels[deviceChart.data.labels.length - 1];
            const incomingLabel = newData.labels[newData.labels.length - 1];

            if (lastLabel !== incomingLabel) {
                // Agregar nuevo punto
                deviceChart.data.labels.push(incomingLabel);
                deviceChart.data.datasets[0].data.push(newData.datasets[0].data.slice(-1)[0]);
                
                // Si excedemos el límite de puntos, aplicar downsampling
                const MAX_POINTS = 1500;
                if (deviceChart.data.labels.length > MAX_POINTS) {
                    const pairedData = deviceChart.data.labels.map((label, index) => ({
                        x: index,
                        y: deviceChart.data.datasets[0].data[index]
                    }));
                    
                    const reduced = downsampleLTTB(pairedData, MAX_POINTS);
                    
                    deviceChart.data.labels = reduced.map(d => deviceChart.data.labels[d.x]);
                    deviceChart.data.datasets[0].data = reduced.map(d => d.y);
                }
                
                // Actualizar colores de puntos
                deviceChart.data.datasets[0].pointBackgroundColor = 
                    deviceChart.data.datasets[0].data.map(v => 
                        (minValue && v < minValue) || (maxValue && v > maxValue)
                            ? 'rgba(255,0,0,0.8)'
                            : 'rgba(54, 162, 235, 0.8)'
                    );
                
                // Verificar si el nuevo valor está fuera de rango
                const newValue = newData.datasets[0].data.slice(-1)[0];
                const isOutOfRange = (minValue && newValue < minValue) || (maxValue && newValue > maxValue);
                
                if (isOutOfRange) {
                    showAlertIfOutOfRange({datasets: [{data: [newValue]}]});
                }
                
                deviceChart.update();
            }
        } catch (err) {
            console.error('Error al actualizar datos:', err);
        }
    }

    // Event listeners
    document.getElementById('historyRange').addEventListener('change', loadHistory);

    // Inicializar
    loadHistory();

    // Configurar intervalos
    setInterval(refreshNewData, 10000); // Actualizar nuevos datos cada 10s
</script>

@endsection