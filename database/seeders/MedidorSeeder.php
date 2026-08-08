<?php

namespace Database\Seeders;

use App\Models\Medidor;
use Illuminate\Database\Seeder;

class MedidorSeeder extends Seeder
{
    public function run(): void
    {
        Medidor::create([
            'codigo'     => '000020',
            'socio_id'   => 1,
            'observacion' => 'Medidor principal del socio',
        ]);

        Medidor::create([
            'codigo'     => '000040',
            'socio_id'   => 2,
            'observacion' => null,
        ]);

        Medidor::create([
            'codigo'     => '000060',
            'socio_id'   => 3,
            'observacion' => 'Medidor secundario',
        ]);
    }
}
