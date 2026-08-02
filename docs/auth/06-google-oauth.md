# 06 — Google OAuth

Guía completa para la integración con Google OAuth usando Laravel Socialite.

## Flujo completo

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   USUARIO    │     │   FRONTEND   │     │   BACKEND    │
└──────┬───────┘     └──────┬───────┘     └──────┬───────┘
       │                    │                    │
       │  1. Click en       │                    │
       │  "Google Login"    │                    │
       │───────────────────▶│                    │
       │                    │                    │
       │                    │  2. Redirigir a    │
       │                    │  /google/redirect  │
       │                    │───────────────────▶│
       │                    │                    │
       │  3. Redirigir a    │                    │
       │  accounts.google   │◀───────────────────│
       │◀───────────────────│                    │
       │                    │                    │
       │  4. Autorizar en   │                    │
       │  Google            │                    │
       │────────────────────────────────────────▶│
       │                    │                    │
       │                    │  5. Google llama   │
       │                    │  a /google/callback│
       │                    │◀───────────────────│
       │                    │                    │
       │                    │  6. Backend busca  │
       │                    │  o crea usuario    │
       │                    │  Genera token      │
       │                    │                    │
       │  7. Retornar con   │                    │
       │  token             │                    │
       │◀───────────────────│                    │
       │                    │                    │
       │  8. Guardar token  │                    │
       │  Ir a dashboard    │                    │
       │◀───────────────────│                    │
```

## Configuración en Google Cloud Console

### Paso 1: Crear proyecto

1. Ir a [Google Cloud Console](https://console.cloud.google.com)
2. Crear un nuevo proyecto o seleccionar uno existente
3. Nombrar el proyecto (ej: "Medi-Agua")

### Paso 2: Habilitar APIs

1. Ir a **APIs & Services → Library**
2. Buscar "Google+ API" o "People API"
3. Click **Enable**

### Paso 3: Crear credenciales OAuth

1. Ir a **APIs & Services → Credentials**
2. Click **Create Credentials → OAuth Client ID**
3. Seleccionar **Web Application**
4. Nombre: "Medi-Agua Frontend"
5. En **Authorized redirect URIs**, agregar:
   ```
   http://127.0.0.1:8000/api/auth/google/callback
   ```
6. Click **Create**
7. Copiar el **Client ID** y **Client Secret**

### Paso 4: Configurar pantallas de consentimiento

1. Ir a **APIs & Services → OAuth consent screen**
2. Seleccionar **External** (o **Internal** si es solo para tu organización)
3. Completar:
   - App name: "Medi-Agua"
   - User support email: tu email
   - Developer contact: tu email
4. Guardar

## Variables de entorno

Agregar al archivo `.env`:

```env
GOOGLE_CLIENT_ID=tu_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/auth/google/callback
```

**Importante:** El `GOOGLE_REDIRECT_URI` debe coincidir EXACTAMENTE con el configurado en Google Cloud Console.

## Configuración de Socialite

El archivo `config/services.php` ya tiene la configuración:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

No se requiere modificar este archivo.

## Rutas

Las rutas ya están configuradas en `routes/api.php`:

```php
Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect']);
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
```

## Cómo funciona internamente

### 1. Redirect a Google

```php
// AuthController::googleRedirect()
public function googleRedirect(): RedirectResponse
{
    $url = $this->authService->getGoogleRedirectUrl();
    return redirect($url);
}

