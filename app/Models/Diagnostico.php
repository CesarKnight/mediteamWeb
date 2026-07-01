<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'historia_id',
    'diagnostico',
    'enfermedad',
    'gravedad',
])]
class Diagnostico extends Model
{
    use HasFactory;

    public function historia(): BelongsTo
    {
        return $this->belongsTo(Historia::class);
    }
}