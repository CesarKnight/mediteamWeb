<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'historia_id',
    'motivo',
    'peso',
    'altura',
])]
class Consulta extends Model
{
    use HasFactory;

    public function historia(): BelongsTo
    {
        return $this->belongsTo(Historia::class);
    }
}