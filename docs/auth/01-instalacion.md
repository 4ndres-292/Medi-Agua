# 01 — Instalación

Guía paso a paso para levantar el proyecto Medi-Agua desde cero.

## Requisitos previos

| Requisito | Versión mínima | Versión utilizada |
|-----------|----------------|-------------------|
| PHP | 8.3 | 8.3+ |
| Composer | 2.x | 2.x |
| Node.js | 18+ | 20+ |
| npm | 9+ | 10+ |
| PostgreSQL | 15 | 15+ |

## 1. Clonar el proyecto

```bash
git clone https://github.com/tu-usuario/medi-agua.git
cd medi-agua
```

## 2. Instalar dependencias de PHP

```bash
composer install
```

Esto instala Laravel 13, Sanctum, Socialite y todas las dependencias del backend.

## 3. Instalar dependencias de JavaScript

```bash
npm install
```

Esto instala React 19, Vite 8, Tailwind CSS 4, Axios y todas las dependencias del frontend.

## 4. Configurar el archivo .env

```bash
cp .env.example .env
```

Abrir `.env` y configurar:

```env
# App
APP_NAME=Medi-Agua
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Base de datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medi_agua
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db

# Sesiones (opcional, no se usa en token-based)
SESSION_DRIVER=database

# Mail (en desarrollo, los emails se guardan en logs)
MAIL_MAILER=log

# Google OAuth (opcional)
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/auth/google/callback
```

## 5. Generar la APP_KEY

```bash
php artisan key:generate
```

Esto genera una clave de encriptación única para tu instalación.

## 6. Crear la base de datos

Crear la base de datos `medi_agua` en PostgreSQL antes de ejecutar las migraciones:

```sql
CREATE DATABASE medi_agua;
```

O usar el nombre que hayas configurado en `.env`.

## 7. Ejecutar migraciones

```bash
php artisan migrate
```

Esto crea todas las tablas necesarias:

- `users` — Usuarios del sistema
- `roles` — Roles (Administrador, Contador, Lecturador, Comun)
- `personal_access_tokens` — Tokens de Sanctum
- `password_reset_tokens` — Tokens de recuperación de contraseña
- `sessions` — Sesiones (opcional)
- Tablas de negocios: `socios`, `medidores`, `lecturas`, etc.

## 8. Ejecutar seeders

```bash
php artisan db:seed --class=RolSeeder
```

Esto crea los 4 roles del sistema:

| ID | Slug | Nombre |
|----|------|--------|
| 1 | administrador | Administrador |
| 2 | contador | Contador |
| 3 | lecturador | Lecturador |
| 4 | comun | Comun |

## 9. Crear enlace de storage (opcional)

```bash
php artisan storage:link
```

Necesario si planeas almacenar archivos (avatares, facturas PDF, etc.).

## 10. Levantar el backend

```bash
php artisan serve
```

El backend estará disponible en: `http://127.0.0.1:8000`

## 11. Levantar el frontend

En una segunda terminal:

```bash
npm run dev
```

El frontend estará disponible en: `http://localhost:5173`

## 12. Verificar que todo funciona

Abrir el navegador en `http://localhost:5173` y verificar:

1. Se muestra la página de login
2. Se puede navegar a `/dashboard`
3. El formulario de login funciona

También se puede verificar el backend directamente:

```bash
curl http://127.0.0.1:8000/api/me
```

Respuesta esperada (401):
```json
{
    "success": false,
    "message": "Unauthenticated.",
    "data": null,
    "errors": null
}
```

## 13. Configurar Google OAuth (opcional)

Si se desea habilitar login con Google:

1. Seguir la guía completa en [06-google-oauth.md](06-google-oauth.md)
2. Configurar las variables en `.env`
3. Ejecutar la migración de campos Google:

```bash
php artisan migrate
```

## Variables de entorno necesarias

| Variable | Requerida | Descripción |
|----------|-----------|-------------|
| `APP_KEY` | Sí | Generada con `php artisan key:generate` |
| `DB_CONNECTION` | Sí | `pgsql` o `sqlite` |
| `DB_HOST` | Sí | Host de la base de datos |
| `DB_PORT` | Sí | Puerto de la base de datos |
| `DB_DATABASE` | Sí | Nombre de la base de datos |
| `DB_USERNAME` | Sí | Usuario de la base de datos |
| `DB_PASSWORD` | Sí | Contraseña de la base de datos |
| `MAIL_MAILER` | No | `log` en desarrollo, `smtp` en producción |
| `GOOGLE_CLIENT_ID` | No | Solo si se usa Google OAuth |
| `GOOGLE_CLIENT_SECRET` | No | Solo si se usa Google OAuth |
| `GOOGLE_REDIRECT_URI` | No | Solo si se usa Google OAuth |

## Solución de problemas

### Error: "Could not find driver"

El driver de PostgreSQL no está instalado. Instalar ext-pgsql:

```bash
# Ubuntu/Debian
sudo apt-get install php-pgsql

# macOS
brew install php-pgsql
```

### Error: "SQLSTATE[HY000] Connection refused"

La base de datos no está corriendo. Verificar que PostgreSQL esté activo:

```bash
# Linux
sudo systemctl status postgresql

# macOS
brew services list
```

### Error: "No application encryption key has been specified"

Ejecutar:

```bash
php artisan key:generate
```

### Error: "CSRF token mismatch"

Asegurarse de que el frontend esté enviando el header `Accept: application/json` en todas las peticiones.
