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

    // 🔄 Actualización automática cada 10 segundos
    async function refreshChartData() {
        try {
            const response = await fetch(dataUrl);
            if (!response.ok) throw new Error('Error al cargar datos');
            const newData = await response.json();

            // Procesar nuevos datos
            const processedNewData = {
                labels: newData.labels,
                datasets: highlightOutOfRangePoints(newData)
            };

            deviceChart.data.labels = processedNewData.labels;
            deviceChart.data.datasets = processedNewData.datasets;
            
            // Mostrar/ocultar alerta según los nuevos datos
            showAlertIfOutOfRange(newData);
            
            deviceChart.update();
        } catch (err) {
            console.error(err);
        }
    }

    // Llamar cada 10 segundos
    setInterval(refreshChartData, 10000);
</script>

@endsection