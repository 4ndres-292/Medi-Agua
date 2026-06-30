<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $fillable = [
        'socio_id',
        'tipo',
        'mensaje',
        'estado',
        'fecha_envio',
    ];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }
}
