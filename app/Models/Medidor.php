<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medidor extends Model {
    protected $fillable = ['codigo', 'ubicacion', 'socio_id', 'estado'];

    public function socio() {
        return $this->belongsTo(Socio::class);
    }

    public function lecturas() {
        return $this->hasMany(Lectura::class);
    }
}