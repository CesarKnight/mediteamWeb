<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'paciente_id',
    'medico_id',
    'servicio_id',
    'hora_inicio',
    'hora_fin',
    'estado',
])]
class Cita extends Model
{
    use HasFactory;

    protected $casts = [
        'hora_inicio' => 'datetime',
        'hora_fin'    => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}