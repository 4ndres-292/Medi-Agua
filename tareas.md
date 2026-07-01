# Medi-Agua - Tareas Pendientes

## 🔥 Estado Actual del Sistema

### ✅ Funcionando
- Login con Sanctum tokens (sin sesiones Laravel)
- Logout que invalida token correctamente
- 48 rutas API protegidas con auth:sanctum
- CRUD completo para 9 entidades backend
- Frontend con 18 rutas y navegación completa
- Navbar ERP con dropdowns funcionales
- AuthGuard que valida token contra backend
- Interceptor Axios que inyecta Bearer token
- Auto-logout en respuesta 401

### ⚠️ Incompleto
- Formularios CRUD (solo tablas, sin modales de creación/edición)
- Dashboard con estadísticas hardcodeadas (no dinámicas)
- Reportes sin datos reales
- Perfil de usuario solo lectura
- Configuración sin funcionalidad real

### 🚨 En Riesgo
- `SESSION_DRIVER=database` en .env (no afecta auth pero genera sesiones huérfanas)
- Sin validación de token expirado en frontend
- Sin manejo de refresh token

---

## 📌 Tareas por Módulo

### 🔐 Autenticación

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Centralizar estado de auth en React Context | ALTO | Pendiente |
| 2 | Agregar validación de token expirado | ALTO | Pendiente |
| 3 | Implementar refresh token automático | MEDIO | Pendiente |
| 4 | Limpiar tabla sessions periodicamente | BAJO | Pendiente |
| 5 | Agregar rate limiting a /api/login | MEDIO | Pendiente |

### 🎨 Frontend - Componentes

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Crear componente Modal reutilizable | ALTO | Pendiente |
| 2 | Crear componente Table con paginación | ALTO | Pendiente |
| 3 | Crear componente Toast/Notificación | MEDIO | Pendiente |
| 4 | Crear componente Loading spinner | MEDIO | Pendiente |
| 5 | Mejorar Navbar responsive (mobile menu) | MEDIO | Pendiente |

### 📊 Dashboard

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Conectar estadísticas con API real | ALTO | Pendiente |
| 2 | Agregar gráficos de consumo | MEDIO | Pendiente |
| 3 | Mostrar últimas facturas | MEDIO | Pendiente |
| 4 | Mostrar socios morosos | MEDIO | Pendiente |

### 👥 Módulo Usuarios

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Formulario crear usuario | ALTO | Pendiente |
| 2 | Formulario editar usuario | ALTO | Pendiente |
| 3 | Eliminar usuario con confirmación | MEDIO | Pendiente |
| 4 | Búsqueda de usuarios | BAJO | Pendiente |

### 🎭 Módulo Roles

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Formulario crear rol | ALTO | Pendiente |
| 2 | Formulario editar rol | ALTO | Pendiente |
| 3 | Eliminar rol con confirmación | MEDIO | Pendiente |

### 👤 Módulo Socios

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Formulario crear socio | ALTO | Pendiente |
| 2 | Formulario editar socio | ALTO | Pendiente |
| 3 | Ver detalle del socio | MEDIO | Pendiente |
| 4 | Asignar medidor al socio | MEDIO | Pendiente |
| 5 | Búsqueda por nombre/CI | BAJO | Pendiente |

### 📊 Módulo Medidores

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Formulario crear medidor | ALTO | Pendiente |
| 2 | Formulario editar medidor | ALTO | Pendiente |
| 3 | Asignar a socio | MEDIO | Pendiente |
| 4 | Cambiar estado (activo/inactivo) | MEDIO | Pendiente |

### 📖 Módulo Lecturas

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Formulario registrar lectura | ALTO | Pendiente |
| 2 | Calcular consumo automáticamente | ALTO | Pendiente |
| 3 | Validar lectura anterior vs actual | ALTO | Pendiente |
| 4 | Historial de lecturas por medidor | MEDIO | Pendiente |

### 💰 Módulo Tarifas

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Formulario crear tarifa | ALTO | Pendiente |
| 2 | Formulario editar tarifa | ALTO | Pendiente |
| 3 | Activar/desactivar tarifa | MEDIO | Pendiente |

### 📄 Módulo Facturas

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Generar factura desde lectura | ALTO | Pendiente |
| 2 | Agregar múltiples tarifas | ALTO | Pendiente |
| 3 | Calcular monto total automáticamente | ALTO | Pendiente |
| 4 | Cambiar estado (Pendiente/Pagada/Vencida) | MEDIO | Pendiente |
| 5 | Imprimir factura | BAJO | Pendiente |

### 💵 Módulo Pagos

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Registrar pago a factura | ALTO | Pendiente |
| 2 | Métodos de pago (Efectivo/QR/Transferencia) | ALTO | Pendiente |
| 3 | Generar referencia de pago | MEDIO | Pendiente |
| 4 | Historial de pagos por socio | MEDIO | Pendiente |

### 📈 Reportes

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Reporte de ingresos con filtros por fecha | ALTO | Pendiente |
| 2 | Reporte de deudores con monto adeudado | ALTO | Pendiente |
| 3 | Reporte de consumo por socio/período | ALTO | Pendiente |
| 4 | Exportar reportes a PDF | MEDIO | Pendiente |
| 5 | Exportar reportes a Excel | BAJO | Pendiente |

### 🔌 Backend

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Agregar validaciones a todos los Controllers | ALTO | Pendiente |
| 2 | Implementar soft deletes | MEDIO | Pendiente |
| 3 | Agregar paginación a endpoints | ALTO | Pendiente |
| 4 | Agregar búsqueda/filtros | MEDIO | Pendiente |
| 5 | Configurar CORS para producción | ALTO | Pendiente |
| 6 | Agregar logging de auditoría | BAJO | Pendiente |

### 🛡️ Seguridad

| # | Tarea | Prioridad | Estado |
|---|-------|-----------|--------|
| 1 | Rate limiting en endpoints públicos | ALTO | Pendiente |
| 2 | Validación de permisos por rol | ALTO | Pendiente |
| 3 | Sanitización de inputs | MEDIO | Pendiente |
| 4 | HTTPS en producción | ALTO | Pendiente |

---

## 🧠 Prioridades

### CRÍTICO (Hacer primero)
1. Formularios CRUD para entidades principales
2. Conectar Dashboard con API real
3. Validaciones backend completas
4. Paginación en tablas

### ALTO (Hacer pronto)
1. Centralizar auth en React Context
2. Generar facturas desde lecturas
3. Registrar pagos a facturas
4. Reportes con datos reales

### MEDIO (Mejoras)
1. Componentes reutilizables (Modal, Table, Toast)
2. Búsqueda y filtros
3. Exportar PDF
4. Historial por entidad

### BAJO (Nice to have)
1. Notificaciones push
2. Dashboard con gráficos
3. Exportar Excel
4. Auditoría de cambios

---

## 👨‍💻 Asignación Sugerida

| Tarea | Responsable |
|-------|-------------|
| Formularios CRUD Frontend | Frontend |
| Validaciones Backend | Backend |
| Dashboard dinámico | Fullstack |
| Reportes | Fullstack |
| Seguridad/Roles | Backend |
| UI/UX Componentes | Frontend |
| API Endpoints | Backend |
| Integración Frontend-Backend | Fullstack |
