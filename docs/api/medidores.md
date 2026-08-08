# Medidores

API CRUD completa para la gestion de medidores del sistema Medi-Agua.

## Endpoints

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/medidores` | Listar medidores (paginado) |
| POST | `/api/medidores` | Crear medidor |
| GET | `/api/medidores/{medidor}` | Obtener medidor |
| PUT | `/api/medidores/{medidor}` | Actualizar medidor (completo) |
| PATCH | `/api/medidores/{medidor}` | Actualizar medidor (parcial) |
| DELETE | `/api/medidores/{medidor}` | Eliminar medidor |

**Requisitos:**
- Autenticacion: `Authorization: Bearer <token>`
- Roles: `administrador` o `operador`

---

## Campos de Medidor

| Campo | Requerido | Tipo | Reglas | Descripcion |
|-------|-----------|------|--------|-------------|
| `codigo` | Si | string | unique | Codigo unico del medidor |
| `socio_id` | Si | integer | debe existir en `socios` | ID del socio propietario |
| `observacion` | No | string | nullable, max 1000 | Observaciones sobre el medidor |

### Campos eliminados

Los campos `ubicacion` y `estado` **ya no pertenecen** a la tabla `medidores`. No deben enviarse ni esperarse en las respuestas.

> La informacion de ubicacion y estado corresponde al modelo de Socio o a la estructura definida por el proyecto.

---

## Esquema de base de datos

```text
medidores
├── id           (bigint, PK)
├── codigo       (varchar, unique)
├── socio_id     (bigint, FK → socios.id, cascade delete)
├── observacion  (text, nullable)
├── created_at   (timestamp)
└── updated_at   (timestamp)
```

**Relacion:** `socio_id` referencia `socios.id` con eliminacion en cascada.

---

## Esquema de respuesta

### GET /api/medidores

```json
{
    "success": true,
    "message": "Lista de medidores obtenida correctamente.",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "codigo": "MED-001",
                "socio_id": 1,
                "observacion": "Medidor instalado en zona norte",
                "created_at": "2026-08-08T12:00:00.000000Z",
                "updated_at": "2026-08-08T12:00:00.000000Z",
                "socio": {
                    "id": 1,
                    "nombres": "Maria Elena",
                    "apellidos": "Garcia Lopez",
                    "ci": "12345678"
                }
            }
        ],
        "first_page_url": "http://localhost:8000/api/medidores?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/api/medidores?page=1",
        "links": [...],
        "next_page_url": null,
        "path": "http://localhost:8000/api/medidores",
        "per_page": 10,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    }
}
```

**Paginacion:** 10 registros por pagina.

**Relaciones incluidas:** `socio`.

---

### POST /api/medidores

**Request:**

```json
{
    "codigo": "MED-001",
    "socio_id": 1,
    "observacion": "Medidor instalado recientemente"
}
```

**Respuesta (201):**

```json
{
    "success": true,
    "message": "Medidor creado correctamente.",
    "data": {
        "id": 1,
        "codigo": "MED-001",
        "socio_id": 1,
        "observacion": "Medidor instalado recientemente",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:00:00.000000Z",
        "socio": {
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
}
```

---

### GET /api/medidores/{medidor}

**Respuesta (200):**

```json
{
    "success": true,
    "message": "Medidor obtenido correctamente.",
    "data": {
        "id": 1,
        "codigo": "MED-001",
        "socio_id": 1,
        "observacion": "Medidor instalado en zona norte",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:00:00.000000Z",
        "socio": {
            "id": 1,
            "nombres": "Maria Elena",
            "apellidos": "Garcia Lopez",
            "ci": "12345678"
        },
        "lecturas": []
    }
}
```

**Relaciones incluidas:** `socio`, `lecturas`.

---

### PUT /api/medidores/{medidor}

Actualizacion completa. Todos los campos requeridos deben enviarse.

**Request:**

```json
{
    "codigo": "MED-002",
    "socio_id": 1,
    "observacion": "Codigo actualizado"
}
```

**Respuesta (200):**

```json
{
    "success": true,
    "message": "Medidor actualizado correctamente.",
    "data": {
        "id": 1,
        "codigo": "MED-002",
        "socio_id": 1,
        "observacion": "Codigo actualizado",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:30:00.000000Z",
        "socio": {
            "id": 1,
            "nombres": "Maria Elena",
            "apellidos": "Garcia Lopez",
            "ci": "12345678"
        }
    }
}
```

---

### PATCH /api/medidores/{medidor}

Actualizacion parcial. Solo enviar los campos a modificar.

**Request:**

```json
{
    "observacion": "Solo cambio observacion"
}
```

**Respuesta (200):**

```json
{
    "success": true,
    "message": "Medidor actualizado correctamente.",
    "data": {
        "id": 1,
        "codigo": "MED-001",
        "socio_id": 1,
        "observacion": "Solo cambio observacion",
        "created_at": "2026-08-08T12:00:00.000000Z",
        "updated_at": "2026-08-08T12:30:00.000000Z",
        "socio": {
            "id": 1,
            "nombres": "Maria Elena",
            "apellidos": "Garcia Lopez",
            "ci": "12345678"
        }
    }
}
```

---

### DELETE /api/medidores/{medidor}

**Respuesta (204):** Sin body.

**Efecto en cascada:** Al eliminar un medidor, se eliminan todas sus lecturas asociadas.

---

## Codigos HTTP

| Codigo | Descripcion |
|--------|-------------|
| 200 | Operacion exitosa (GET, PUT, PATCH) |
| 201 | Medidor creado (POST) |
| 204 | Medidor eliminado (DELETE) |
| 401 | No autenticado |
| 403 | Sin permisos (rol no autorizado) |
| 404 | Medidor no encontrado |
| 422 | Error de validacion |

---

## Validaciones negativas

| # | Prueba | Body | Status | Campo afectado |
|---|--------|------|--------|----------------|
| 1 | codigo duplicado | `{ "codigo": "MED-001", "socio_id": 1 }` (ya existe) | 422 | `codigo` |
| 2 | socio inexistente | `{ "codigo": "MED-NEW", "socio_id": 99999 }` | 422 | `socio_id` |
| 3 | codigo faltante | `{ "socio_id": 1 }` | 422 | `codigo` |
| 4 | socio_id faltante | `{ "codigo": "MED-X" }` | 422 | `socio_id` |
| 5 | medidor inexistente | GET `/api/medidores/99999` | 404 | - |
| 6 | observacion nullable | `{ "codigo": "MED-X", "socio_id": 1 }` | 201 | Creado sin observacion |

### Comportamiento con campos no validados

Si se envian campos que no estan en la validacion (`ubicacion`, `estado`, etc.), Laravel los ignora silenciosamente y el medidor se crea con 201. Estos campos no se almacenan ni aparecen en la respuesta.

| # | Prueba | Body enviado | Resultado |
|---|--------|-------------|-----------|
| 7 | enviado `ubicacion` | `{ "codigo": "MED-X", "socio_id": 1, "ubicacion": "Calle" }` | 201 (campo ignorado) |
| 8 | enviado `estado` | `{ "codigo": "MED-X", "socio_id": 1, "estado": "activo" }` | 201 (campo ignorado) |

> **Nota tecnica:** Laravel aplica mass assignment solo a los campos definidos en `$fillable` del modelo. Los campos no incluidos simplemente no se procesan.

---

## Relaciones Eloquent

```text
Medidor
  │
  ├── belongsTo Socio
  └── hasMany Lecturas
```

**Eliminacion en cascada:**
- Al eliminar un Socio, se eliminan todos sus Medidores.
- Al eliminar un Medidor, se eliminan todas sus Lecturas.
