<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medidor extends Model
{
    protected $table = 'medidores';
    protected $fillable = ['codigo', 'ubicacion', 'socio_id', 'estado'];

    public function socio(): BelongsTo
    {
        return $this->belongsTo(Socio::class);
    }

    public function lecturas(): HasMany
    {
        return $this->hasMany(Lectura::class);
    }
}
