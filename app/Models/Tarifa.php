<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tarifa extends Model
{
    protected $fillable = ['nombre', 'precio'];

    public function facturas(): BelongsToMany
    {
        return $this->belongsToMany(Factura::class, 'facturas_tarifas')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }
}
