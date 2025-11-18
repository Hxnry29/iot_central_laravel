<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener todos los dispositivos con su último dato cargado
        $devices = Device::with(['datas' => function ($query) {
            $query->latest();  // toma el dato más reciente
        }])->get();

        // Estadísticas
        $totalDevices = $devices->count();
        $activeDevices = $devices->where('status', 'online')->count();
        $inactiveDevices = $devices->where('status', 'offline')->count();
        $stoppedDevices = $devices->where('status', 'stopped')->count(); // opcional

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'activeDevices',
            'inactiveDevices',
            'stoppedDevices'
        ));
    }
}
