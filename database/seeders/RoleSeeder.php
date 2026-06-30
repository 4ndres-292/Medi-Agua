<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Administrador'],
            ['id' => 2, 'name' => 'Contador'],
            ['id' => 3, 'name' => 'Lecturador'],
            ['id' => 4, 'name' => 'Usuario Común'],
        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }
    }
}
