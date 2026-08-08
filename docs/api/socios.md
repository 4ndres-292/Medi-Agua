# Socios

API CRUD completa para la gestion de socios del sistema Medi-Agua.

## Endpoints

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/socios` | Listar socios (paginado) |
| POST | `/api/socios` | Crear socio |
| GET | `/api/socios/{socio}` | Obtener socio |
| PUT | `/api/socios/{socio}` | Actualizar socio (completo) |
| PATCH | `/api/socios/{socio}` | Actualizar socio (parcial) |
| DELETE | `/api/socios/{socio}` | Eliminar socio |

**Requisitos:**
- Autenticacion: `Authorization: Bearer <token>`
- Roles: `administrador` o `operador`

---

## Campos de Socio

| Campo | Requerido | Tipo | Reglas | Descripcion |
|-------|-----------|------|--------|-------------|
| `nombres` | Si | string | letras y espacios, max 255 | Nombres del socio |
| `apellidos` | Si | string | letras y espacios, max 255 | Apellidos del socio |
| `ci` | Si | string | solo digitos, unique | Carnet de identidad |
| `telefono` | No | string | solo digitos, max 20 | Numero de telefono |
| `direccion` | No | string | max 255 | Direccion del socio |
| `estado` | No | string | `activo` o `inactivo` | Estado del socio (default: `activo`) |

> **Nota sobre `ci`:** Se almacena como `string` para conservar ceros iniciales (ej: `07025489`).

---

## Esquema de respuesta

### GET /api/socios

```json
{
    "success": true,
    "message": "Lista de socios obtenida correctamente.",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "nombres": "Maria Elena",
                "apellidos": "Garcia Lopez",
                "ci": "12345678",
                "telefono": "76543210",
                "direccion": "Av. Principal No. 123",
                "estado": "activo",
                "created_at": "2026-08-08T12:00:00.000000Z",
                "updated_at": "2026-08-08T12:00:00.000000Z",
                "medidores": []
            }
        ],
        "first_page_url": "http://localhost:8000/api/socios?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/api/socios?page=1",
        "links": [...],
        "next_page_url": null,
        "path": "http://localhost:8000/api/socios",
        "per_page": 10,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    }
}
```

**Paginacion:** 10 registros por pagina.

**Relaciones incluidas:** `medidores`.

---

### POST /api/socios

**Request:**

```json
{
    "nombres": "Maria Elena",
    "apellidos": "Garcia Lopez",
    "ci": "12345678",
    "telefono": "76543210",
    "direccion": "Av. Principal No. 123",
    "estado": "activo"
}
```

**Respuesta (201):**

```json
{
    "success": true,
    "message": "Socio creado correctamente.",
    "data": {
        "id": 1,
        "nombres": "Maria Elena",
        "apellidos": "Garcia Lopez",
        "ci": "12345678",
        "telefono": "76543210",
        "direccion": "Av. Principal No. 123",
        "estado": "activo",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:00:00.000000Z"
    }
}
```

---

### GET /api/socios/{socio}

**Respuesta (200):**

```json
{
    "success": true,
    "message": "Socio obtenido correctamente.",
    "data": {
        "id": 1,
        "nombres": "Maria Elena",
        "apellidos": "Garcia Lopez",
        "ci": "12345678",
        "telefono": "76543210",
        "direccion": "Av. Principal No. 123",
        "estado": "activo",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:00:00.000000Z",
        "medidores": [],
        "facturas": [],
        "notificaciones": []
    }
}
```

**Relaciones incluidas:** `medidores`, `facturas`, `notificaciones`.

---

### PUT /api/socios/{socio}

Actualizacion completa. Todos los campos requeridos deben enviarse.

**Request:**

```json
{
    "nombres": "Ana Maria",
    "apellidos": "Garcia Lopez",
    "ci": "12345678",
    "telefono": "65432109",
    "direccion": "Calle Secundaria No. 456",
    "estado": "inactivo"
}
```

**Respuesta (200):**

```json
{
    "success": true,
    "message": "Socio actualizado correctamente.",
    "data": {
        "id": 1,
        "nombres": "Ana Maria",
        "apellidos": "Garcia Lopez",
        "ci": "12345678",
        "telefono": "65432109",
        "direccion": "Calle Secundaria No. 456",
        "estado": "inactivo",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:30:00.000000Z"
    }
}
```

---

### PATCH /api/socios/{socio}

Actualizacion parcial. Solo enviar los campos a modificar.

**Request:**

```json
{
    "telefono": "99999999"
}
```

**Respuesta (200):**

```json
{
    "success": true,
    "message": "Socio actualizado correctamente.",
    "data": {
        "id": 1,
        "nombres": "Maria Elena",
        "apellidos": "Garcia Lopez",
        "ci": "12345678",
        "telefono": "99999999",
        "direccion": "Av. Principal No. 123",
        "estado": "activo",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:30:00.000000Z"
    }
}
```

---

### DELETE /api/socios/{socio}

**Respuesta (204):** Sin body.

**Efecto en cascada:** Al eliminar un socio, se eliminan todos sus medidores asociados.

---

## Codigos HTTP

| Codigo | Descripcion |
|--------|-------------|
| 200 | Operacion exitosa (GET, PUT, PATCH) |
| 201 | Socio creado (POST) |
| 204 | Socio eliminado (DELETE) |
| 401 | No autenticado |
| 403 | Sin permisos (rol no autorizado) |
| 404 | Socio no encontrado |
| 422 | Error de validacion |

---

## Validaciones negativas

| # | Prueba | Body | Status | Campo afectado |
|---|--------|------|--------|----------------|
| 1 | CI duplicado | `{ "ci": "12345678" }` (ya existe) | 422 | `ci` |
| 2 | CI con letras | `{ "ci": "ABC123" }` | 422 | `ci` |
| 3 | nombres invalidos | `{ "nombres": "Maria123" }` | 422 | `nombres` |
| 4 | apellidos invalidos | `{ "apellidos": "Garcia123" }` | 422 | `apellidos` |
| 5 | campos requeridos faltantes | `{}` | 422 | `nombres`, `apellidos`, `ci` |
| 6 | estado invalido | `{ "estado": "suspendido" }` | 422 | `estado` |
| 7 | socio inexistente | GET `/api/socios/99999` | 404 | - |

---

## Relaciones Eloquent

```text
Socio
  │
  ├── hasMany Medidores
  ├── hasMany Facturas
  └── hasMany Notificaciones
```

**Eliminacion en cascada:** Al eliminar un Socio, se eliminan automaticamente sus Medidores (configurado en la migracion con `onDelete('cascade')`).
