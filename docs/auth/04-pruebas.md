# 04 — Pruebas

Guía paso a paso para probar manualmente todo el módulo de autenticación.

## Requisitos

- Backend corriendo en `http://127.0.0.1:8000`
- Base de datos con los seeders ejecutados
- Postman o curl instalado

## Orden recomendado de pruebas

```
 1. Registrar usuario
     ↓
 2. Iniciar sesión
     ↓
 3. Guardar token
     ↓
 4. Consultar /me
     ↓
 5. Cambiar contraseña
     ↓
 6. Verificar que el token anterior ya no funciona
     ↓
 7. Iniciar sesión con la nueva contraseña
     ↓
 8. Cerrar sesión
     ↓
 9. Verificar que el token ya no funciona
     ↓
10. Solicitar recuperación de contraseña
     ↓
11. Restablecer contraseña
     ↓
12. Iniciar sesión con la contraseña restablecida
```

## Prueba 1 — Registrar usuario

### Objetivo

Crear una cuenta nueva en el sistema.

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "username": "Juan",
    "lastname": "Pérez",
    "email": "juan@example.com",
    "password": "secret123",
    "password_confirmation": "secret123"
  }'
```

### Respuesta esperada (201)

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
        "token": "1|abc123..."
    },
    "errors": null
}
```

### Qué verificar

- [ ] Código 201
- [ ] `success` es `true`
- [ ] `data.user.email` es "juan@example.com"
- [ ] `data.user.role.name` es "Comun"
- [ ] `data.token` existe y no está vacío
- [ ] **GUARDAR EL TOKEN** para las siguientes pruebas

### Errores posibles

| Error | Causa | Solución |
|-------|-------|----------|
| 422 "email already taken" | Email ya registrado | Usar otro email |
| 422 "password confirmation" | Passwords no coinciden | Verificar password_confirmation |

---

## Prueba 2 — Iniciar sesión

### Objetivo

Obtener un token con credenciales válidas.

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "juan@example.com",
    "password": "secret123"
  }'
```

### Respuesta esperada (200)

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
        "token": "2|xyz789..."
    },
    "errors": null
}
```

### Qué verificar

- [ ] Código 200
- [ ] `data.token` es diferente al del registro
- [ ] **GUARDAR EL NUEVO TOKEN**

### Errores posibles

| Error | Causa | Solución |
|-------|-------|----------|
| 422 "no existe un usuario" | Email incorrecto | Verificar email |
| 422 "credenciales no son correctas" | Password incorrecto | Verificar password |
| 429 "Too Many Attempts" | Demasiados intentos | Esperar 1 minuto |

---

## Prueba 3 — Consultar /me

### Objetivo

Verificar que el token funciona y obtener los datos del usuario.

### Request

```bash
curl -X GET http://127.0.0.1:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 2|xyz789..."
```

Reemplazar `2|xyz789...` con el token real del paso 2.

### Respuesta esperada (200)

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

### Qué verificar

- [ ] Código 200
- [ ] Los datos coinciden con el usuario registrado
- [ ] No hay campo `token` en la respuesta

### Errores posibles

| Error | Causa | Solución |
|-------|-------|----------|
| 401 "Unauthenticated" | Token inválido o faltante | Verificar header Authorization |

---

## Prueba 4 — Cambiar contraseña

### Objetivo

Cambiar la contraseña del usuario autenticado.

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/change-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 2|xyz789..." \
  -d '{
    "current_password": "secret123",
    "password": "nueva_password123",
    "password_confirmation": "nueva_password123"
  }'
```

### Respuesta esperada (200)

```json
{
    "success": true,
    "message": "Contraseña actualizada correctamente.",
    "data": null,
    "errors": null
}
```

### Qué verificar

- [ ] Código 200
- [ ] El token anterior ya no funciona (ver paso 5)

### Errores posibles

| Error | Causa | Solución |
|-------|-------|----------|
| 422 "current password incorrect" | Password actual incorrecto | Verificar current_password |
| 422 "password confirmation" | Passwords no coinciden | Verificar password_confirmation |

---

## Prueba 5 — Verificar que el token ya no funciona

### Objetivo

Confirmar que cambiar la contraseña invalidó el token.

### Request

```bash
curl -X GET http://127.0.0.1:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 2|xyz789..."
```

Usar el MISMO token del paso 2.

### Respuesta esperada (401)

```json
{
    "success": false,
    "message": "Unauthenticated.",
    "data": null,
    "errors": null
}
```

### Qué verificar

- [ ] Código 401
- [ ] El token fue eliminado de la base de datos

---

## Prueba 6 — Iniciar sesión con la nueva contraseña

### Objetivo

Verificar que la nueva contraseña funciona.

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "juan@example.com",
    "password": "nueva_password123"
  }'
```

