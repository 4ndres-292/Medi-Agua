<?php

namespace Database\Seeders;

use App\Models\Lectura;
use Illuminate\Database\Seeder;

class LecturaSeeder extends Seeder
{
    public function run(): void
    {
        // Medidor 000020 - Socio 1
        $consumo1 = 12.00;
        Lectura::create([
            'medidor_id'       => 1,
            'lectura_anterior' => 0.00,
            'lectura_actual'   => 12.00,
            'consumo'          => $consumo1,
            'observacion'      => 'Lectura normal.',
            'usuario_id'       => 1,
            'fecha_lectura'    => '2026-05-15',
        ]);

        $consumo2 = 65.00 - 12.00;
        Lectura::create([
            'medidor_id'       => 1,
            'lectura_anterior' => 12.00,
            'lectura_actual'   => 65.00,
            'consumo'          => $consumo2,
            'observacion'      => 'Medidor en buen estado.',
            'usuario_id'       => 1,
            'fecha_lectura'    => '2026-06-15',
        ]);

        // Medidor 000040 - Socio 2
        $consumo3 = 10.00;
        Lectura::create([
            'medidor_id'       => 2,
            'lectura_anterior' => 0.00,
            'lectura_actual'   => 10.00,
            'consumo'          => $consumo3,
            'observacion'      => 'Sin novedades.',
            'usuario_id'       => 1,
            'fecha_lectura'    => '2026-05-16',
        ]);

        $consumo4 = 62.00 - 10.00;
        Lectura::create([
            'medidor_id'       => 2,
            'lectura_anterior' => 10.00,
            'lectura_actual'   => 62.00,
            'consumo'          => $consumo4,
            'observacion'      => 'Lectura normal.',
            'usuario_id'       => 1,
            'fecha_lectura'    => '2026-06-16',
        ]);

        // Medidor 000060 - Socio 3
        $consumo5 = 15.00;
        Lectura::create([
            'medidor_id'       => 3,
            'lectura_anterior' => 0.00,
            'lectura_actual'   => 15.00,
            'consumo'          => $consumo5,
            'observacion'      => 'Medidor en buen estado.',
            'usuario_id'       => 1,
            'fecha_lectura'    => '2026-05-17',
        ]);

        $consumo6 = 70.00 - 15.00;
        Lectura::create([
            'medidor_id'       => 3,
            'lectura_anterior' => 15.00,
            'lectura_actual'   => 70.00,
            'consumo'          => $consumo6,
            'observacion'      => 'Sin novedades.',
            'usuario_id'       => 1,
            'fecha_lectura'    => '2026-06-17',
        ]);
    }
}
