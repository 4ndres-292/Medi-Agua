# 03 — Endpoints

Documentación completa de cada endpoint de autenticación.

## Formato de respuesta

Todas las respuestas usan el formato `ApiResponse`:

**Éxito:**
```json
{
    "success": true,
    "message": "Mensaje descriptivo.",
    "data": { ... },
    "errors": null
}
```

**Error:**
```json
{
    "success": false,
    "message": "Descripción del error.",
    "data": null,
    "errors": { ... }
}
```

---

## 1. POST /api/register

### Objetivo

Crear una nueva cuenta de usuario en el sistema.

### Método HTTP

`POST`

### URL

`/api/register`

### Middleware

`throttle:3,1` — Máximo 3 intentos por minuto.

### Headers

```
Content-Type: application/json
Accept: application/json
```

### Body

| Campo | Tipo | Requerido | Validación | Descripción |
|-------|------|-----------|------------|-------------|
| `username` | string | Sí | min:2, max:255, regex:\p{L} | Nombre del usuario |
| `lastname` | string | Sí | min:2, max:255, regex:\p{L} | Apellido del usuario |
| `email` | string | Sí | email, max:255, unique | Correo electrónico |
| `password` | string | Sí | min:8, max:255, confirmed, regex uppercase, regex lowercase, regex digit | Contraseña |
| `password_confirmation` | string | Sí | Debe coincidir con password | Confirmación |

### Ejemplo Request

```http
POST /api/register HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
    "username": "Juan",
    "lastname": "Pérez",
    "email": "juan@example.com",
    "password": "secret123",
    "password_confirmation": "secret123"
}
```

### Ejemplo Response (201)

```json
{
    "success": true,
    "message": "Usuario registrado exitosamente.",
    "data": {
        "user": {
            "id": 1,
            "username": "Juan",
            "lastname": "Pérez",
            "email": "juan@example.com",
            "role": {
                "id": 4,
                "name": "Comun"
            }
        },
        "token": "1|abc123def456ghi789"
    },
    "errors": null
}
```

### Ejemplo Error (422)

