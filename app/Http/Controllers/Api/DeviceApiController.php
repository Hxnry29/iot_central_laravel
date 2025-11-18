<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceData;
use App\Models\DeviceCommand;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeviceApiController extends Controller
{
    // Recepción de datos: POST /api/device-data
    public function receiveData(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|exists:devices,device_id',
            'type' => 'nullable|string',
            'value' => 'required|numeric',
            'unit' => 'nullable|string',
            'timestamp' => 'nullable|date', // Cambiado de 'observed_at' a 'timestamp'
            'payload' => 'nullable|array',
        ]);

        $device = Device::where('device_id', $validated['device_id'])->first();

        // token check (más flexible para debugging)
        $token = $request->header('X-DEVICE-TOKEN');
        if (!$device) {
            return response()->json(['error' => 'device_not_found'], 404);
        }

        if ($device->token && $device->token !== $token) {
            Log::warning("Token mismatch for device {$device->device_id}");
            return response()->json(['error' => 'invalid_token'], 401);
        }

        $outOfRange = false;

        // Verificar si el valor está fuera del rango permitido
        if ($device->min_value !== null && $validated['value'] < $device->min_value) {
            $outOfRange = true;
        }

        if ($device->max_value !== null && $validated['value'] > $device->max_value) {
            $outOfRange = true;
        }

        $data = DeviceData::create([
            'device_id' => $device->id,
            'type' => $validated['type'] ?? 'value',
            'value' => $validated['value'],
            'unit' => $validated['unit'] ?? $device->unit,
            'payload' => $validated['payload'] ?? null,
            'observed_at' => $validated['timestamp'] ?? now(), // Cambiado aquí
        ]);

        // Log para debugging
        Log::info("📥 Datos recibidos - Dispositivo: {$device->device_id}, Valor: {$validated['value']}, Tipo: {$validated['type']}");

        // Log warning si está fuera de rango
        if ($outOfRange) {
            Log::warning("⚠️ Dato fuera de rango para el dispositivo {$device->device_id}: {$validated['value']} {$validated['unit']}");
        }

        return response()->json([
            'status' => 'ok',
            'stored' => $data->id,
            'message' => $outOfRange
                ? 'Dato recibido pero fuera del rango permitido'
                : 'Dato recibido correctamente',
            'out_of_range' => $outOfRange,
        ], 201);
    }

    // Device polling: GET /api/device/{device_id}/commands
    public function getCommands($device_id, Request $request)
    {
        $device = Device::where('device_id',$device_id)->first();
        $token = $request->header('X-DEVICE-TOKEN');
        if (!$device || ($device->token && $device->token !== $token)) {
            return response()->json(['error'=>'device_not_found_or_invalid_token'], 401);
        }

        $commands = $device->commands()->where('delivered', false)->get();

        return response()->json(['commands'=>$commands]);
    }

    // Mark a command as delivered: POST /api/device/{device_id}/commands/{command_id}/delivered
    public function markDelivered($device_id, $command_id, Request $request)
    {
        $device = Device::where('device_id',$device_id)->first();
        if (!$device) return response()->json(['error'=>'device_not_found'], 404);

        $command = $device->commands()->where('id', $command_id)->first();
        if (!$command) return response()->json(['error'=>'command_not_found'], 404);

        $command->delivered = true;
        $command->delivered_at = Carbon::now();
        $command->save();

        return response()->json(['status'=>'ok']);
    }

    /**
     * Obtener datos del dispositivo para la lista en tiempo real
     */
    public function getDeviceData($deviceId)
    {
        $device = Device::where('device_id', $deviceId)->first();
        
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }
        
        // Verificar token
        $token = request()->header('X-DEVICE-TOKEN');
        if ($device->token && $device->token !== $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $data = $device->datas()
            ->orderBy('observed_at', 'desc')
            ->take(50)
            ->get(['type', 'value', 'unit', 'observed_at']);
        
        return response()->json([
            'data' => $data
        ]);
    }
}