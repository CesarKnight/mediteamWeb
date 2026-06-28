<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'estado'
])]
class Paciente extends Model
{
    use HasFactory;
    
    /**
    * Get the user associated with the paciente.
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
