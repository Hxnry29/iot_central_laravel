<?php

namespace Database\Seeders;
use Illuminate\Support\Str; 

use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    public function run()
    {
        Device::create([
            'device_id' => 'temp_real_1',
            'name' => 'Sensor Temperatura Real',
            'kind' => 'real',
            'token' => Str::random(32)
        ]);

        Device::create([
            'device_id' => 'openweather_bridge',
            'name' => 'API Weather (Bridge)',
            'kind' => 'api',
            'token' => Str::random(32)
        ]);

        Device::create([
            'device_id' => 'twin_1',
            'name' => 'Digital Twin 1',
            'kind' => 'digital_twin',
            'token' => Str::random(32),
            'meta' => ['sim_interval'=>5]
        ]);

        Device::create([
            'device_id' => 'dataset_1',
            'name' => 'Dataset Simulator',
            'kind' => 'dataset',
            'token' => Str::random(32)
        ]);
    }
}