```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["El correo electrónico ya está en uso."]
    }
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 201 | Registro exitoso |
| 422 | Error de validación |

### Notas importantes

- El usuario se crea con el rol "Comun" automáticamente (buscado por slug en DB)
- La contraseña se hashea automáticamente antes de guardarse (cast `hashed`)
- Se retorna un token Sanctum válido para iniciar sesión inmediatamente
- El regex `\p{L}` acepta letras con tilde (é, á, ñ) y caracteres Unicode
- **Rate limiting:** 3 intentos por minuto
- **Complejidad de contraseña:** Debe contener al menos una mayúscula, una minúscula y un número

---

## 2. POST /api/login

### Objetivo

Iniciar sesión y obtener un token de acceso.

### Método HTTP

`POST`

### URL

`/api/login`

### Middleware

`throttle:5,1` — Máximo 5 intentos por minuto.

### Headers

```
Content-Type: application/json
Accept: application/json
```

### Body

| Campo | Tipo | Requerido | Validación | Descripción |
|-------|------|-----------|------------|-------------|
| `email` | string | Sí | email, max:255 | Correo electrónico |
| `password` | string | Sí | min:8, max:255 | Contraseña |

### Ejemplo Request

```http
POST /api/login HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
    "email": "juan@example.com",
    "password": "secret123"
}
```

### Ejemplo Response (200)

```json
{
    "success": true,
    "message": "Inicio de sesión exitoso.",
    "data": {
        "user": {
            "id": 1,
            "username": "Juan",
            "lastname": "Pérez",
            "email": "juan@example.com",
            "role": {
                "id": 4,
                "name": "Comun"
            }
        },
        "token": "2|xyz789abc123def456"
    },
    "errors": null
}
```

### Ejemplo Error (422) — Email no registrado

```json
{
    "message": "No existe un usuario con esa dirección de correo electrónico.",
    "errors": {
        "email": ["No existe un usuario con esa dirección de correo electrónico."]
    }
}
```

### Ejemplo Error (422) — Password incorrecto

```json
{
    "message": "Las credenciales proporcionadas no son correctas.",
    "errors": {
        "password": ["Las credenciales proporcionadas no son correctas."]
    }
}
```

### Ejemplo Error (429) — Demasiados intentos

```json
{
    "message": "Too Many Attempts."
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 200 | Login exitoso |
| 422 | Credenciales inválidas |
| 429 | Demasiados intentos (rate limiting) |

### Notas importantes

- Se retorna un nuevo token en cada login
- Se pueden tener múltiples tokens activos (uno por sesión/dispositivo)
- Si el email no existe, el error es genérico para prevenir user enumeration
- Rate limiting: 5 intentos fallidos por minuto

---

## 3. GET /api/me

### Objetivo

Obtener los datos del usuario autenticado.

### Método HTTP

`GET`

### URL

`/api/me`

### Middleware

`auth:sanctum` — Requiere token válido.

### Headers

```
Accept: application/json
Authorization: Bearer <token>
```

### Body

Ninguno.

### Ejemplo Request

```http
GET /api/me HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
Authorization: Bearer 2|xyz789abc123def456
```

### Ejemplo Response (200)

```json
{
    "success": true,
    "message": "Usuario autenticado.",
    "data": {
        "id": 1,
        "username": "Juan",
        "lastname": "Pérez",
        "email": "juan@example.com",
        "role": {
            "id": 4,
            "name": "Comun"
        }
    },
    "errors": null
}
```

### Ejemplo Error (401)

```json
{
    "success": false,
    "message": "Unauthenticated.",
    "data": null,
    "errors": null
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 200 | Éxito |
| 401 | Token faltante, inválido o expirado |

### Notas importantes

- La respuesta NO incluye el token
- Solo retorna los datos del usuario
- El campo `role` se incluye porque el backend hace eager loading

---

## 4. POST /api/logout

### Objetivo

Cerrar sesión eliminando el token actual.

### Método HTTP

`POST`

### URL

`/api/logout`

### Middleware

`auth:sanctum` — Requiere token válido.

### Headers

```
Accept: application/json
Authorization: Bearer <token>
```

### Body

Ninguno.

### Ejemplo Request

```http
POST /api/logout HTTP/1.1
Host: 127.0.0.1:8000
Accept: application/json
Authorization: Bearer 2|xyz789abc123def456
```

### Ejemplo Response (200)

```json
{
    "success": true,
    "message": "Sesión cerrada correctamente.",
    "data": null,
    "errors": null
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 200 | Logout exitoso |
| 401 | Token inválido |

### Notas importantes

- Solo elimina el token actual (no todos los tokens del usuario)
- Después de cerrar sesión, el token queda invalidado
- Si intentas usar el mismo token de nuevo, recibirás 401

---

## 5. POST /api/change-password

### Objetivo

Cambiar la contraseña del usuario autenticado.

### Método HTTP

`POST`

### URL

`/api/change-password`

### Middleware

`auth:sanctum` — Requiere token válido.

### Headers

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer <token>
```

### Body

| Campo | Tipo | Requerido | Validación | Descripción |
|-------|------|-----------|------------|-------------|
| `current_password` | string | Sí | Debe coincidir con la contraseña actual | Contraseña actual |
| `password` | string | Sí | min:8, max:255, confirmed | Nueva contraseña |
| `password_confirmation` | string | Sí | Debe coincidir con password | Confirmación |

### Ejemplo Request

```http
POST /api/change-password HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json
Authorization: Bearer 2|xyz789abc123def456

{
    "current_password": "secret123",
    "password": "nueva_password123",
    "password_confirmation": "nueva_password123"
}
```

### Ejemplo Response (200)

```json
{
    "success": true,
    "message": "Contraseña actualizada correctamente.",
    "data": null,
    "errors": null
}
```

### Ejemplo Error (422) — Contraseña actual incorrecta

```json
{
    "message": "The current password is incorrect.",
    "errors": {
        "current_password": ["La contraseña actual no es correcta."]
    }
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 200 | Contraseña cambiada |
| 422 | Error de validación |
| 401 | Token inválido |

### Notas importantes

- **TODOS los tokens del usuario se eliminan después del cambio**
- El usuario debe iniciar sesión nuevamente en todos sus dispositivos
- Esto es una medida de seguridad

---

## 6. POST /api/forgot-password

### Objetivo

Solicitar un enlace de recuperación de contraseña.

### Método HTTP

`POST`

### URL

`/api/forgot-password`

### Middleware

`throttle:3,1` — Máximo 3 intentos por minuto.

### Headers

```
Content-Type: application/json
Accept: application/json
```

### Body

| Campo | Tipo | Requerido | Validación | Descripción |
|-------|------|-----------|------------|-------------|
| `email` | string | Sí | email, max:255 | Correo electrónico |

### Ejemplo Request

```http
POST /api/forgot-password HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
    "email": "juan@example.com"
}
```

### Ejemplo Response (200)

```json
{
    "success": true,
    "message": "Si el correo existe, se enviará un enlace para restablecer la contraseña.",
    "data": null,
    "errors": null
}
```

### Ejemplo Error (422)

```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["El campo de correo electrónico es obligatorio."]
    }
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 200 | Siempre (ver nota) |
| 422 | Email inválido |

### Notas importantes

- **La respuesta es SIEMPRE 200**, tanto si el email existe como si no
- Esto previene que un atacante descubra qué emails están registrados (user enumeration)
- En desarrollo, el email se guarda en `storage/logs/laravel.log`
- En producción, se envía un email real con el enlace de reset

---

## 7. POST /api/reset-password

### Objetivo

Restablecer la contraseña usando el token del email de recuperación.

### Método HTTP

`POST`

### URL

`/api/reset-password`

### Middleware

`throttle:3,1` — Máximo 3 intentos por minuto.

### Headers

```
Content-Type: application/json
Accept: application/json
```

### Body

| Campo | Tipo | Requerido | Validación | Descripción |
|-------|------|-----------|------------|-------------|
| `token` | string | Sí | string | Token del email |
| `email` | string | Sí | email, max:255 | Correo electrónico |
| `password` | string | Sí | min:8, max:255, confirmed, regex uppercase, regex lowercase, regex digit | Nueva contraseña |
| `password_confirmation` | string | Sí | Debe coincidir con password | Confirmación |

### Ejemplo Request

```http
POST /api/reset-password HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: application/json

{
    "token": "token_recibido_por_email",
    "email": "juan@example.com",
    "password": "nueva_password123",
    "password_confirmation": "nueva_password123"
}
```

### Ejemplo Response (200)

```json
{
    "success": true,
    "message": "Contraseña restablecida correctamente.",
    "data": null,
    "errors": null
}
```

### Ejemplo Error (422) — Token inválido

```json
{
    "success": false,
    "message": "No se pudo restablecer la contraseña. El token puede ser inválido o haber expirado.",
    "data": null,
    "errors": null
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 200 | Contraseña restablecida |
| 422 | Token inválido o expirado |
| 401 | Token de Sanctum inválido (no aplica, es ruta pública) |

### Notas importantes

- **TODOS los tokens del usuario se eliminan después del reset**
- El usuario debe iniciar sesión nuevamente
- El token expira después de 60 minutos (configurado en `config/auth.php`)
- Cada token solo se puede usar una vez

---

## 8. GET /api/auth/google/redirect

### Objetivo

Redirigir al usuario a Google para autorizar la aplicación.

### Método HTTP

`GET`

### URL

`/api/auth/google/redirect`

### Middleware

Ninguno (ruta pública).

### Headers

Ninguno específico.

### Body

Ninguno.

### Ejemplo Request

```http
GET /api/auth/google/redirect HTTP/1.1
Host: 127.0.0.1:8000
```

### Ejemplo Response (302)

```
HTTP/1.1 302 Found
Location: https://accounts.google.com/o/oauth2/auth?client_id=xxx&redirect_uri=xxx&scope=email+profile&state=xxx
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 302 | Redirect a Google |

### Notas importantes

- Este endpoint es para abrir en el navegador, no para llamadas AJAX
- Después de autorizar, Google redirige a `/api/auth/google/callback`
- El parámetro `state` previene ataques CSRF

---

## 9. GET /api/auth/google/callback

### Objetivo

Manejar la respuesta de Google después de la autorización.

### Método HTTP

`GET`

### URL

`/api/auth/google/callback?code=CODIGO&state=STATE`

### Middleware

Ninguno (ruta pública).

### Headers

Ninguno específico.

### Parámetros de query

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `code` | string | Código de autorización de Google |
| `state` | string | Parámetro state para prevenir CSRF |

### Ejemplo Response (200)

```json
{
    "success": true,
    "message": "Inicio de sesión con Google exitoso.",
    "data": {
        "user": {
            "id": 1,
            "username": "Juan García",
            "lastname": "",
            "email": "juan.garcia@gmail.com",
            "role": {
                "id": 4,
                "name": "Comun"
            }
        },
        "token": "3|abc123def456"
    },
    "errors": null
}
```

### Ejemplo Error (422) — Email no verificado

```json
{
    "success": false,
    "message": "Tu cuenta de Google no tiene el correo electrónico verificado.",
    "data": null,
    "errors": {
        "email": ["Tu cuenta de Google no tiene el correo electrónico verificado."]
    }
}
```

### Ejemplo Error (500)

```json
{
    "success": false,
    "message": "Error al autenticar con Google. Intente nuevamente.",
    "data": null,
    "errors": null
}
```

### Código HTTP

| Código | Significado |
|--------|-------------|
| 200 | Login con Google exitoso |
| 422 | Email no verificado o sin email |
| 500 | Error en la autenticación con Google |

### Notas importantes

- Google llama automáticamente a este endpoint después de la autorización
- **Se verifica que el email esté verificado en Google** — si no, retorna 422
- Si el usuario ya existe (mismo email), se vincula su `google_id`
- Si el usuario es nuevo, se crea con el rol "Comun"
- El `lastname` se guarda vacío para usuarios nuevos de Google
- Se genera un password aleatorio (nunca se usará)
- Se busca el email de forma case-insensitive

### Comportamiento por escenario

| Escenario | Comportamiento |
|-----------|----------------|
| Email verificado, no existe en DB | Se crea usuario nuevo con rol "Comun" |
| Email verificado, ya existe en DB | Se vincula `google_id` y `avatar` |
| Email verificado, ya tiene `google_id` | Se actualiza `avatar` |
| Email NO verificado en Google | Retorna 422 |
| Email null en Google | Retorna 422 |
| Error de conexión con Google | Retorna 500 |
| Token de Google inválido | Retorna 500 |

---

## Resumen de códigos HTTP

| Endpoint | Método | Éxito | Error común |
|----------|--------|-------|-------------|
| `/api/register` | POST | 201 | 422 |
| `/api/login` | POST | 200 | 422, 429 |
| `/api/me` | GET | 200 | 401 |
| `/api/logout` | POST | 200 | 401 |
| `/api/change-password` | POST | 200 | 422, 401 |
| `/api/forgot-password` | POST | 200 | 422 |
| `/api/reset-password` | POST | 200 | 422 |
| `/api/auth/google/redirect` | GET | 302 | — |
| `/api/auth/google/callback` | GET | 200 | 422, 500 |
