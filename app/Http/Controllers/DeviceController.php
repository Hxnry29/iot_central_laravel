<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; 

class DeviceController extends Controller
{   
    public function command(Request $request, Device $device)
{
    $request->validate([
        'action' => 'required|string',
        'params' => 'nullable|string',
        
    ]);

    // Guardar el comando 
    DB::table('device_commands')->insert([
        'device_id' => $device->id,
        'action' => $request->action,
        'params' => $request->params ? json_decode($request->params, true) : null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return back()->with('success', 'Comando enviado correctamente.');
}






    public function index()
    {
        $devices = Device::latest()->paginate(10);
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|unique:devices',
            'name' => 'nullable|string|max:255',
            'kind' => 'required|in:real,api,digital_twin,dataset,virtual',
            'status' => 'required|in:online,offline,stopped',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
        ]);

        $validated['token'] = Str::random(16);
        $validated['meta'] = json_encode($request->meta ?? []);

        Device::create($validated);

        return redirect()->route('devices.index')->with('success', 'Dispositivo creado correctamente');
    }

    public function show(Device $device)
    {
        $datas = $device->datas()->latest()->take(50)->get(); 
    return view('devices.show', compact('device', 'datas'));
    }

    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'kind' => 'required|in:real,api,digital_twin,dataset,virtual',
            'status' => 'required|in:online,offline,stopped',
        ]);

        $validated['meta'] = json_encode($request->meta ?? []);

        $device->update($validated);

        return redirect()->route('devices.index')->with('success', 'Dispositivo actualizado');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Dispositivo eliminado');
    }
}
