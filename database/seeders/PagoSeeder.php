<?php

namespace Database\Seeders;

use App\Models\Pago;
use Illuminate\Database\Seeder;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        Pago::create([
            'factura_id'    => 1,
            'monto'         => 147.50,
            'metodo_pago'   => 'QR',
            'referencia_qr' => 'QR-20260620-000001-ANDRES',
            'fecha_pago'    => '2026-06-20 14:30:00',
        ]);
    }
}
