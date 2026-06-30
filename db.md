# Base de Datos - Medi-Agua

## Descripción

La base de datos de **Medi-Agua** fue diseñada siguiendo un modelo relacional para administrar el servicio de distribución de agua potable. Su objetivo es controlar de manera eficiente los procesos de registro de socios, instalación de medidores, toma de lecturas, facturación, pagos y notificaciones.

El modelo prioriza la escalabilidad, la integridad de los datos y la normalización, permitiendo agregar nuevos conceptos de cobro sin modificar la estructura principal del sistema.

---

# Objetivos del Modelo

La base de datos permite gestionar:

- Administración de usuarios del sistema.
- Gestión de roles y permisos.
- Registro de socios.
- Administración de medidores.
- Registro de lecturas de consumo.
- Configuración de tarifas.
- Generación de facturas.
- Registro de pagos.
- Envío de notificaciones.

---

# Modelo Relacional

```
Roles
   │
   └──────── Users
                 │
                 └──────── Lecturas

Socios
   │
   ├──────── Medidores
   │              │
   │              └──────── Lecturas
   │                            │
   │                            └──────── Facturas
   │                                         │
   │                                         ├──────── Facturas_Tarifas
   │                                         │               │
   │                                         │               └──────── Tarifas
   │                                         │
   │                                         └──────── Pagos
   │
   └──────── Notificaciones
```

---

# Tablas

---

## Roles

Almacena los perfiles de acceso del sistema.

| Campo      | Tipo      | Descripción           |
| ---------- | --------- | --------------------- |
| id         | Integer   | Identificador del rol |
| name       | Varchar   | Nombre del rol        |
| created_at | Timestamp | Fecha de creación     |

### Ejemplos

- Administrador
- Cajero
- Lector
- Supervisor

---

## Users

Contiene los usuarios que operan el sistema.

| Campo      | Tipo      |
| ---------- | --------- |
| id         | Integer   |
| username   | Varchar   |
| lastname   | Varchar   |
| email      | Varchar   |
| password   | Varchar   |
| role_id    | Integer   |
| created_at | Timestamp |

### Relaciones

- Pertenece a un rol.
- Puede registrar múltiples lecturas.

---

## Socios

Representa a los clientes o propietarios del servicio de agua.

| Campo      | Tipo      |
| ---------- | --------- |
| id         | Integer   |
| nombres    | Varchar   |
| apellidos  | Varchar   |
| ci         | Varchar   |
| telefono   | Varchar   |
| direccion  | Varchar   |
| estado     | Varchar   |
| created_at | Timestamp |

### Relaciones

- Un socio posee uno o varios medidores.
- Un socio puede tener múltiples facturas.
- Un socio recibe notificaciones.

---

## Medidores

Representa cada medidor físico instalado.

| Campo      | Tipo      |
| ---------- | --------- |
| id         | Integer   |
| codigo     | Varchar   |
| ubicacion  | Varchar   |
| socio_id   | Integer   |
| estado     | Varchar   |
| created_at | Timestamp |

### Relaciones

- Pertenece a un socio.
- Tiene muchas lecturas.

---

## Lecturas

Registra el consumo periódico del medidor.

| Campo            | Tipo      |
| ---------------- | --------- |
| id               | Integer   |
| medidor_id       | Integer   |
| lectura_anterior | Decimal   |
| lectura_actual   | Decimal   |
| consumo          | Decimal   |
| observacion      | Text      |
| usuario_id       | Integer   |
| fecha_lectura    | Date      |
| created_at       | Timestamp |

### Cálculo del consumo

```
Consumo = Lectura Actual - Lectura Anterior
```

### Relaciones

- Pertenece a un medidor.
- Es registrada por un usuario.
- Genera una factura.

---

## Tarifas

Representa cada concepto de cobro que puede formar parte de una factura.

| Campo      | Tipo      |
| ---------- | --------- |
| id         | Integer   |
| nombre     | Varchar   |
| precio     | Decimal   |
| created_at | Timestamp |

### Ejemplos

- Consumo por metro cúbico
- Pro-Deporte
- Alcantarillado
- Mantenimiento
- Reconexión
- Multa por retraso
- Servicio administrativo

---

## Facturas

Representa el documento de cobro emitido al socio.

| Campo             | Tipo      |
| ----------------- | --------- |
| id                | Integer   |
| numero            | Varchar   |
| socio_id          | Integer   |
| lectura_id        | Integer   |
| monto_total       | Decimal   |
| fecha_emision     | Date      |
| fecha_vencimiento | Date      |
| estado            | Varchar   |
| created_at        | Timestamp |

### Estados sugeridos

- Pendiente
- Pagada
- Vencida
- Anulada

### Relaciones

- Pertenece a un socio.
- Se genera a partir de una lectura.
- Contiene múltiples conceptos de cobro mediante la tabla **facturas_tarifas**.
- Puede registrar uno o varios pagos.

