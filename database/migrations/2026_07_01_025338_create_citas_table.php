<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->constrained('medicos')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->dateTime('hora_inicio');
            $table->dateTime('hora_fin');
            $table->enum('estado', ['aprobado', 'pospuesto', 'cancelado'])->default('aprobado');
            $table->timestamps();

            $table->index(['medico_id', 'hora_inicio', 'hora_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};