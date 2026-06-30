# Objetivo

Crear todos los Seeders necesarios para poblar automáticamente la base de datos con información de prueba consistente.

Los Seeders deben permitir ejecutar únicamente:

```bash
php artisan migrate:fresh --seed
```

y obtener un sistema completamente funcional para pruebas con Postman y posteriormente React.

---

# Organización

Crear un Seeder independiente para cada tabla.

```
database/seeders/

RoleSeeder.php
UserSeeder.php
SocioSeeder.php
MedidorSeeder.php
LecturaSeeder.php
TarifaSeeder.php
FacturaSeeder.php
FacturaTarifaSeeder.php
PagoSeeder.php
NotificacionSeeder.php
DatabaseSeeder.php
```

No colocar toda la lógica dentro de DatabaseSeeder.

DatabaseSeeder únicamente debe llamar los demás seeders.

---

# Roles

Crear exactamente estos registros.

| ID | Nombre        |
| -- | ------------- |
| 1  | Administrador |
| 2  | Contador      |
| 3  | Lecturador    |
| 4  | Usuario Común |

Estos IDs deben mantenerse para facilitar el desarrollo.

---

# Usuario Administrador

Crear un único usuario administrador.

Nombre

```
Andres
```

Apellido

```
Choquecahuana Cahuana
```

Correo

```
choquecahuanaandresoriginal@gmail.com
```

Contraseña

```
12345678
```

La contraseña debe almacenarse utilizando Hash::make().

Debe pertenecer al rol:

```
Administrador
```

(role_id = 1)

---

# Socios

Crear tres socios ficticios.

Todos deben tener:

* nombres
* apellidos
* CI
* teléfono
* dirección

Estado:

```
Activo
```

Los datos deben parecer reales.

---

# Medidores

Crear tres medidores.

Todos deben estar en estado:

```
Activo
```

Los códigos deben dejar espacios para futuras inserciones.

Utilizar la siguiente secuencia.

```
000020
000040
000060
```

La idea es que posteriormente puedan existir:

```
000010
000030
000050
```

sin romper el orden.

Cada medidor pertenece a un socio.

Un medidor por socio.

---

# Lecturas

Crear dos lecturas para cada medidor.

Total:

```
6 lecturas
```

La primera lectura.

```
Lectura anterior: 0
Lectura actual: entre 10 y 15
```

La segunda lectura.

```
Lectura anterior: lectura actual anterior
Lectura actual: entre 60 y 70
```

Calcular automáticamente:

```
consumo
=
lectura_actual
-
lectura_anterior
```

Todas las lecturas deben haber sido registradas por:

```
usuario_id = 1
```

Agregar observaciones realistas.

Ejemplos.

* Lectura normal.
* Sin novedades.
* Medidor en buen estado.

---

# Tarifas

Crear varias tarifas de ejemplo.

Ejemplo.

| Nombre            | Precio |
| ----------------- | ------ |
| Consumo por m³    | 2.50   |
| Pro-Deporte       | 5.00   |
| Mantenimiento     | 10.00  |
| Alcantarillado    | 8.00   |
| Multa por retraso | 20.00  |

Los precios pueden ser decimales.

---

# Facturas

Crear tres facturas.

Una por cada socio.

Relacionarlas con sus respectivas lecturas.

Calcular el monto_total utilizando los subtotales de la tabla pivote.

Estados variados.

Ejemplo.

* Pagada
* Pendiente
* Vencida

Fechas coherentes.

---

# Facturas_Tarifas

Cada factura debe tener varios conceptos.

Ejemplo.

Factura 1

* Consumo por m³
* Pro-Deporte
* Mantenimiento

Factura 2

* Consumo por m³
* Alcantarillado

Factura 3

* Consumo por m³
* Pro-Deporte
* Multa por retraso

Cada registro debe guardar.

* cantidad
* precio_unitario
* subtotal

El subtotal debe calcularse automáticamente.

---

# Pagos

Crear un ejemplo de pago.

Solo la factura marcada como Pagada debe tener pago.

Método.

```
QR
```

Agregar una referencia ficticia.

---

# Notificaciones

Crear al menos una notificación para cada socio.

Ejemplos.

Factura emitida.

Pago recibido.

Factura pendiente.

---

# Relaciones

Todos los datos deben mantener integridad referencial.

No crear registros huérfanos.

Todas las Foreign Keys deben existir.

---

# Fechas

Utilizar fechas coherentes.

Por ejemplo.

* Lecturas en días consecutivos.
* Facturas posteriores a las lecturas.
* Pagos posteriores a las facturas.
* Notificaciones posteriores a los pagos.

---

# Buenas prácticas

Utilizar:

* Model::create()
* Carbon cuando sea necesario
* Hash::make() para contraseñas

No utilizar SQL crudo.

No utilizar DB::statement().

---

# Resultado esperado

Después de ejecutar:

```bash
php artisan migrate:fresh --seed
```

La base de datos debe quedar completamente poblada con información consistente y lista para realizar pruebas desde Postman y posteriormente consumir la API desde React.

No generar datos aleatorios con Faker.

Los datos deben ser fijos, reproducibles y fáciles de identificar durante las pruebas y demostraciones.
