<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCommand extends Model
{
    use HasFactory;

    protected $fillable = ['device_id','action','params','delivered','delivered_at'];

    protected $casts = [
        'params' => 'array',
        'delivered_at' => 'datetime',
        'delivered' => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
