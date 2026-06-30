<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    protected $fillable = [
        'numero',
        'socio_id',
        'lectura_id',
        'monto_total',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
    ];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }

    public function lectura(): BelongsTo
    {
        return $this->belongsTo(Lectura::class);
    }

    public function tarifas(): BelongsToMany
    {
        return $this->belongsToMany(Tarifa::class, 'facturas_tarifas')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}
