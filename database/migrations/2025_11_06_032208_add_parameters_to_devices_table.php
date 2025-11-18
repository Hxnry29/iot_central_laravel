<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('devices', function (Blueprint $table) {
        $table->double('min_value')->nullable();
        $table->double('max_value')->nullable();
        $table->string('unit')->nullable();
    });
}

public function down(): void
{
    Schema::table('devices', function (Blueprint $table) {
        $table->dropColumn(['min_value', 'max_value', 'unit']);
    });
}
};
