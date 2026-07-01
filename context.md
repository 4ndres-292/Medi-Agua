# Medi-Agua - Documentación Técnica

## Descripción General

**Medi-Agua** es un sistema ERP web para la gestión integral de agua potable de una OTB (Organización de Trabajadores Barriales). Permite administrar socios, medidores, lecturas, facturación, pagos y reportes.

## Arquitectura

```
┌─────────────────────┐     HTTP/JSON     ┌─────────────────────┐
│   Frontend (React)  │ ◄──────────────► │   Backend (Laravel)  │
│   Vite + TypeScript │    Bearer Token   │   Sanctum Tokens     │
│   Puerto 5173       │                   │   Puerto 8000        │
└─────────────────────┘                   └─────────────────────┘
                                                   │
                                          ┌────────┴────────┐
                                          │   PostgreSQL     │
                                          │   medi_agua_db   │
                                          └─────────────────┘
```

### Stack Tecnológico

| Capa | Tecnología | Versión |
|------|------------|---------|
| Backend | Laravel | 13.x |
| Frontend | React | 19.x |
| Build Tool | Vite | 8.x |
| Language | TypeScript | 5.x |
| CSS | Tailwind CSS | 4.x |
| Auth | Laravel Sanctum | 4.x |
| Database | PostgreSQL | 15+ |
| HTTP Client | Axios | 1.x |

## Flujo de Autenticación

### 1. Login
```
POST /api/login
├── Valida credenciales (email + password)
├── Auth::guard('web')->once() → NO crea sesión
├── createToken('auth-token') → genera Personal Access Token
└── Retorna { user, token }
```

### 2. Requests Autenticados
```
GET /api/me
├── Header: Authorization: Bearer <token>
├── Sanctum valida token en personal_access_tokens
├── Retorna usuario autenticado
└── Sin sesión Laravel, sin cookies
```

### 3. Logout
```
POST /api/logout
├── currentAccessToken()->delete()
├── Token eliminado de DB
└── Frontend limpia localStorage
```

## Estructura de Carpetas

### Backend (Laravel)
```
app/
├── Http/Controllers/
│   ├── AuthController.php      # Login, logout, me
│   ├── UserController.php      # CRUD Usuarios
│   ├── RolController.php       # CRUD Roles
│   ├── SocioController.php     # CRUD Socios
│   ├── MedidorController.php   # CRUD Medidores
│   ├── LecturaController.php   # CRUD Lecturas
│   ├── TarifaController.php    # CRUD Tarifas
│   ├── FacturaController.php   # CRUD Facturas
│   ├── PagoController.php      # CRUD Pagos
│   ├── NotificacionController.php
│   └── ReportesController.php  # Reportes
├── Models/
│   ├── User.php
│   ├── Rol.php
│   ├── Socio.php
│   ├── Medidor.php
│   ├── Lectura.php
│   ├── Tarifa.php
│   ├── Factura.php
│   ├── Pago.php
│   └── Notificacion.php
routes/
├── api.php                     # 48 rutas API
└── web.php                     # SPA catch-all
config/
├── sanctum.php                 # Token config
└── session.php                 # Session config
```

### Frontend (React)
```
resources/js/
├── app.tsx                     # Entry point + 18 rutas
├── services/
│   └── api.ts                  # Axios + interceptors
├── components/
│   ├── auth/
│   │   ├── AuthGuard.tsx       # Validación token
│   │   └── Login.tsx           # Formulario login
│   └── layout/
│       ├── Layout.tsx          # Navbar + children + Footer
│       ├── Navbar.tsx          # Menú ERP
│       └── Footer.tsx          # Copyright
└── pages/
    ├── Home.tsx                # Landing page
    ├── Dashboard.tsx           # Stats
    ├── users/Users.tsx         # CRUD Usuarios
    ├── roles/Roles.tsx         # CRUD Roles
    ├── socios/Socios.tsx       # CRUD Socios
    ├── medidores/Medidores.tsx # CRUD Medidores
    ├── lecturas/Lecturas.tsx   # CRUD Lecturas
    ├── tarifas/Tarifas.tsx     # CRUD Tarifas
    ├── facturas/Facturas.tsx   # CRUD Facturas
    ├── pagos/Pagos.tsx         # CRUD Pagos
    ├── notificaciones/         # CRUD Notificaciones
    ├── reportes/               # 3 reportes
    └── usuario/                # Perfil + Config
```

## Sistema de Roles

| ID | Rol | Descripción |
|----|-----|-------------|
| 1 | Administrador | Acceso total al sistema |
| 2 | Operador | Gestión de socios y lecturas |
| 3 | Cajero | Gestión de pagos |
| 4 | Consulta | Solo lectura |

## Comunicación Frontend ↔ Backend

### Request Flow
```
React Component
    ↓
api.get('/socios')
    ↓
Interceptor: agrega Bearer token
    ↓
HTTP GET http://127.0.0.1:8000/api/socios
    ↓
Laravel: auth:sanctum middleware
    ↓
Sanctum: valida token → retorna usuario
    ↓
Controller: retorna datos
    ↓
Response JSON
```

### Response Format
```json
{
    "success": true,
    "message": "Operación exitosa",
    "data": { ... }
}
```

### Error Format
```json
{
    "success": false,
    "message": "Error description"
}
```

## Estado Actual

### ✅ Funcional
- Login con Sanctum tokens
- Logout que invalida token
- 48 rutas API protegidas
- CRUD completo para 9 entidades
- Frontend con 18 rutas
- Navegación ERP completa
- Tablas con datos reales
- Validaciones backend

### 🔧 En Desarrollo
- Formularios CRUD (solo tablas, sin modales)
- Reportes con datos reales
- Dashboard con estadísticas dinámicas
- Perfil de usuario editable

### 📋 Pendiente
- Paginación en tablas
- Búsqueda y filtros
- Exportar PDF
- Notificaciones push
- Dashboard con gráficos

## Credenciales de Prueba

```
Email: choquecahuanaandresoriginal@gmail.com
Password: 12345678
Rol: Administrador
```

## Endpoints API

### Autenticación
| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | /api/login | Iniciar sesión |
| POST | /api/logout | Cerrar sesión |
| GET | /api/me | Usuario autenticado |

### CRUD (protegidos con auth:sanctum)
| Recurso | Rutas |
|---------|-------|
| Users | GET/POST /api/users, GET/PUT/DELETE /api/users/{id} |
| Roles | GET/POST /api/roles, GET/PUT/DELETE /api/roles/{id} |
| Socios | GET/POST /api/socios, GET/PUT/DELETE /api/socios/{id} |
| Medidores | GET/POST /api/medidores, GET/PUT/DELETE /api/medidores/{id} |
| Lecturas | GET/POST /api/lecturas, GET/PUT/DELETE /api/lecturas/{id} |
| Tarifas | GET/POST /api/tarifas, GET/PUT/DELETE /api/tarifas/{id} |
| Facturas | GET/POST /api/facturas, GET/PUT/DELETE /api/facturas/{id} |
| Pagos | GET/POST /api/pagos, GET/PUT/DELETE /api/pagos/{id} |
| Notificaciones | GET/POST /api/notificaciones, GET/PUT/DELETE /api/notificaciones/{id} |

### Reportes
| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | /api/reportes/ingresos | Ingresos por período |
| GET | /api/reportes/deudores | Socios con deudas |
| GET | /api/reportes/consumo | Consumo por socio |
