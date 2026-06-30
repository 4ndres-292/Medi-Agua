<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Socio extends Model
{
    protected $fillable = [
        'nombres',
        'apellidos',
        'ci',
        'telefono',
        'direccion',
        'estado',
    ];

    public function medidores(): HasMany
    {
        return $this->hasMany(Medidor::class);
    }

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }
}
