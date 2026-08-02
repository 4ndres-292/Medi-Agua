# 07 — Postman

Guía para usar la colección de Postman del módulo de autenticación.

## Colecciones disponibles

| Archivo | Contenido |
|---------|-----------|
| `postman/Register_API.json` | Endpoint de registro |
| `postman/ChangePassword_API.json` | Endpoint de cambio de contraseña |
| `postman/ForgotPassword_API.json` | Endpoint de recuperación de contraseña |
| `postman/ResetPassword_API.json` | Endpoint de restablecimiento de contraseña |
| `postman/GoogleOAuth_API.json` | Endpoints de Google OAuth |

## Cómo importar una colección

### Método 1: Importar archivo

1. Abrir Postman
2. Click en **Import** (botón arriba a la izquierda)
3. Seleccionar **Files**
4. Navegar a la carpeta `postman/` del proyecto
5. Seleccionar el archivo `.json` deseado
6. Click **Import**

### Método 2: Arrastrar archivo

1. Abrir Postman
2. Arrastrar el archivo `.json` directamente a la ventana de Postman
3. Soltar

## Cómo configurar variables

### Variables de colección

1. En Postman, abrir la colección importada
2. Click en la pestaña **Variables**
3. Agregar las siguientes variables:

| Nombre | Valor inicial | Descripción |
|--------|---------------|-------------|
| `baseUrl` | `http://127.0.0.1:8000/api` | URL base del backend |
| `token` | *(vacío)* | Token de autenticación |

4. Click **Save**

### Variables de entorno (recomendado)

1. Click en el ojo 👁️ arriba a la derecha
2. Click en **Environment quick look**
3. Click **Add Environment**
4. Nombre: "Medi-Agua Local"
5. Agregar variables:

| Nombre | Valor |
|--------|-------|
| `baseUrl` | `http://127.0.0.1:8000/api` |
| `token` | *(vacío)* |

6. Click **Save**
7. Seleccionar "Medi-Agua Local" como entorno activa

## Cómo reutilizar el token automáticamente

### Opción 1: Script en el request de Login

1. Abrir el request de Login
2. Ir a la pestaña **Tests**
3. Agregar este código:

```javascript
const response = pm.response.json();

if (response.data && response.data.token) {
    pm.collectionVariables.set('token', response.data.token);
    console.log('Token guardado: ' + response.data.token);
}
```

4. Guardar

Ahora, cada vez que ejecutes el request de Login, el token se guardará automáticamente.

### Opción 2: Usar el token en requests autenticados

1. Abrir el request de /me
2. En la pestaña **Authorization**, seleccionar **Bearer Token**
3. En el campo Token, escribir: `{{token}}`
4. Guardar

Esto usará el token guardado automáticamente.

## Cómo ejecutar una colección completa

### Método 1: Collection Runner

1. En la barra lateral, expandir la colección
2. Click en **Run collection**
3. Seleccionar los requests que deseas ejecutar
4. Click **Run Medi-Agua**
5. Verificar que todos los tests pasen (verde)

### Método 2: Newman (CLI)

```bash
# Instalar Newman globalmente
npm install -g newman

# Ejecutar una colección
newman run postman/Register_API.json

# Ejecutar con entorno
newman run postman/Register_API.json -e environment.json
```

## Cómo interpretar las respuestas

### Respuesta exitosa (200/201)

```
Status: 200 OK
Body:
{
    "success": true,
    "message": "Inicio de sesión exitoso.",
    "data": { ... },
    "errors": null
}
```

### Respuesta de error (422)

```
Status: 422 Unprocessable Entity
Body:
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["El correo electrónico ya está en uso."]
    }
}
```

### Respuesta de autenticación (401)

```
Status: 401 Unauthorized
Body:
{
    "success": false,
    "message": "Unauthenticated.",
    "data": null,
    "errors": null
}
```

## Ejemplo: Flujo completo en Postman

### Paso 1: Register

1. Seleccionar request **Register**
2. Verificar body:
```json
{
    "username": "Juan",
    "lastname": "Pérez",
    "email": "juan@example.com",
    "password": "secret123",
    "password_confirmation": "secret123"
}
```
3. Click **Send**
4. Verificar respuesta 201
5. El token se guarda automáticamente (si configuraste el script)

### Paso 2: Login

1. Seleccionar request **Login**
2. Verificar body:
```json
{
    "email": "juan@example.com",
    "password": "secret123"
}
```
3. Click **Send**
4. Verificar respuesta 200
5. El token se actualiza

### Paso 3: Me

1. Seleccionar request **Me**
2. Verificar que el header `Authorization` usa `{{token}}`
3. Click **Send**
4. Verificar respuesta 200 con datos del usuario

### Paso 4: Change Password

1. Seleccionar request **Change Password**
2. Verificar body:
```json
{
    "current_password": "secret123",
    "password": "nueva_password123",
    "password_confirmation": "nueva_password123"
}
```
3. Click **Send**
4. Verificar respuesta 200
5. **Nota:** El token anterior queda invalidado

### Paso 5: Verificar token invalidado

1. Seleccionar request **Me** de nuevo
2. Click **Send**
3. Verificar respuesta 401 (token ya no funciona)

## Solución de problemas

### "Could not send request"

**Causa:** El backend no está corriendo.

**Solución:**
```bash
php artisan serve
```

### "Connection refused"

**Causa:** URL incorrecta o backend no accesible.

**Solución:** Verificar que `baseUrl` sea `http://127.0.0.1:8000/api`.

### Token no se guarda automáticamente

**Causa:** El script de Tests no está configurado.

**Solución:** Verificar que el request de Login tenga el script en la pestaña Tests.

### 401 en requests autenticados

**Causa:** Token no enviado o inválido.

**Solución:**
1. Verificar que el header sea `Authorization: Bearer {{token}}`
2. Verificar que la variable `token` tenga un valor

### 429 Too Many Requests

**Causa:** Demasiados intentos de login.

**Solución:** Esperar 60 minutos o reiniciar el backend.
