<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceData;
use Illuminate\Http\Request;

class DeviceDataController extends Controller
{
    /**
     * Muestra los datos del dispositivo y genera la vista del gráfico.
     */
    public function show(Device $device)
    {
        // Obtenemos los últimos 100 registros del dispositivo
        $data = $device->datas()
            ->orderBy('observed_at', 'asc')
            ->take(100)
            ->get(['type', 'value', 'observed_at']);

        // Agrupamos los datos por tipo (ej: temperature, humidity, etc.)
        $grouped = $data->groupBy('type');

        // Estructura base para Chart.js
        $chartData = [
            'labels' => $data->pluck('observed_at')->map(fn($d) => $d ? $d->format('H:i:s') : null),
            'datasets' => [],
        ];

        // Colores personalizados para cada tipo de dato
        $colors = [
            'temperature' => 'rgba(255, 99, 132, 1)',
            'humidity'    => 'rgba(54, 162, 235, 1)',
            'pressure'    => 'rgba(255, 206, 86, 1)',
            'default'     => 'rgba(75, 192, 192, 1)',
        ];

        foreach ($grouped as $type => $values) {
            $color = $colors[$type] ?? $colors['default'];
            $chartData['datasets'][] = [
                'label' => ucfirst($type),
                'data' => $values->pluck('value'),
                'borderColor' => $color,
                'backgroundColor' => str_replace('1)', '0.2)', $color),
                'tension' => 0.3,
                'fill' => true,
            ];
        }

        return view('devices.data', compact('device', 'chartData'));
    }

    /**
     * Devuelve los datos del dispositivo en formato JSON (para actualizaciones en vivo o AJAX).
     */
    public function dataJson(Device $device)
{
    $data = $device->datas()
        ->orderBy('observed_at', 'asc')
        ->take(100)
        ->get(['id', 'type', 'value', 'unit', 'observed_at', 'created_at']);

    $grouped = $data->groupBy('type');

    $chartData = [
        'labels' => $data->pluck('observed_at')->map(fn($d) => $d ? $d->format('H:i:s') : null),
        'datasets' => [],
        // 🔥 METADATOS QUE NECESITA TU NUEVA VISTA
        'meta' => $data->map(fn($d) => [
            'id'   => $d->id,
            'type' => $d->type,
            'unit' => $d->unit,
            'observed_at' => optional($d->observed_at)->format('Y-m-d H:i:s'),
        ]),
    ];

    $colors = [
        'temperature' => 'rgba(255, 99, 132, 1)',
        'humidity'    => 'rgba(54, 162, 235, 1)',
        'pressure'    => 'rgba(255, 206, 86, 1)',
        'default'     => 'rgba(75, 192, 192, 1)',
    ];

    foreach ($grouped as $type => $values) {
        $color = $colors[$type] ?? $colors['default'];
        $chartData['datasets'][] = [
            'label' => ucfirst($type),
            'data' => $values->pluck('value'),
            'borderColor' => $color,
            'backgroundColor' => str_replace('1)', '0.2)', $color),
            'tension' => 0.3,
            'fill' => true,
        ];
    }

    return response()->json($chartData);
}

    /**
     * Página principal con lista de datos del dispositivo (paginación tradicional).
     */
    public function list(Device $device)
    {
        $data = DeviceData::where('device_id', $device->id)
            ->orderBy('observed_at', 'desc')
            ->paginate(20);

        return view('devices.data_list', compact('device', 'data'));
    }

    /**
     * Cargar más datos para scroll infinito.
     */
    public function loadMore(Device $device, Request $request)
    {
        $page = $request->page ?? 1;

        $data = DeviceData::where('device_id', $device->id)
            ->orderBy('observed_at', 'desc')
            ->paginate(20, ['*'], 'page', $page);

        return response()->json([
            'html' => view('devices.partials.data_items', compact('data'))->render(),
            'nextPage' => $data->nextPageUrl() ? $page + 1 : null,
        ]);
    }
}