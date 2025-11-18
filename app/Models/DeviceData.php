<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    use HasFactory;

    protected $table = 'device_datas';
    protected $fillable = ['device_id','type','value','unit','payload','observed_at'];

    protected $casts = [
        'payload' => 'array',
        'observed_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
