<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'especialidad'
])]
class Medico extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function historiasCreadas(): HasMany
    {
        return $this->hasMany(Historia::class);
    }

    public function historiasInvolucradas(): BelongsToMany
    {
        return $this->belongsToMany(Historia::class, 'historia_medico')
            ->withTimestamps();
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
