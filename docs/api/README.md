# Documentacion API — Medi-Agua

Documentacion de las APIs REST del sistema Medi-Agua para gestion de agua potable.

## Base URL

```
http://localhost:8000/api
```

## Formato de respuestas

Todas las respuestas usan el formato `ApiResponse`:

**Exito:**
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
    "message": "Descripcion del error.",
    "data": null,
    "errors": { ... }
}
```

## Autenticacion

Todos los endpoints protegidos requieren el header:

```
Authorization: Bearer <token>
```

Ver [Autenticacion](autenticacion.md) para obtener el token.

## Documentacion disponible

| Documento | Contenido |
|-----------|-----------|
| [Autenticacion](autenticacion.md) | Login, token, roles |
| [Socios](socios.md) | CRUD completo de socios |
| [Medidores](medidores.md) | CRUD completo de medidores |

## Codigos HTTP utilizados

| Codigo | Significado |
|--------|-------------|
| 200 | Operacion exitosa |
| 201 | Recurso creado |
| 204 | Recurso eliminado (sin body) |
| 401 | No autenticado |
| 403 | Sin permisos |
| 404 | Recurso no encontrado |
| 422 | Error de validacion |
| 429 | Demasiadas requests (rate limit) |

## Orden recomendado de pruebas

```
 1. POST /api/login          → Obtener token
 2. POST /api/socios          → Crear socio
 3. GET  /api/socios          → Listar socios
 4. GET  /api/socios/{id}     → Obtener socio
 5. POST /api/medidores       → Crear medidor (usando socio_id)
 6. GET  /api/medidores       → Listar medidores
 7. GET  /api/medidores/{id}  → Obtener medidor
 8. PUT  /api/medidores/{id}  → Actualizar medidor
 9. PATCH /api/medidores/{id} → Actualizar parcialmente
10. DELETE /api/medidores/{id} → Eliminar medidor
11. DELETE /api/socios/{id}    → Eliminar socio
```

## Configuracion de Postman

### Variables de entorno

| Nombre | Valor |
|--------|-------|
| `base_url` | `http://localhost:8000` |
| `token` | *(obtener del login)* |

### Uso en requests

```
{{base_url}}/api/socios
```

### Header de autenticacion

```
Authorization: Bearer {{token}}
Content-Type: application/json
```

## Roles del sistema

| Rol | Permisos |
|-----|----------|
| `administrador` | Acceso total |
| `operador` | Socios, medidores, lecturas, tarifas |
| `cajero` | Facturas, pagos |
| `comun` | Solo lectura |
