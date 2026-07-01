<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pago_id',
    'transferencia_uuid',
    'monto_recibido',
    'pago_facil_id',
    'estado',
    'qr_base64',
    'expiracion',
])]
class PagoQr extends Model
{
    use HasFactory;

    protected $casts = [
        'expiracion' => 'datetime',
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }
}