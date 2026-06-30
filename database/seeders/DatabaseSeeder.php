<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SocioSeeder::class,
            MedidorSeeder::class,
            LecturaSeeder::class,
            TarifaSeeder::class,
            FacturaSeeder::class,
            FacturaTarifaSeeder::class,
            PagoSeeder::class,
            NotificacionSeeder::class,
        ]);
    }
}
