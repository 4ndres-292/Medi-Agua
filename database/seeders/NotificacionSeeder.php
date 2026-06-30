<?php

namespace Database\Seeders;

use App\Models\Notificacion;
use Illuminate\Database\Seeder;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        // Socio 1 - María Elena García
        Notificacion::create([
            'socio_id'    => 1,
            'tipo'        => 'Factura generada',
            'mensaje'     => 'Se ha generado la factura N.º 000001 por un monto de 147.50 Bs. Fecha de vencimiento: 30/06/2026.',
            'estado'      => 'leido',
            'fecha_envio' => '2026-06-16 09:00:00',
        ]);

        Notificacion::create([
            'socio_id'    => 1,
            'tipo'        => 'Pago registrado',
            'mensaje'     => 'Se ha registrado su pago de 147.50 Bs por el método QR. Factura N.º 000001.',
            'estado'      => 'enviado',
            'fecha_envio' => '2026-06-20 14:35:00',
        ]);

        // Socio 2 - Juan Carlos Mamani
        Notificacion::create([
            'socio_id'    => 2,
            'tipo'        => 'Factura generada',
            'mensaje'     => 'Se ha generado la factura N.º 000002 por un monto de 138.00 Bs. Fecha de vencimiento: 01/07/2026.',
            'estado'      => 'enviado',
            'fecha_envio' => '2026-06-17 09:00:00',
        ]);

        Notificacion::create([
            'socio_id'    => 2,
            'tipo'        => 'Pago pendiente',
            'mensaje'     => 'Recuerde que la factura N.º 000002 vence el 01/07/2026. Monto: 138.00 Bs.',
            'estado'      => 'pendiente',
            'fecha_envio' => '2026-06-25 09:00:00',
        ]);

        // Socio 3 - Ana Lucía Flores
        Notificacion::create([
            'socio_id'    => 3,
            'tipo'        => 'Factura generada',
            'mensaje'     => 'Se ha generado la factura N.º 000003 por un monto de 162.50 Bs. Fecha de vencimiento: 25/06/2026.',
            'estado'      => 'enviado',
            'fecha_envio' => '2026-06-18 09:00:00',
        ]);

        Notificacion::create([
            'socio_id'    => 3,
            'tipo'        => 'Pago pendiente',
            'mensaje'     => 'La factura N.º 000003 ha vencido. Se ha aplicado una multa por retraso. Contacte a la administración.',
            'estado'      => 'enviado',
            'fecha_envio' => '2026-06-26 09:00:00',
        ]);
    }
}