### Respuesta esperada (200)

```json
{
    "success": true,
    "message": "Inicio de sesión exitoso.",
    "data": {
        "user": { ... },
        "token": "3|abc123..."
    },
    "errors": null
}
```

### Qué verificar

- [ ] Código 200
- [ ] Se obtiene un nuevo token
- [ ] **GUARDAR EL NUEVO TOKEN**

---

## Prueba 7 — Cerrar sesión

### Objetivo

Eliminar el token actual.

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 3|abc123..."
```

### Respuesta esperada (200)

```json
{
    "success": true,
    "message": "Sesión cerrada correctamente.",
    "data": null,
    "errors": null
}
```

### Qué verificar

- [ ] Código 200
- [ ] El token ya no funciona (ver paso 8)

---

## Prueba 8 — Verificar que el token de logout ya no funciona

### Request

```bash
curl -X GET http://127.0.0.1:8000/api/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 3|abc123..."
```

### Respuesta esperada (401)

```json
{
    "success": false,
    "message": "Unauthenticated.",
    "data": null,
    "errors": null
}
```

### Qué verificar

- [ ] Código 401
- [ ] El token fue eliminado

---

## Prueba 9 — Solicitar recuperación de contraseña

### Objetivo

Solicitar un enlace de recuperación.

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/forgot-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "juan@example.com"
  }'
```

### Respuesta esperada (200)

```json
{
    "success": true",
    "message": "Si el correo existe, se enviará un enlace para restablecer la contraseña.",
    "data": null,
    "errors": null
}
```

### Qué verificar

- [ ] Código 200 siempre (incluso si el email no existe)
- [ ] En desarrollo, el token se guarda en `storage/logs/laravel.log`

### Cómo encontrar el token en desarrollo

```bash
# Buscar el token en los logs
cat storage/logs/laravel.log | grep "reseturl"
```

El token se verá algo como: `http://localhost/reset-password?token=abc123...`

---

## Prueba 10 — Restablecer contraseña

### Objetivo

Usar el token del email para crear una nueva contraseña.

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/reset-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "token": "token_del_email",
    "email": "juan@example.com",
    "password": "password_final123",
    "password_confirmation": "password_final123"
  }'
```

### Respuesta esperada (200)

```json
{
    "success": true,
    "message": "Contraseña restablecida correctamente.",
    "data": null,
    "errors": null
}
```

### Qué verificar

- [ ] Código 200
- [ ] Se puede iniciar sesión con la nueva contraseña

### Errores posibles

| Error | Causa | Solución |
|-------|-------|----------|
| 422 "token inválido" | Token incorrecto o expirado | Solicitar nuevo forgot-password |

---

## Prueba 11 — Iniciar sesión con la contraseña restablecida

### Request

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "juan@example.com",
    "password": "password_final123"
  }'
```

### Respuesta esperada (200)

```json
{
    "success": true,
    "message": "Inicio de sesión exitoso.",
    "data": {
        "user": { ... },
        "token": "4|xyz789..."
    },
    "errors": null
}
```

### Qué verificar

- [ ] Código 200
- [ ] Se obtiene un nuevo token

---

## Resumen de pruebas

| # | Prueba | Resultado esperado |
|---|--------|-------------------|
| 1 | Register | 201 + token |
| 2 | Login | 200 + token |
| 3 | Me | 200 + user data |
| 4 | Change password | 200 |
| 5 | Token viejo no funciona | 401 |
| 6 | Login con nueva password | 200 + token |
| 7 | Logout | 200 |
| 8 | Token de logout no funciona | 401 |
| 9 | Forgot password | 200 |
| 10 | Reset password | 200 |
| 11 | Login con password restablecida | 200 + token |

## Errores comunes durante pruebas

### "CSRF token mismatch"

**Causa:** El request está siendo tratado como cookie-based.

**Solución:** Asegurarse de enviar el header `Accept: application/json`.

### 401 en todas las rutas protegidas

**Causa:** Token no enviado o formato incorrecto.

**Solución:** Verificar que el header sea `Authorization: Bearer <token>` (con espacio después de Bearer).

### 429 Too Many Attempts

**Causa:** Demasiados intentos de login.

**Solución:** Esperar 60 segundos antes de intentar de nuevo.

### Token no se invalida después de change-password

**Causa:** Bug en el backend (no debería ocurrir).

**Solución:** Reportar al equipo de backend.
