<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_qrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->cascadeOnDelete();
            $table->uuid('transferencia_uuid')->unique();
            $table->float('monto_recibido');
            $table->string('pago_facil_id')->nullable();
            $table->enum('estado', ['pendiente', 'exitoso', 'anulado'])->default('pendiente');
            $table->text('qr_base64')->nullable();
            $table->dateTime('expiracion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_qrs');
    }
};