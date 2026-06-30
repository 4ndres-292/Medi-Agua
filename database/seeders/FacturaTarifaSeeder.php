<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacturaTarifaSeeder extends Seeder
{
    public function run(): void
    {
        // Factura 1 - Socio 1: Consumo por m³, Pro-Deporte, Mantenimiento
        // Consumo: 53 m³ × 2.50 = 132.50
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 1,
            'tarifa_id'       => 1,
            'cantidad'        => 53.00,
            'precio_unitario' => 2.50,
            'subtotal'        => 132.50,
            'created_at'      => now(),
        ]);

        // Pro-Deporte: 1 × 5.00 = 5.00
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 1,
            'tarifa_id'       => 2,
            'cantidad'        => 1.00,
            'precio_unitario' => 5.00,
            'subtotal'        => 5.00,
            'created_at'      => now(),
        ]);

        // Mantenimiento: 1 × 10.00 = 10.00
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 1,
            'tarifa_id'       => 3,
            'cantidad'        => 1.00,
            'precio_unitario' => 10.00,
            'subtotal'        => 10.00,
            'created_at'      => now(),
        ]);

        // Factura 2 - Socio 2: Consumo por m³, Alcantarillado
        // Consumo: 52 m³ × 2.50 = 130.00
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 2,
            'tarifa_id'       => 1,
            'cantidad'        => 52.00,
            'precio_unitario' => 2.50,
            'subtotal'        => 130.00,
            'created_at'      => now(),
        ]);

        // Alcantarillado: 1 × 8.00 = 8.00
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 2,
            'tarifa_id'       => 4,
            'cantidad'        => 1.00,
            'precio_unitario' => 8.00,
            'subtotal'        => 8.00,
            'created_at'      => now(),
        ]);

        // Factura 3 - Socio 3: Consumo por m³, Pro-Deporte, Multa por retraso
        // Consumo: 55 m³ × 2.50 = 137.50
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 3,
            'tarifa_id'       => 1,
            'cantidad'        => 55.00,
            'precio_unitario' => 2.50,
            'subtotal'        => 137.50,
            'created_at'      => now(),
        ]);

        // Pro-Deporte: 1 × 5.00 = 5.00
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 3,
            'tarifa_id'       => 2,
            'cantidad'        => 1.00,
            'precio_unitario' => 5.00,
            'subtotal'        => 5.00,
            'created_at'      => now(),
        ]);

        // Multa por retraso: 1 × 20.00 = 20.00
        DB::table('facturas_tarifas')->insert([
            'factura_id'      => 3,
            'tarifa_id'       => 5,
            'cantidad'        => 1.00,
            'precio_unitario' => 20.00,
            'subtotal'        => 20.00,
            'created_at'      => now(),
        ]);
    }
}
