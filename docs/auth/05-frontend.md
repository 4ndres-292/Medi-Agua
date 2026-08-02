# 05 — Frontend

Guía completa para consumir las APIs de autenticación desde React.

## Configuración de Axios

El proyecto ya tiene configurado un cliente Axios en `resources/js/services/api.ts`:

```typescript
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
});

// Interceptor: agrega Bearer token automáticamente
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Interceptor: maneja 401 automáticamente
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;
```

**Qué hace este archivo:**
1. Configura la URL base (`http://127.0.0.1:8000/api`)
2. Agrega el header `Authorization: Bearer <token>` automáticamente si hay token
3. Si recibe 401, limpia el token y redirige a `/login`

## Cómo almacenar el token

### Después de Login

```typescript
const response = await api.post('/login', { email, password });
const { token } = response.data.data;

// Guardar en localStorage
localStorage.setItem('token', token);
```

### Después de Register

```typescript
const response = await api.post('/register', {
    username: 'Juan',
    lastname: 'Pérez',
    email: 'juan@example.com',
    password: 'secret123',
    password_confirmation: 'secret123',
});
const { token } = response.data.data;

localStorage.setItem('token', token);
```

### Después de Google OAuth

```typescript
// El backend retorna el token en el callback
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get('token');

if (token) {
    localStorage.setItem('token', token);
    window.location.href = '/dashboard';
}
```

## Cómo enviar el Bearer Token

### Usando Axios (recomendado)

```typescript
import api from '../services/api';

// El interceptor ya agrega el token automáticamente
const response = await api.get('/me');
console.log(response.data.data); // Datos del usuario
```

### Usando fetch

```typescript
const token = localStorage.getItem('token');

const response = await fetch('http://127.0.0.1:8000/api/me', {
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
    },
});

const data = await response.json();
console.log(data.data); // Datos del usuario
```

## Cómo detectar un 401

### Con Axios

```typescript
try {
    const response = await api.get('/me');
    // Todo bien
} catch (error) {
    if (error.response?.status === 401) {
        // Token inválido o expirado
        localStorage.removeItem('token');
        window.location.href = '/login';
    }
}
```

### Con fetch

```typescript
const token = localStorage.getItem('token');

const response = await fetch('http://127.0.0.1:8000/api/me', {
    headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
    },
});

if (response.status === 401) {
    localStorage.removeItem('token');
    window.location.href = '/login';
}
```

**Nota:** Con Axios, el interceptor ya maneja esto automáticamente. Solo necesitas configurarlo una vez.

## Cómo detectar un 422

### Con Axios

```typescript
try {
    const response = await api.post('/login', { email, password });
    // Login exitoso
} catch (error) {
    if (error.response?.status === 422) {
        const errors = error.response.data.errors;
        
        // errors es un objeto como:
        // { email: ["El correo electrónico es obligatorio."] }
        
        Object.keys(errors).forEach(field => {
            console.log(`${field}: ${errors[field][0]}`);
        });
    }
}
```

### Con fetch

```typescript
const response = await fetch('http://127.0.0.1:8000/api/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
    body: JSON.stringify({ email, password }),
});

if (response.status === 422) {
    const data = await response.json();
    const errors = data.errors;
    
    Object.keys(errors).forEach(field => {
        console.log(`${field}: ${errors[field][0]}`);
    });
}
```

## Cómo cerrar sesión

### Con AuthContext (recomendado)

```typescript
import { useAuth } from '../contexts/AuthContext';

const { logout } = useAuth();

const handleLogout = async () => {
    await logout(); // Llama a /api/logout y limpia localStorage
    navigate('/login');
};
```

### Con Axios directamente

```typescript
const handleLogout = async () => {
    try {
        await api.post('/logout');
    } catch (error) {
        console.error('Error al cerrar sesión:', error);
    } finally {
        localStorage.removeItem('token');
        window.location.href = '/login';
    }
};
```

### Con fetch

```typescript
const handleLogout = async () => {
    const token = localStorage.getItem('token');
    
    try {
        await fetch('http://127.0.0.1:8000/api/logout', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        });
    } catch (error) {
        console.error('Error al cerrar sesión:', error);
    } finally {
        localStorage.removeItem('token');
        window.location.href = '/login';
    }
};
```

## Cómo manejar Google Login

### Paso 1: Redirigir a Google

```typescript
// En el botón "Continuar con Google"
const handleGoogleLogin = () => {
    window.location.href = 'http://127.0.0.1:8000/api/auth/google/redirect';
};
```

### Paso 2: Manejar el callback

```typescript
// En la página de callback (Login.tsx o una ruta dedicada)
import { useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';

const LoginCallback = () => {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();

    useEffect(() => {
        const token = searchParams.get('token');
        
        if (token) {
            localStorage.setItem('token', token);
            navigate('/dashboard');
        } else {
            // Error o usuario canceló
            navigate('/login');
        }
    }, [searchParams, navigate]);

    return <div>Procesando autenticación...</div>;
};
```

## Ejemplos completos

### Login completo con React

