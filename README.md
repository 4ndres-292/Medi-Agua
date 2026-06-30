# Prompt para generar el Backend de Medi-Agua (Laravel 13)

Actúa como un **Senior Backend Developer especializado en Laravel 13**, arquitectura REST API, buenas prácticas, SOLID y Clean Code.

Estoy desarrollando un sistema llamado **Medi-Agua**, cuyo frontend será desarrollado posteriormente en **React**.

Por el momento **NO quiero desarrollar React**, únicamente el backend que será consumido por Postman y posteriormente por React.

## Objetivo

Generar únicamente la capa de acceso a datos (Backend API REST).

No quiero autenticación, vistas Blade, Livewire ni Inertia.

Todo debe responder mediante **JSON**.

---

# Debes generar únicamente

- Migraciones
- Modelos Eloquent
- Controladores API
- Rutas en `routes/api.php`

No debes generar:

- React
- Blade
- Resources
- Seeders
- Factories
- Policies
- Middleware personalizados
- Tests
- Autenticación
- Swagger
- Documentación OpenAPI

---

# Arquitectura

El proyecto utilizará Laravel 13.

Las rutas deben definirse únicamente en:

```php
routes/api.php
```

Los controladores deben ser API Controllers.

Nunca utilizar:

```php
return view(...);

return redirect(...);
```

Toda respuesta debe ser JSON.

---

# Formato de respuesta exitoso

Cuando una operación sea correcta devolver:

```json
{
    "success": true,
    "message": "Mensaje descriptivo",
    "data": {}
}
```

Ejemplo:

```json
{
    "success": true,
    "message": "Socio creado correctamente.",
    "data": {
        ...
    }
}
```

---

# Formato de errores

Los errores también deben responder en JSON.

Ejemplo para validación:

```json
{
    "success": false,
    "message": "Error de validación.",
    "errors": {
        "nombre": ["El nombre es obligatorio."]
    }
}
```

Ejemplo cuando no existe un recurso:

```json
{
    "success": false,
    "message": "Socio no encontrado."
}
```

Ejemplo para errores internos:

```json
{
    "success": false,
    "message": "Ocurrió un error interno."
}
```

Nunca devolver HTML.

Nunca devolver páginas de error.

---

# Validaciones

Utilizar las validaciones propias de Laravel.

Validar todos los campos necesarios.

Responder siempre con JSON.

---

# CRUD

Cada controlador debe implementar:

- index()
- store()
- show()
- update()
- destroy()

Cada método debe devolver respuestas JSON.

---

# Rutas

Utilizar únicamente:

```php
Route::apiResource(...)
```

No utilizar rutas web.

---

# Modelos

Crear los modelos utilizando relaciones Eloquent correctamente.

Utilizar:

- hasMany
- belongsTo
- belongsToMany

según corresponda.

Definir correctamente:

```php
protected $fillable = [];
```

No utilizar `$guarded`.

---

# Relaciones

Implementar todas las relaciones de la base de datos.

Debe utilizarse correctamente la tabla pivote para la relación Muchos a Muchos entre Facturas y Tarifas.

---

# Migraciones

Crear migraciones completas.

Agregar:

- Foreign Keys
- Cascade cuando corresponda
- Restricciones
- Tipos de datos adecuados

No omitir ninguna relación.

---

# Base de Datos

La base de datos contiene las siguientes entidades:

- Roles
- Users
- Socios
- Medidores
- Lecturas
- Tarifas
- Facturas
- Facturas_Tarifas (tabla pivote)
- Pagos
- Notificaciones

La relación entre Facturas y Tarifas es de Muchos a Muchos mediante la tabla pivote `facturas_tarifas`.

La tabla pivote debe contener:

- id
- factura_id
- tarifa_id
- cantidad
- precio_unitario
- subtotal
- created_at

La tabla Facturas NO debe contener `tarifa_id`.

---

# Orden de desarrollo

Generar los archivos en el siguiente orden:

1. Migraciones
2. Modelos
3. Relaciones Eloquent
4. Controladores API
5. Rutas en `routes/api.php`

No avanzar al siguiente paso hasta terminar completamente el anterior.

---

# Buenas prácticas

Aplicar:

- Clean Code
- SOLID
- Convenciones de Laravel 13
- Nombres claros
- Código reutilizable
- Relaciones correctamente tipadas
- Validaciones completas
- Respuestas HTTP adecuadas

Utilizar los códigos HTTP correspondientes:

- 200 OK
- 201 Created
- 204 No Content
- 400 Bad Request
- 404 Not Found
- 422 Unprocessable Entity
- 500 Internal Server Error

---

# Resultado esperado

Quiero un backend completamente funcional para ser probado en Postman.

Posteriormente ese backend será consumido por React sin necesidad de modificar la lógica de los controladores.

No expliques teoría.

No hagas resúmenes.

Genera directamente el código completo de cada archivo siguiendo el orden indicado.
