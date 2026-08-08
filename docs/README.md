# Documentacion — Medi-Agua

Sistema ERP web para la gestion integral de agua potable de una OTB.

## Estructura de documentacion

```
docs/
├── README.md                    ← Este archivo (indice principal)
│
├── api/                         ← Documentacion de APIs
│   ├── README.md                ← Indice de APIs
│   ├── autenticacion.md         ← Login y tokens
│   ├── socios.md                ← CRUD Socios
│   └── medidores.md             ← CRUD Medidores
│
└── auth/                        ← Documentacion del modulo de autenticacion
    ├── README.md                ← Indice del modulo
    ├── 01-instalacion.md        ← Como levantar el proyecto
    ├── 02-arquitectura.md       ← Arquitectura interna
    ├── 03-endpoints.md          ← Endpoints de autenticacion
    ├── 04-pruebas.md            ← Guia de pruebas manuales
    ├── 05-frontend.md           ← Consumo desde React
    ├── 06-google-oauth.md       ← Integracion Google OAuth
    ├── 07-postman.md            ← Coleccion de Postman
    └── 08-tests.md              ← Suite de tests
```

## Documentacion rapida

### Para consumir las APIs

1. [Autenticacion](api/autenticacion.md) — Obtener token
2. [Socios](api/socios.md) — CRUD de socios
3. [Medidores](api/medidores.md) — CRUD de medidores

### Para configurar el proyecto

1. [Instalacion](auth/01-instalacion.md) — Levantar desde cero
2. [Arquitectura](auth/02-arquitectura.md) — Entender la estructura

### Para probar

1. [Postman API](api/README.md#configuracion-de-postman) — Variables y headers
2. [Postman Auth](auth/07-postman.md) — Coleccion de autenticacion
3. [Tests](auth/08-tests.md) — Ejecutar suite completa

## Stack tecnologico

| Capa | Tecnologia | Version |
|------|------------|---------|
| Backend | Laravel | 13.x |
| Frontend | React | 19.x |
| Auth | Laravel Sanctum | 4.x |
| Database | PostgreSQL | 15+ |
| HTTP Client | Axios | 1.x |

## Endpoints disponibles

### Autenticacion (publicos)

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| POST | `/api/login` | Iniciar sesion |
| POST | `/api/register` | Crear cuenta |
| POST | `/api/forgot-password` | Recuperar contrasena |
| POST | `/api/reset-password` | Restablecer contrasena |

### Socios (admin, operador)

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/socios` | Listar |
| POST | `/api/socios` | Crear |
| GET | `/api/socios/{id}` | Obtener |
| PUT | `/api/socios/{id}` | Actualizar |
| PATCH | `/api/socios/{id}` | Actualizar parcial |
| DELETE | `/api/socios/{id}` | Eliminar |

### Medidores (admin, operador)

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/medidores` | Listar |
| POST | `/api/medidores` | Crear |
| GET | `/api/medidores/{id}` | Obtener |
| PUT | `/api/medidores/{id}` | Actualizar |
| PATCH | `/api/medidores/{id}` | Actualizar parcial |
| DELETE | `/api/medidores/{id}` | Eliminar |

## Credenciales de prueba

```
Email:    usuario@example.com
Password: Password123
Rol:      Administrador
```

> Solo para entorno de desarrollo local.

## Puerto del backend

```
http://localhost:8000
```
