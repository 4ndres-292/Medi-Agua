<?php

namespace Database\Seeders;

use App\Models\Socio;
use Illuminate\Database\Seeder;

class SocioSeeder extends Seeder
{
    public function run(): void
    {
        Socio::create([
            'nombres'   => 'Maria Elena',
            'apellidos' => 'Garcia Lopez',
            'ci'        => '7025489',
            'telefono'  => '76541230',
            'direccion' => 'Av. Los Libradores No. 1245, Zona Villa Fátima',
            'estado'    => 'Activo',
        ]);

        Socio::create([
            'nombres'   => 'Juan Carlos',
            'apellidos' => 'Mamani Quispe',
            'ci'        => '5189632',
            'telefono'  => '71234568',
            'direccion' => 'Calle Sucre No. 892, Zона Centro',
            'estado'    => 'Activo',
        ]);

        Socio::create([
            'nombres'   => 'Ana Lucia',
            'apellidos' => 'Flores Mendoza',
            'ci'        => '6325874',
            'telefono'  => '78456123',
            'direccion' => 'Av. Ballivián No. 567, Zona San Jorge',
            'estado'    => 'Activo',
        ]);
    }
}
