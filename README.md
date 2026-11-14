# Sistema de Trámites y PQRS — Alcaldía Municipal de Lorica

Aplicación web académica desarrollada en **PHP 8.2 puro (sin frameworks)** y **MySQL/MariaDB**, orientada a la gestión de **PQRS** (Peticiones, Quejas, Reclamos y Sugerencias) y **trámites/certificados** de la Alcaldía de Lorica.

Incluye tres roles (ciudadano, funcionario y administrador), autenticación con contraseñas hasheadas, protección CSRF, carga privada de archivos, notificaciones internas, seguimiento con línea de tiempo, reportes con exportación CSV y auditoría de acciones.

---

## 1. Requisitos

- XAMPP (o equivalente) con **PHP 8.2+** y extensiones `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `json`, `session`.
- **MariaDB/MySQL 5.7+ / 10.4+**.
- Apache con `mod_rewrite` habilitado (XAMPP lo trae activo).

## 2. Instalación

1. Copie el proyecto en `C:\xamppp\htdocs\alcaldia-lorica` (o ajuste `base_url`).
2. Inicie **Apache** y **MySQL** desde el panel de XAMPP.
3. Importe la base de datos (en Windows use `--default-character-set=utf8mb4` para conservar tildes/ñ):

   ```bash
   "C:\xamppp\mysql\bin\mysql.exe" --default-character-set=utf8mb4 -u root < database/alcaldia_lorica.sql
   ```

4. Verifique las credenciales de conexión en `config/database.php` (por defecto `root` sin contraseña).
5. Abra: **http://localhost/alcaldia-lorica/**

> Si instala en otra carpeta, cambie `base_url` en `config/app.php`.
> Para restablecer los datos de demostración, vuelva a importar `database/alcaldia_lorica.sql` (elimina y recrea las tablas).

## 3. Usuarios de demostración

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@lorica.test` | `Admin123!` |
| Funcionario | `funcionario@lorica.test` | `Funcionario123!` |
| Ciudadano | `ciudadano@lorica.test` | `Ciudadano123!` |

También existen cuentas de ejemplo para `carlos.mendoza@lorica.test` (funcionario) y `maria.gomez@lorica.test` / `jose.martinez@lorica.test` (ciudadanos) con las mismas contraseñas de su rol.

## 4. Funcionalidades principales

### Portal público
- Página de inicio, información institucional, servicios y consulta de radicado sin iniciar sesión.
- Consulta por radicado de PQRS o trámites (`PQRS-AAAA-NNNNNN` / `TRAM-AAAA-NNNNNN`).

### Ciudadano
- Registro, inicio de sesión y recuperación de acceso (genera contraseña temporal de demostración).
- Radicar PQRS con adjuntos, consultar historial, ver respuestas y estado.
- Solicitar trámites con formularios dinámicos definidos por el administrador.
- Notificaciones internas y edición de perfil.

### Funcionario
- Dashboard con indicadores y gráficas de sus PQRS asignadas.
- Gestión de PQRS: cambio de estado, respuesta formal con adjunto y notas internas/públicas.
- Gestión de trámites asignados e historial de actividades.

### Administrador
- Dashboard general con gráficas (Chart.js) calculadas en tiempo real.
- CRUD de usuarios (admin/funcionario/ciudadano), activación/desactivación y edición de perfiles.
- Gestión de ciudadanos, funcionarios y dependencias.
- Asignación de PQRS y trámites a funcionarios, cambio de estado y seguimiento.
- Catálogo de tipos de trámites con campos dinámicos.
- Reportes filtrables y exportación CSV (UTF-8 con BOM para Excel).
- Auditoría de acciones y configuración/cambio de contraseña.

## 5. Estructura del proyecto

```
/app
  core/         Bootstrap, Router, View, Response
  controllers/  Controladores por rol y módulo
  models/       Acceso a datos (PDO)
  services/     Radicados, archivos, notificaciones
  helpers/      Funciones globales, CSRF, Auth, Flash, Validator, Audit
  middleware/   Control de sesión y roles
  views/        Vistas por rol + layouts + errores
/config         app.php, database.php
/database       Esquema + datos demo y script de reinicio
/public         Front controller, assets locales (Bootstrap, Icons, Chart.js)
/routes         Definición de rutas
/storage        Uploads privados y logs (no accesibles por URL directa)
/tests          Pruebas de humo por CLI
```

## 6. Seguridad implementada

- Contraseñas con `password_hash()` (BCRYPT) y `password_verify()`.
- Token **CSRF** global para todas las peticiones `POST`.
- Consultas preparadas (PDO, `ATTR_EMULATE_PREPARES = false`).
- Escape de salida con `e()` (`htmlspecialchars`).
- Control de acceso por rol y verificación de propiedad de solicitudes/archivos.
- Archivos fuera de `public/` y descarga mediante `/archivos/descargar/{id}` con validación de permisos, extensión, MIME y tamaño.
- Auditoría de acciones sensibles.

## 7. Pruebas

- Verificación de sintaxis de todos los archivos PHP:

  ```bash
  find app config routes public database tests -name "*.php" -print0 | xargs -0 -n1 php -l
  ```

- Prueba de humo (conexión, esquema, datos demo y contraseñas):

  ```bash
  php tests/smoke.php
  ```

La guía paso a paso para la demostración está en [`docs/GUIA_DEMOSTRACION.md`](docs/GUIA_DEMOSTRACION.md).

## 8. Solución de problemas

- **Acentos/ñ dañados en la base de datos:** reimporte usando `--default-character-set=utf8mb4`.
- **Error 404 en todas las rutas:** revise que `mod_rewrite` esté activo y que `base_url` coincida con la carpeta real.
- **No se guardan adjuntos:** confirme permisos de escritura en `storage/uploads`.
- **Error de conexión a MySQL:** verifique que el servicio esté iniciado y los datos de `config/database.php`.

---

Proyecto con fines académicos y de demostración. No incluye envío real de correos ni pasarela de pagos.
