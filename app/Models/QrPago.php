<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrPago extends Model
{
    protected $fillable = [
        'imagen',
        'fecha_actualizacion',
        'valido_hasta',
        'actualizado_por',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }
}