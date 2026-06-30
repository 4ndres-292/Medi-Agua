<?php

namespace Database\Seeders;

use App\Models\Medidor;
use Illuminate\Database\Seeder;

class MedidorSeeder extends Seeder
{
    public function run(): void
    {
        Medidor::create([
            'codigo'    => '000020',
            'ubicacion' => 'Av. Los Libradores No. 1245, Zona Villa Fátima',
            'socio_id'  => 1,
            'estado'    => 'activo',
        ]);

        Medidor::create([
            'codigo'    => '000040',
            'ubicacion' => 'Calle Sucre No. 892, Zona Centro',
            'socio_id'  => 2,
            'estado'    => 'activo',
        ]);

        Medidor::create([
            'codigo'    => '000060',
            'ubicacion' => 'Av. Ballivián No. 567, Zona San Jorge',
            'socio_id'  => 3,
            'estado'    => 'activo',
        ]);
    }
}
