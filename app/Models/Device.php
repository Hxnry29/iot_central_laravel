<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'name',
        'kind',
        'meta',
        'status',
        'token',
        'min_value',
        'max_value',
        'unit',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function datas()
    {
        return $this->hasMany(DeviceData::class);
    }

    public function commands()
    {
        return $this->hasMany(DeviceCommand::class);
    }

    public function data()
    {
        return $this->hasMany(DeviceData::class);
    }
}
