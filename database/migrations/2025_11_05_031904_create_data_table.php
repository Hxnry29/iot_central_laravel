<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->string('type');   // Ej: temperatura, humedad
            $table->string('value');  // Valor medido
            $table->string('unit')->nullable(); // Unidad, ej: °C, %
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_data');
    }
};
