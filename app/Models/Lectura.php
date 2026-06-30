<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lectura extends Model
{
    protected $fillable = [
        'medidor_id',
        'lectura_anterior',
        'lectura_actual',
        'consumo',
        'observacion',
        'usuario_id',
        'fecha_lectura',
    ];

    public function medidor(): BelongsTo
    {
        return $this->belongsTo(Medidor::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class);
    }
}
