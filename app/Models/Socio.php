<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socio extends Model {
    protected $fillable = ['nombre', 'apellido', 'cedula', 'telefono', 'direccion'];

    public function medidores() {
        return $this->hasMany(Medidor::class);
    }
}