<?php

namespace Database\Seeders;

use App\Models\Tarifa;
use Illuminate\Database\Seeder;

class TarifaSeeder extends Seeder
{
    public function run(): void
    {
        Tarifa::create(['nombre' => 'Consumo por m³',    'precio' => 2.50]);
        Tarifa::create(['nombre' => 'Pro-Deporte',       'precio' => 5.00]);
        Tarifa::create(['nombre' => 'Mantenimiento',     'precio' => 10.00]);
        Tarifa::create(['nombre' => 'Alcantarillado',    'precio' => 8.00]);
        Tarifa::create(['nombre' => 'Multa por retraso', 'precio' => 20.00]);
    }
}
