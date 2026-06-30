<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'estado',
    'paciente_id',
    'medico_id',
])]
class Historia extends Model
{
    use HasFactory;

    public function medicoCreador(): BelongsTo
    {
        return $this->belongsTo(Medico::class,'medico_id');
    }

    public function medicosInvolucrados(): BelongsToMany
    {
        return $this->belongsToMany(Medico::class, 'historia_medico')
                    ->withTimestamps();
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
}
