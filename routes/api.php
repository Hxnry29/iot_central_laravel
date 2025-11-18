<?php

use App\Http\Controllers\Api\DeviceApiController;
use Illuminate\Support\Facades\Route;

Route::post('/device-data', [DeviceApiController::class,'receiveData']);
Route::get('/device/{device_id}/commands', [DeviceApiController::class,'getCommands']);
Route::post('/device/{device_id}/commands/{command_id}/delivered', [DeviceApiController::class,'markDelivered']);


    