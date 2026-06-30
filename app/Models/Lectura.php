<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lectura extends Model {
    protected $fillable = [
        'medidor_id', 'lectura_anterior', 'lectura_actual',
        'consumo', 'observacion', 'usuario_id', 'fecha_lectura'
    ];

    public function medidor() {
        return $this->belongsTo(Medidor::class);
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}