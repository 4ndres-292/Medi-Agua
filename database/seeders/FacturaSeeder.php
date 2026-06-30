<?php

namespace Database\Seeders;

use App\Models\Factura;
use Illuminate\Database\Seeder;

class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        // Factura 1 - Socio 1 - Pagada
        // Consumo: 53 m³ × 2.50 = 132.50 + Pro-Deporte 5.00 + Mantenimiento 10.00 = 147.50
        Factura::create([
            'numero'            => '000001',
            'socio_id'          => 1,
            'lectura_id'        => 2,
            'monto_total'       => 147.50,
            'fecha_emision'     => '2026-06-16',
            'fecha_vencimiento' => '2026-06-30',
            'estado'            => 'Pagada',
        ]);

        // Factura 2 - Socio 2 - Pendiente
        // Consumo: 52 m³ × 2.50 = 130.00 + Alcantarillado 8.00 = 138.00
        Factura::create([
            'numero'            => '000002',
            'socio_id'          => 2,
            'lectura_id'        => 4,
            'monto_total'       => 138.00,
            'fecha_emision'     => '2026-06-17',
            'fecha_vencimiento' => '2026-07-01',
            'estado'            => 'Pendiente',
        ]);

        // Factura 3 - Socio 3 - Vencida
        // Consumo: 55 m³ × 2.50 = 137.50 + Pro-Deporte 5.00 + Multa 20.00 = 162.50
        Factura::create([
            'numero'            => '000003',
            'socio_id'          => 3,
            'lectura_id'        => 6,
            'monto_total'       => 162.50,
            'fecha_emision'     => '2026-06-18',
            'fecha_vencimiento' => '2026-06-25',
            'estado'            => 'Vencida',
        ]);
    }
}