---

## Facturas_Tarifas

Tabla intermedia que implementa la relación **Muchos a Muchos** entre facturas y tarifas.

Cada registro representa un concepto incluido dentro de una factura.

| Campo           | Tipo      |
| --------------- | --------- |
| id              | Integer   |
| factura_id      | Integer   |
| tarifa_id       | Integer   |
| cantidad        | Decimal   |
| precio_unitario | Decimal   |
| subtotal        | Decimal   |
| created_at      | Timestamp |

### Ejemplo

Factura N.º 00025

| Tarifa            | Cantidad | Precio | Subtotal |
| ----------------- | -------: | -----: | -------: |
| Consumo de agua   |    32 m³ |   2.50 |    80.00 |
| Pro-Deporte       |        1 |   5.00 |     5.00 |
| Mantenimiento     |        1 |  10.00 |    10.00 |
| Multa por retraso |        1 |  20.00 |    20.00 |

Monto Total:

```
115.00 Bs
```

### ¿Por qué guardar precio_unitario?

El precio de una tarifa puede cambiar con el tiempo.

Guardar el precio aplicado en la factura garantiza conservar el historial de cobros, evitando que una modificación futura altere facturas ya emitidas.

---

## Pagos

Registra los pagos realizados por los socios.

| Campo         | Tipo      |
| ------------- | --------- |
| id            | Integer   |
| factura_id    | Integer   |
| monto         | Decimal   |
| metodo_pago   | Varchar   |
| referencia_qr | Varchar   |
| fecha_pago    | Datetime  |
| created_at    | Timestamp |

### Métodos de pago

- Efectivo
- QR
- Transferencia
- Tarjeta

---

## Notificaciones

Permite almacenar las notificaciones enviadas a los socios.

| Campo       | Tipo      |
| ----------- | --------- |
| id          | Integer   |
| socio_id    | Integer   |
| tipo        | Varchar   |
| mensaje     | Text      |
| estado      | Varchar   |
| fecha_envio | Datetime  |
| created_at  | Timestamp |

### Tipos

- Factura generada
- Pago registrado
- Pago pendiente
- Corte de servicio
- Aviso general

---

# Relaciones

## Roles

```
Role (1)
    │
    └──────── (N) Users
```

---

## Socios

```
Socio (1)
    ├──────── (N) Medidores
    ├──────── (N) Facturas
    └──────── (N) Notificaciones
```

---

## Medidores

```
Medidor (1)
        │
        └──────── (N) Lecturas
```

---

## Usuarios

```
Usuario (1)
       │
       └──────── (N) Lecturas
```

---

## Lecturas

```
Lectura (1)
       │
       └──────── (1) Factura
```

---

## Facturas

```
Factura (1)
      │
      ├──────── (N) Pagos
      │
      └──────── (N) Facturas_Tarifas
                     │
                     └──────── (1) Tarifa
```

---

# Llaves Foráneas

| Tabla            | Llave Foránea | Referencia   |
| ---------------- | ------------- | ------------ |
| users            | role_id       | roles.id     |
| medidores        | socio_id      | socios.id    |
| lecturas         | medidor_id    | medidores.id |
| lecturas         | usuario_id    | users.id     |
| facturas         | socio_id      | socios.id    |
| facturas         | lectura_id    | lecturas.id  |
| facturas_tarifas | factura_id    | facturas.id  |
| facturas_tarifas | tarifa_id     | tarifas.id   |
| pagos            | factura_id    | facturas.id  |
| notificaciones   | socio_id      | socios.id    |

---

# Flujo del Sistema

```
Registro del Socio
        │
        ▼
Asignación del Medidor
        │
        ▼
Registro de Lectura
        │
        ▼
Cálculo del Consumo
        │
        ▼
Generación de Factura
        │
        ├──────── Consumo de Agua
        ├──────── Pro-Deporte
        ├──────── Mantenimiento
        ├──────── Multas
        └──────── Otros conceptos
        │
        ▼
Monto Total
        │
        ▼
Pago
        │
        ▼
Notificación al Socio
```

---

# Justificación del Modelo

La relación entre **Facturas** y **Tarifas** se implementa mediante una tabla intermedia (**facturas_tarifas**) debido a que una factura puede incluir múltiples conceptos de cobro, mientras que una misma tarifa puede aplicarse en numerosas facturas.

Este diseño ofrece las siguientes ventajas:

- Permite agregar nuevos conceptos de cobro sin modificar la estructura de la base de datos.
- Conserva el historial de precios aplicados en cada factura.
- Facilita la incorporación de descuentos, subsidios, impuestos o recargos.
- Evita redundancia de datos y mantiene la base de datos normalizada.
- Escala fácilmente conforme aumenten las necesidades del sistema.

Este enfoque corresponde al utilizado en sistemas modernos de facturación, donde una factura se compone de múltiples detalles o ítems asociados mediante una relación de muchos a muchos.
