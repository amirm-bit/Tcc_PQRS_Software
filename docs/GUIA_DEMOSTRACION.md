# Guía de demostración — Sistema de Trámites y PQRS

Esta guía permite mostrar el sistema completo en aproximadamente **10–15 minutos**, recorriendo los tres roles y los flujos principales.

> Antes de iniciar: Apache y MySQL activos, base de datos importada y el sitio abierto en `http://localhost/alcaldia-lorica/`.
> Si desea partir de datos limpios: reimporte `database/alcaldia_lorica.sql`.

---

## 1. Portal público (sin iniciar sesión)

1. Abra **http://localhost/alcaldia-lorica/** y recorra Inicio, Acerca de y Servicios.
2. Entre a **Consultar radicado**.
3. Consulte el radicado de ejemplo **`PQRS-2026-000001`** → se muestra el estado y la trazabilidad pública.
4. Pruebe un radicado inexistente (ej. `PQRS-9999-999999`) → mensaje de advertencia.

**Mensaje clave:** el ciudadano puede hacer seguimiento sin crear cuenta.

---

## 2. Rol ciudadano

Inicie sesión con:

- Correo: `ciudadano@lorica.test`
- Contraseña: `Ciudadano123!`

1. **Dashboard:** indicadores de sus PQRS y trámites.
2. **Nueva PQRS:**
   - Tipo: *Petición*.
   - Asunto: “Solicitud de poda de árbol en el parque central”.
   - Descripción: mínimo 20 caracteres.
   - Medio de contacto: correo; complete correo y teléfono.
   - Adjunte un PDF/JPG (opcional) → demuestra la carga privada de archivos.
   - Radique → se genera un radicado `PQRS-AAAA-NNNNNN` y se abre el detalle.
3. **Mis PQRS:** verifique el estado *Radicada* y la línea de tiempo.
4. **Trámites disponibles → Certificado de residencia:** complete el formulario dinámico y radique → radicado `TRAM-AAAA-NNNNNN`.
5. **Notificaciones:** revise las alertas generadas.

**Mensaje clave:** radicación real con persistencia en base de datos, adjuntos y formularios dinámicos.

---

## 3. Rol administrador (asignación)

Cierre sesión e ingrese con:

- Correo: `admin@lorica.test`
- Contraseña: `Admin123!`

1. **Dashboard general:** tarjetas y gráficas (por tipo, estado, trámites, tendencia mensual y actividad de funcionarios).
2. **Gestión de PQRS →** abra la PQRS recién radicada.
   - En **Asignar responsable**, seleccione un funcionario y una dependencia → la solicitud pasa a *Asignada* y se notifica al funcionario y al ciudadano.
3. **Solicitudes de trámites →** abra el trámite recién radicado y asígnelo a un funcionario.
4. **Usuarios:** muestre el listado, cree o edite un usuario y active/desactive una cuenta.
5. **Dependencias:** cree una dependencia nueva (código único) y muestre el cambio de estado.
6. **Tipos de trámites:** cree un tipo con campos dinámicos usando el formato `nombre|Etiqueta|tipo|1`.
7. **Reportes:** aplique filtros y use **Exportar CSV** (se descarga un archivo compatible con Excel).
8. **Auditoría:** evidencie el registro de las acciones anteriores.
9. **Configuración:** muestre parámetros y el formulario de cambio de contraseña (no es necesario cambiarla).

**Mensaje clave:** control total, asignación, catálogos, reportes y trazabilidad.

---

## 4. Rol funcionario (respuesta oficial)

Cierre sesión e ingrese con:

- Correo: `funcionario@lorica.test`
- Contraseña: `Funcionario123!`

1. **Dashboard:** PQRS asignadas, pendientes, atendidas y gráfica por estado.
2. **PQRS asignadas → Gestionar** la solicitud asignada:
   - Cambie el estado a *En proceso* con una observación → se registra en el historial y se notifica al ciudadano.
   - En **Registrar respuesta formal**, redacte la respuesta (mínimo 20 caracteres), adjunte un documento si lo desea y emita → la PQRS pasa a *Respondida*.
   - En **Agregar observación**, marque/desmarque “Visible para el ciudadano” para demostrar notas internas vs. públicas.
3. **Gestión de trámites:** cambie el estado del trámite asignado y agregue seguimiento.
4. **Historial de actividades:** muestre sus acciones y seguimientos.

**Mensaje clave:** atención con respuesta oficial, adjuntos, notas internas y notificaciones.

---

## 5. Verificación final (ciudadano)

1. Vuelva a iniciar sesión como ciudadano.
2. Abra la PQRS en **Mis PQRS**:
   - Verá el estado actualizado, la respuesta oficial y la línea de tiempo (sin notas internas).
   - Podrá descargar la respuesta adjunta.
3. Revise **Notificaciones** con las alertas de asignación y respuesta.

---

## 6. Controles de seguridad para mostrar

- **CSRF:** envíe un `POST` sin token (o espere una sesión vieja) → respuesta `419` con página de sesión expirada.
- **Roles:** con sesión de ciudadano intente abrir `/admin/dashboard` → `403 Acceso denegado`.
- **Archivos privados:** con otra cuenta de ciudadano intente descargar un adjunto ajeno → `403`; como dueño o administrador → descarga correcta.
- **Contraseñas:** están almacenadas con BCRYPT (no en texto plano). Puede verificarlo en la base de datos.
- **Auditoría:** todas las acciones sensibles quedan registradas con usuario, fecha e IP.

---

## 7. Reinicio de la demostración

Para dejar el sistema como nuevo:

1. Reimporte la base de datos:

   ```bash
   "C:\xamppp\mysql\bin\mysql.exe" --default-character-set=utf8mb4 -u root < database/alcaldia_lorica.sql
   ```

2. (Opcional) Elimine los archivos cargados en `storage/uploads/pqrs`, `storage/uploads/tramites` y `storage/uploads/respuestas` conservando los `.gitkeep`.

---

## 8. Preguntas frecuentes en la sustentación

- **¿Usa frameworks?** No. PHP 8.2 puro con un front controller y enrutador propio; Bootstrap y Chart.js se sirven localmente.
- **¿Dónde están los archivos adjuntos?** En `storage/` (fuera de `public/`) y se sirven con control de permisos.
- **¿Cómo se garantiza la integridad?** Consultas preparadas, validación de entrada, escape de salida, CSRF y control de roles.
- **¿Cómo se calculan los indicadores?** Consultas SQL agregadas en los modelos y renderizadas con Chart.js.
- **¿Cómo se generan los radicados?** `RadicadoService` compone prefijo + año + consecutivo y evita duplicados.