```typescript
import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';

const Login: React.FC = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const response = await api.post('/login', { email, password });
            const { token } = response.data.data;
            
            localStorage.setItem('token', token);
            navigate('/dashboard');
        } catch (err: any) {
            if (err.response?.status === 422) {
                const errors = err.response.data.errors;
                const firstError = Object.values(errors)[0];
                setError(Array.isArray(firstError) ? firstError[0] : String(firstError));
            } else if (err.response?.status === 429) {
                setError('Demasiados intentos. Espera 1 minuto.');
            } else {
                setError('Error al iniciar sesión.');
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            {error && <div className="error">{error}</div>}
            
            <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Email"
                required
            />
            
            <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Contraseña"
                required
            />
            
            <button type="submit" disabled={loading}>
                {loading ? 'Ingresando...' : 'Iniciar sesión'}
            </button>
        </form>
    );
};

export default Login;
```

### Register completo con React

```typescript
import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';

const Register: React.FC = () => {
    const [formData, setFormData] = useState({
        username: '',
        lastname: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            const response = await api.post('/register', formData);
            const { token } = response.data.data;
            
            localStorage.setItem('token', token);
            navigate('/dashboard');
        } catch (err: any) {
            if (err.response?.status === 422) {
                const validationErrors = err.response.data.errors;
                const formattedErrors: Record<string, string> = {};
                
                Object.keys(validationErrors).forEach(field => {
                    formattedErrors[field] = validationErrors[field][0];
                });
                
                setErrors(formattedErrors);
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <input
                name="username"
                value={formData.username}
                onChange={handleChange}
                placeholder="Nombre"
                required
            />
            {errors.username && <span className="error">{errors.username}</span>}
            
            <input
                name="lastname"
                value={formData.lastname}
                onChange={handleChange}
                placeholder="Apellido"
                required
            />
            {errors.lastname && <span className="error">{errors.lastname}</span>}
            
            <input
                name="email"
                type="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="Email"
                required
            />
            {errors.email && <span className="error">{errors.email}</span>}
            
            <input
                name="password"
                type="password"
                value={formData.password}
                onChange={handleChange}
                placeholder="Contraseña"
                required
            />
            {errors.password && <span className="error">{errors.password}</span>}
            
            <input
                name="password_confirmation"
                type="password"
                value={formData.password_confirmation}
                onChange={handleChange}
                placeholder="Confirmar contraseña"
                required
            />
            
            <button type="submit" disabled={loading}>
                {loading ? 'Registrando...' : 'Registrarse'}
            </button>
        </form>
    );
};

export default Register;
```

### Obtener perfil del usuario

```typescript
import React, { useEffect, useState } from 'react';
import api from '../services/api';

const Perfil: React.FC = () => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchUser = async () => {
            try {
                const response = await api.get('/me');
                setUser(response.data.data);
            } catch (error) {
                console.error('Error al obtener usuario:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchUser();
    }, []);

    if (loading) return <div>Cargando...</div>;
    if (!user) return <div>Usuario no encontrado</div>;

    return (
        <div>
            <h1>{user.username} {user.lastname}</h1>
            <p>Email: {user.email}</p>
            <p>Rol: {user.role?.name}</p>
        </div>
    );
};

export default Perfil;
```

### Cambiar contraseña

```typescript
import React, { useState } from 'react';
import api from '../services/api';

const ChangePassword: React.FC = () => {
    const [formData, setFormData] = useState({
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setMessage('');
        setError('');

        try {
            await api.post('/change-password', formData);
            setMessage('Contraseña actualizada. Debes iniciar sesión nuevamente.');
            localStorage.removeItem('token');
            window.location.href = '/login';
        } catch (err: any) {
            if (err.response?.status === 422) {
                const errors = err.response.data.errors;
                setError(errors.current_password?.[0] || errors.password?.[0] || 'Error');
            }
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            {message && <div className="success">{message}</div>}
            {error && <div className="error">{error}</div>}
            
            <input
                type="password"
                placeholder="Contraseña actual"
                value={formData.current_password}
                onChange={(e) => setFormData({...formData, current_password: e.target.value})}
                required
            />
            
            <input
                type="password"
                placeholder="Nueva contraseña"
                value={formData.password}
                onChange={(e) => setFormData({...formData, password: e.target.value})}
                required
            />
            
            <input
                type="password"
                placeholder="Confirmar nueva contraseña"
                value={formData.password_confirmation}
                onChange={(e) => setFormData({...formData, password_confirmation: e.target.value})}
                required
            />
            
            <button type="submit">Cambiar contraseña</button>
        </form>
    );
};

export default ChangePassword;
```

## AuthContext

El proyecto incluye un AuthContext que maneja el estado de autenticación:

```typescript
// resources/js/contexts/AuthContext.tsx
import { useAuth } from '../contexts/AuthContext';

// En cualquier componente:
const { user, token, loading, login, logout, isAuthenticated } = useAuth();

// user: datos del usuario o null
// token: token actual o null
// loading: true mientras se verifica el token
// login(email, password): función para iniciar sesión
// logout(): función para cerrar sesión
// isAuthenticated: true si hay token y usuario
```

### Uso en componentes

```typescript
import { useAuth } from '../contexts/AuthContext';
import { Navigate } from 'react-router-dom';

const ProtectedRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const { isAuthenticated, loading } = useAuth();

    if (loading) {
        return <div>Cargando...</div>;
    }

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    return <>{children}</>;
};
```
