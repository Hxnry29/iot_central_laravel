<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // identificador externo (ej: temp1)
            $table->string('name')->nullable();
            $table->enum('kind',['real','api','digital_twin','dataset','virtual'])->default('real');
            $table->json('meta')->nullable(); // para parámetros custom
            $table->string('status')->default('online'); // online, offline, stopped
            $table->string('token')->nullable(); // token simple para autenticación de dispositivo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
