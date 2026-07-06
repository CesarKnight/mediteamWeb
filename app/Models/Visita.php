<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'ruta',
    'visitas',
    'ultima_visita_at',
])]
class Visita extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'ultima_visita_at' => 'datetime',
        ];
    }
}
