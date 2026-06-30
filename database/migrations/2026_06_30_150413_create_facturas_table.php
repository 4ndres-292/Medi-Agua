<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('socio_id')->constrained('socios')->onDelete('cascade');
            $table->foreignId('lectura_id')->constrained('lecturas')->onDelete('cascade');
            $table->decimal('monto_total', 10, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->string('estado')->default('Pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
