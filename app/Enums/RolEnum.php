<?php

namespace App\Enums;

enum RolEnum: string
{
    case Administrador = 'administrador';
    case Contador = 'contador';
    case Lecturador = 'lecturador';
    case Comun = 'comun';

    /**
     * Nombre legible para mostrar en frontend, reportes, logs, etc.
     * Mantenido separado del valor 'name' de la BD a propósito:
     * este es un fallback/valor de referencia en código,
     * la fuente de verdad para mostrar al usuario sigue siendo la tabla `roles`.
     */
    public function label(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Contador => 'Contador',
            self::Lecturador => 'Lecturador',
            self::Comun => 'Comun',
        };
    }
}