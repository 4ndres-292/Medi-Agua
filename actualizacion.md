
## Objetivo

Quiero mantener una captura de datos uniforme en todo el sistema.

Por esta razón los nombres y apellidos deben seguir una convención específica.

---

# Actualización de Seeders

Todos los Seeders existentes deben actualizarse para cumplir con las nuevas reglas de validación.

Ningún Seeder debe insertar información que posteriormente sería rechazada por las validaciones de la API.

Por ejemplo:

* No utilizar nombres con vocales acentuadas.
* No utilizar apellidos con vocales acentuadas.
* Sí utilizar la letra ñ cuando corresponda.
* Mantener teléfonos únicamente con números.
* Mantener CI únicamente con números.
* Mantener estados utilizando únicamente los valores permitidos.
* Mantener direcciones simples pero realistas.

Ejemplos correctos:

```text
Juan
Maria
Carlos
Peña
Muñoz
Choquecahuana
```

Ejemplos incorrectos:

```text
José
María
Ángel
Óscar
```

El objetivo es que después de ejecutar:

```bash
php artisan migrate:fresh --seed
```

todos los datos se inserten correctamente sin producir errores de validación.

---

## Nombres y Apellidos

Los campos:

* nombres
* apellidos

deben cumplir las siguientes reglas.

### Permitido

* Letras A-Z
* Letras a-z
* La letra ñ y Ñ
* Espacios

Ejemplos válidos

```text
Juan
Maria
Carlos
Peña
Muñoz
Choquecahuana
Juan Carlos
Maria Lopez
```

### No permitido

#### Vocales con tilde

```text
José
María
Ángel
Óscar
```

#### Números

```text
Juan123
```

#### Caracteres especiales

```text
Juan@
Carlos#
Pedro$
```

#### Emojis

```text
Juan😀
```

Cuando la validación falle debe responder utilizando el mismo formato JSON definido en toda la API.

Ejemplo:

```json
{
    "success": false,
    "message": "Error de validacion.",
    "errors": {
        "nombres": [
            "El nombre solo puede contener letras, espacios y la letra ñ. No se permiten vocales con tilde."
        ]
    }
}
```

---

## Direcciones

NO restringir direcciones.

Únicamente validar:

* requerido
* longitud máxima

Las direcciones pueden contener libremente:

* letras
* números
* #
* N°
* guiones
* barras
* comas
* puntos

---

## CI

Permitir únicamente números.

---

## Teléfono

Permitir únicamente números.

---

## Correos electrónicos

Continuar utilizando la validación estándar de Laravel.

---

## Estados

Validar únicamente los estados permitidos para cada entidad.

Ejemplo:

```text
activo
inactivo
pendiente
pagada
vencida
```

No aceptar otros valores.

---

## Importante

* No modificar la lógica de negocio.
* No modificar la estructura de la base de datos.
* No modificar migraciones.
* No modificar relaciones.
* No modificar las respuestas JSON.
* No modificar las rutas.
* No cambiar la arquitectura REST de la API.
* Adaptar todos los Seeders para que sean compatibles con estas reglas.
* Verificar que `php artisan migrate:fresh --seed` finalice sin errores.
* Verificar que todos los datos generados puedan ser consumidos correctamente desde Postman y posteriormente desde React.
