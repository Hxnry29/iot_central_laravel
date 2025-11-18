<?php

use App\Http\Controllers\DeviceDataController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/', function(){ 
    return redirect()->route('devices.index'); 
});


Route::get('devices/{device}/data', [DeviceDataController::class, 'show'])
    ->name('devices.data');

Route::get('devices/{device}/data/json', [DeviceDataController::class, 'dataJson'])
    ->name('devices.data.json');


Route::resource('devices', DeviceController::class);

Route::post('devices/{device}/command', [DeviceController::class,'command'])
    ->name('devices.command');



Route::get('/devices/{device}/history', [App\Http\Controllers\DeviceController::class, 'history'])
    ->name('devices.data.history');
