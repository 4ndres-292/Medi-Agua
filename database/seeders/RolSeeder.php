<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Enums\RolEnum;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RolEnum::cases() as $rol) {
            Rol::updateOrCreate(
                ['slug' => $rol->value],
                ['name' => $rol->label()]
            );
        }
    }
}