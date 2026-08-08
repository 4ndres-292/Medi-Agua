# Autenticacion

Toda la API de Medi-Agua utiliza autenticacion basada en tokens (Laravel Sanctum). No se usan sesiones de navegador ni cookies.

## Login

### Request

| Campo | Valor |
|-------|-------|
| Metodo | `POST` |
| URL | `http://localhost:8000/api/login` |
| Content-Type | `application/json` |
| Auth | No requerido |
| Throttle | 5 intentos por minuto |

### Body

```json
{
    "email": "usuario@example.com",
    "password": "Password123"
}
```

### Campos

| Campo | Requerido | Tipo | Validacion |
|-------|-----------|------|------------|
| email | Si | string | email, max 255 |
| password | Si | string | min 8, max 255 |

### Respuesta exitosa (200)

```json
{
    "success": true,
    "message": "Inicio de sesion exitoso.",
    "data": {
        "user": {
            "id": 1,
            "username": "Juan",
            "lastname": "Perez",
            "email": "juan@example.com",
            "rol": {
                "id": 1,
                "slug": "administrador",
                "name": "Administrador"
            }
        },
        "token": "1|abc123def456..."
    }
}
```

### Credenciales de prueba

| Campo | Valor |
|-------|-------|
| Email | `choquecahuanaandresoriginal@gmail.com` |
| Password | `12345678` |
| Rol | Administrador |

> **Nota:** Estas credenciales son solo para entorno de desarrollo local.

### Errores

| Codigo | Condicion |
|--------|-----------|
| 422 | Email o password incorrectos |
| 429 | Mas de 5 intentos por minuto |

---

## Como usar el token

### Obtener el token

1. Enviar `POST /api/login` con email y password.
2. Copiar el valor de `data.token` de la respuesta.

### Usar el token en requests

Agregar el header `Authorization` en cada request protegido:

```
Authorization: Bearer 1|abc123def456...
```

### En Postman

1. Ir a la pestana **Authorization** del request.
2. Seleccionar tipo **Bearer Token**.
3. Pegar el token en el campo **Token**.

O usar header manual:

```
Authorization: Bearer {{token}}
```

---

## Roles requeridos

| Recurso | Roles permitidos |
|---------|------------------|
| Usuarios | `administrador` |
| Roles | `administrador` |
| Socios | `administrador`, `operador` |
| Medidores | `administrador`, `operador` |
| Lecturas | `administrador`, `operador` |
| Tarifas | `administrador`, `operador` |
| Facturas | `administrador`, `cajero` |
| Pagos | `administrador`, `cajero` |
| Notificaciones | Todos los autenticados |
| Reportes | Todos los autenticados |

---

## Otros endpoints de autenticacion

| Metodo | Ruta | Descripcion | Auth |
|--------|------|-------------|------|
| POST | `/api/register` | Crear cuenta nueva | No |
| GET | `/api/me` | Obtener usuario autenticado | Si |
| POST | `/api/logout` | Cerrar sesion | Si |
| POST | `/api/change-password` | Cambiar contrasena | Si |
| POST | `/api/forgot-password` | Solicitar recuperacion | No |
| POST | `/api/reset-password` | Restablecer contrasena | No |

La documentacion completa de autenticacion se encuentra en [`docs/auth/`](../auth/README.md).
