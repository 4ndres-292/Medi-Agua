# Módulo de Autenticación — Medi-Agua

## Descripción

El módulo de autenticación de Medi-Agua gestiona el ciclo completo de vida de las sesiones de usuario: registro, inicio de sesión, cierre de sesión, recuperación de contraseña y autenticación con Google OAuth.

Toda la autenticación se basa en **tokens** (Laravel Sanctum). No se utilizan sesiones de navegador ni cookies.

## Objetivo

Providar a los desarrolladores frontend todo lo necesario para consumir las APIs de autenticación de forma correcta, segura y eficiente.

## Tecnologías utilizadas

| Capa | Tecnología | Versión |
|------|------------|---------|
| Backend | Laravel | 13.x |
| Auth tokens | Laravel Sanctum | 4.3 |
| OAuth | Laravel Socialite | 5.29 |
| Base de datos | PostgreSQL | 15+ |
| Frontend | React | 19.x |
| HTTP Client | Axios | 1.x |
| Build Tool | Vite | 8.x |
| Language | TypeScript | 5.x |
| CSS | Tailwind CSS | 4.x |

## Arquitectura resumida

```
Frontend (React)
    │
    │  HTTP + Bearer Token
    │
    ▼
AuthController ──▶ AuthService ──▶ User Model ──▶ PostgreSQL
    │                  │
    │                  ├── Login
    │                  ├── Register
    │                  ├── Google OAuth (Socialite)
    │                  └── Password Broker
    │
    ▼
ApiResponse ──▶ JSON
```

## Endpoints disponibles

| # | Método | Ruta | Descripción | Auth |
|---|--------|------|-------------|------|
| 1 | POST | `/api/register` | Crear cuenta nueva | No |
| 2 | POST | `/api/login` | Iniciar sesión | No |
| 3 | GET | `/api/me` | Obtener usuario autenticado | Sí |
| 4 | POST | `/api/logout` | Cerrar sesión | Sí |
| 5 | POST | `/api/change-password` | Cambiar contraseña | Sí |
| 6 | POST | `/api/forgot-password` | Solicitar recuperación | No |
| 7 | POST | `/api/reset-password` | Restablecer contraseña | No |
| 8 | GET | `/api/auth/google/redirect` | Redirigir a Google | No |
| 9 | GET | `/api/auth/google/callback` | Callback de Google | No |

## Estructura de la documentación

| Archivo | Contenido |
|---------|-----------|
| [01-instalacion.md](01-instalacion.md) | Cómo levantar el proyecto desde cero |
| [02-arquitectura.md](02-arquitectura.md) | Arquitectura interna del módulo |
| [03-endpoints.md](03-endpoints.md) | Documentación completa de cada endpoint |
| [04-pruebas.md](04-pruebas.md) | Guía para probar cada endpoint manualmente |
| [05-frontend.md](05-frontend.md) | Guía para consumir las APIs desde React |
| [06-google-oauth.md](06-google-oauth.md) | Integración completa con Google OAuth |
| [07-postman.md](07-postman.md) | Cómo usar la colección de Postman |
| [08-tests.md](08-tests.md) | Suite de tests y cómo ejecutarlos |

## Flujo general de autenticación

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   USUARIO    │     │   FRONTEND   │     │   BACKEND    │
└──────┬───────┘     └──────┬───────┘     └──────┬───────┘
       │                    │                    │
       │  1. Ingresa email  │                    │
       │  y password        │                    │
       │───────────────────▶│                    │
       │                    │  POST /login       │
       │                    │───────────────────▶│
       │                    │                    │
       │                    │  { user, token }   │
       │                    │◀───────────────────│
       │                    │                    │
       │  2. Guarda token   │                    │
       │  en localStorage   │                    │
       │◀───────────────────│                    │
       │                    │                    │
       │  3. Navega a /     │                    │
       │  dashboard         │                    │
       │                    │  GET /me           │
       │                    │  Bearer: token     │
       │                    │───────────────────▶│
       │                    │                    │
       │                    │  { user }          │
       │                    │◀───────────────────│
       │                    │                    │
       │  4. Ve datos       │                    │
       │  del usuario       │                    │
       │◀───────────────────│                    │
```

## Orden recomendado de lectura

1. **[01-instalacion.md](01-instalacion.md)** — Primero levanta el proyecto
2. **[03-endpoints.md](03-endpoints.md)** — Conoce qué endpoints existen
3. **[04-pruebas.md](04-pruebas.md)** — Prueba cada endpoint manualmente
4. **[05-frontend.md](05-frontend.md)** — Integra las APIs en tu frontend
5. **[02-arquitectura.md](02-arquitectura.md)** — Entiende cómo funciona internamente
6. **[06-google-oauth.md](06-google-oauth.md)** — Configura Google OAuth si es necesario
7. **[07-postman.md](07-postman.md)** — Usa Postman para pruebas rápidas
8. **[08-tests.md](08-tests.md)** — Ejecuta y entiende los tests

## Credenciales de prueba

```
Email:    choquecahuanaandresoriginal@gmail.com
Password: 12345678
Rol:      Administrador
```

## Puerto del backend

```
http://127.0.0.1:8000
```

Todas las rutas son relativas a `/api`. Por ejemplo, el endpoint de login es `http://127.0.0.1:8000/api/login`.
