<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'paciente_id',
    'servicio_id',
    'total',
    'estado',
    'metodo',
])]
class Pago extends Model
{
    use HasFactory;

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function pagoQrs(): HasMany
    {
        return $this->hasMany(PagoQr::class);
    }
}