// AuthService::getGoogleRedirectUrl()
public function getGoogleRedirectUrl(): string
{
    return Socialite::driver('google')
        ->stateless()    // Sin sesiones
        ->redirect()
        ->getTargetUrl();
}
```

### 2. Callback de Google

```php
// AuthController::googleCallback()
public function googleCallback(): JsonResponse
{
    try {
        $data = $this->authService->handleGoogleCallback();
        return ApiResponse::success(
            new LoginResource($data),
            'Inicio de sesión con Google exitoso.'
        );
    } catch (\Exception $e) {
        return ApiResponse::error(
            'Error al autenticar con Google. Intente nuevamente.',
            null, 500
        );
    }
}
```

### 3. Creación o vinculación de usuario

```php
// AuthService::findOrCreateGoogleUser()
private function findOrCreateGoogleUser(GoogleUser $googleUser): User
{
    // Buscar usuario existente por email
    $existingUser = User::where('email', $googleUser->getEmail())->first();

    if ($existingUser) {
        // Si existe, vincular google_id y avatar
        $existingUser->update([
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ]);
        return $existingUser;
    }

    // Si no existe, crear usuario nuevo
    return User::create([
        'username' => $googleUser->getName() ?? $googleUser->getEmail(),
        'lastname' => '',
        'email' => $googleUser->getEmail(),
        'password' => bcrypt(Str::random(32)),  // Password aleatorio
        'rol_id' => $this->getComunRoleId(),    // Rol "Comun"
        'google_id' => $googleUser->getId(),
        'avatar' => $googleUser->getAvatar(),
    ]);
}
```

## Creación automática de usuarios

Cuando un usuario se autentica por primera vez con Google:

| Campo | Valor |
|-------|-------|
| `username` | Nombre de Google (o email si no hay nombre) |
| `lastname` | String vacío `''` |
| `email` | Email verificado de Google |
| `password` | String aleatorio de 32 caracteres (hasheado) |
| `rol_id` | ID del rol "Comun" (buscado en la DB) |
| `google_id` | ID único de Google |
| `avatar` | URL de la foto de perfil |

## Vinculación de cuentas

Si un usuario ya tiene una cuenta con el mismo email:

1. Se actualiza su `google_id` con el ID de Google
2. Se actualiza su `avatar` con la foto de Google
3. Se le genera un token Sanctum
4. Se retorna login exitoso

**No se crea un usuario duplicado.**

## Asignación del rol "Común"

```php
private function getComunRoleId(): int
{
    $rol = Rol::where('slug', 'comun')->first();

    if (!$rol) {
        // Si no existe, lo crea automáticamente
        $rol = Rol::create([
            'slug' => 'comun',
            'name' => 'Comun',
        ]);
    }

    return $rol->id;
}
```

**No se usa un ID hardcodeado.** El rol se busca por su slug en la base de datos.

## Generación del token Sanctum

```php
private function createAccessToken(User $user): string
{
    return $user
        ->createToken('auth-token')
        ->plainTextToken;
}
```

El token se genera igual que en login normal. Se usa el mismo patrón.

## Ejemplos completos

### Frontend — Botón de Google Login

```typescript
const GoogleLoginButton: React.FC = () => {
    const handleGoogleLogin = () => {
        window.location.href = 'http://127.0.0.1:8000/api/auth/google/redirect';
    };

    return (
        <button onClick={handleGoogleLogin} className="google-btn">
            <img src="/google-icon.svg" alt="Google" />
            Continuar con Google
        </button>
    );
};
```

### Frontend — Manejar callback

```typescript
// En Login.tsx o una ruta dedicada
useEffect(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    
    if (token) {
        localStorage.setItem('token', token);
        navigate('/dashboard');
    }
}, [navigate]);
```

### Request completo (curl)

```bash
# 1. Redirigir a Google (abrir en navegador)
curl -v http://127.0.0.1:8000/api/auth/google/redirect

# 2. Después de autorizar, Google redirige a:
# http://127.0.0.1:8000/api/auth/google/callback?code=xxx&state=xxx

# 3. El backend retorna JSON con el token
```

### Response exitosa

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

## Errores comunes

### "Redirect URI mismatch"

**Causa:** El URI configurado en Google Cloud Console no coincide con `.env`.

**Solución:**
1. Verificar `GOOGLE_REDIRECT_URI` en `.env`
2. Verificar **Authorized redirect URIs** en Google Cloud Console
3. Deben ser idénticos (incluyendo `http://` y puerto)

### "Invalid state"

**Causa:** El parámetro `state` no coincide (posible ataque CSRF o configuración incorrecta).

**Solución:**
1. Verificar que no hay extensions del navegador bloqueando cookies
2. Verificar que Socialite está configurado con `stateless()`

### Error 500 en callback

**Causa:** Configuración incorrecta de Client ID o Client Secret.

**Solución:**
1. Verificar `GOOGLE_CLIENT_ID` en `.env`
2. Verificar `GOOGLE_CLIENT_SECRET` en `.env`
3. Verificar que las credenciales no hayan expirado

### Email no verificado

**Causa:** Google retorna `email_verified: false`.

**Solución:** En el código actual, no se valida `email_verified`. Si se desea agregar esta validación, modificar `findOrCreateGoogleUser()`.

### Usuario existe pero no se vincula

**Causa:** El email de Google no coincide exactamente con el email en la DB.

**Solución:**
1. Verificar que el email en la DB es el mismo que el de Google
2. Los emails son case-sensitive en algunas configuraciones

## Variables de entorno requeridas

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `GOOGLE_CLIENT_ID` | Client ID de Google | `xxx.apps.googleusercontent.com` |
| `GOOGLE_CLIENT_SECRET` | Client Secret de Google | `GOCSPX-xxx` |
| `GOOGLE_REDIRECT_URI` | URI de callback | `http://127.0.0.1:8000/api/auth/google/callback` |

## Migración necesaria

La migración para los campos de Google ya está incluida:

```php
// database/migrations/2026_08_02_000001_add_google_fields_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('google_id')->nullable()->unique()->after('id');
    $table->string('avatar')->nullable()->after('email');
});
```

Ejecutar:

```bash
php artisan migrate
```
