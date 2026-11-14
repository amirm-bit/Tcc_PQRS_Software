-- ============================================================
-- BASE DE DATOS: alcaldia_lorica
-- SISTEMA WEB DE TRÁMITES Y PQRS - ALCALDÍA MUNICIPAL DE LORICA
-- ============================================================

CREATE DATABASE IF NOT EXISTS `alcaldia_lorica` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `alcaldia_lorica`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. TABLA: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABLA: usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rol_id` INT NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `ultimo_acceso` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABLA: dependencias
DROP TABLE IF EXISTS `dependencias`;
CREATE TABLE `dependencias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NULL,
    `telefono` VARCHAR(50) NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABLA: ciudadanos
DROP TABLE IF EXISTS `ciudadanos`;
CREATE TABLE `ciudadanos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL UNIQUE,
    `tipo_documento` ENUM('CC', 'TI', 'CE', 'PASAPORTE', 'NIT') DEFAULT 'CC',
    `numero_documento` VARCHAR(30) NOT NULL UNIQUE,
    `nombres` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NOT NULL,
    `telefono` VARCHAR(30) NULL,
    `direccion` VARCHAR(200) NULL,
    `barrio` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ciudadanos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABLA: funcionarios
DROP TABLE IF EXISTS `funcionarios`;
CREATE TABLE `funcionarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL UNIQUE,
    `dependencia_id` INT NOT NULL,
    `cargo` VARCHAR(100) NOT NULL,
    `nombres` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NOT NULL,
    `telefono` VARCHAR(30) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_funcionarios_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_funcionarios_dependencia` FOREIGN KEY (`dependencia_id`) REFERENCES `dependencias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABLA: tipos_pqrs
DROP TABLE IF EXISTS `tipos_pqrs`;
CREATE TABLE `tipos_pqrs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` TEXT NULL,
    `dias_termino` INT DEFAULT 15,
    `activo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TABLA: estados_pqrs
DROP TABLE IF EXISTS `estados_pqrs`;
CREATE TABLE `estados_pqrs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(30) NOT NULL UNIQUE,
    `nombre` VARCHAR(60) NOT NULL,
    `color_badge` VARCHAR(20) DEFAULT 'secondary',
    `descripcion` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. TABLA: pqrs
DROP TABLE IF EXISTS `pqrs`;
CREATE TABLE `pqrs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `radicado` VARCHAR(30) NOT NULL UNIQUE,
    `ciudadano_id` INT NOT NULL,
    `tipo_pqrs_id` INT NOT NULL,
    `estado_pqrs_id` INT NOT NULL,
    `funcionario_id` INT NULL,
    `dependencia_id` INT NULL,
    `asunto` VARCHAR(255) NOT NULL,
    `descripcion` TEXT NOT NULL,
    `medio_contacto` ENUM('correo', 'telefono', 'direccion') DEFAULT 'correo',
    `email_contacto` VARCHAR(150) NOT NULL,
    `telefono_contacto` VARCHAR(30) NULL,
    `fecha_radicacion` DATETIME NOT NULL,
    `fecha_vencimiento` DATE NULL,
    `fecha_cierre` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pqrs_radicado` (`radicado`),
    INDEX `idx_pqrs_ciudadano` (`ciudadano_id`),
    INDEX `idx_pqrs_estado` (`estado_pqrs_id`),
    INDEX `idx_pqrs_funcionario` (`funcionario_id`),
    INDEX `idx_pqrs_fecha` (`fecha_radicacion`),
    CONSTRAINT `fk_pqrs_ciudadano` FOREIGN KEY (`ciudadano_id`) REFERENCES `ciudadanos` (`id`),
    CONSTRAINT `fk_pqrs_tipo` FOREIGN KEY (`tipo_pqrs_id`) REFERENCES `tipos_pqrs` (`id`),
    CONSTRAINT `fk_pqrs_estado` FOREIGN KEY (`estado_pqrs_id`) REFERENCES `estados_pqrs` (`id`),
    CONSTRAINT `fk_pqrs_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_pqrs_dependencia` FOREIGN KEY (`dependencia_id`) REFERENCES `dependencias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. TABLA: seguimiento_pqrs
DROP TABLE IF EXISTS `seguimiento_pqrs`;
CREATE TABLE `seguimiento_pqrs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pqrs_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `estado_anterior_id` INT NULL,
    `estado_nuevo_id` INT NOT NULL,
    `observacion` TEXT NOT NULL,
    `es_publico` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_seguimiento_pqrs` (`pqrs_id`),
    CONSTRAINT `fk_seg_pqrs` FOREIGN KEY (`pqrs_id`) REFERENCES `pqrs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_seg_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
    CONSTRAINT `fk_seg_est_ant` FOREIGN KEY (`estado_anterior_id`) REFERENCES `estados_pqrs` (`id`),
    CONSTRAINT `fk_seg_est_nue` FOREIGN KEY (`estado_nuevo_id`) REFERENCES `estados_pqrs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. TABLA: respuestas_pqrs
DROP TABLE IF EXISTS `respuestas_pqrs`;
CREATE TABLE `respuestas_pqrs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pqrs_id` INT NOT NULL,
    `funcionario_id` INT NOT NULL,
    `contenido` TEXT NOT NULL,
    `fecha_respuesta` DATETIME NOT NULL,
    `archivo_respuesta` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_resp_pqrs` FOREIGN KEY (`pqrs_id`) REFERENCES `pqrs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_resp_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. TABLA: tipos_tramites
DROP TABLE IF EXISTS `tipos_tramites`;
CREATE TABLE `tipos_tramites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(30) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT NOT NULL,
    `requisitos` TEXT NOT NULL,
    `instrucciones` TEXT NOT NULL,
    `detalle_campos` JSON NULL,
    `dias_termino` INT DEFAULT 10,
    `dependencia_id` INT NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tramites_dependencia` FOREIGN KEY (`dependencia_id`) REFERENCES `dependencias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. TABLA: estados_tramites
DROP TABLE IF EXISTS `estados_tramites`;
CREATE TABLE `estados_tramites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(30) NOT NULL UNIQUE,
    `nombre` VARCHAR(60) NOT NULL,
    `color_badge` VARCHAR(20) DEFAULT 'secondary',
    `descripcion` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. TABLA: solicitudes_tramites
DROP TABLE IF EXISTS `solicitudes_tramites`;
CREATE TABLE `solicitudes_tramites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `radicado` VARCHAR(30) NOT NULL UNIQUE,
    `ciudadano_id` INT NOT NULL,
    `tipo_tramite_id` INT NOT NULL,
    `estado_tramite_id` INT NOT NULL,
    `funcionario_id` INT NULL,
    `datos_adicionales` JSON NULL,
    `motivo_rechazo` TEXT NULL,
    `documento_generado` VARCHAR(255) NULL,
    `fecha_radicacion` DATETIME NOT NULL,
    `fecha_cierre` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_solic_radicado` (`radicado`),
    INDEX `idx_solic_ciudadano` (`ciudadano_id`),
    INDEX `idx_solic_estado` (`estado_tramite_id`),
    INDEX `idx_solic_funcionario` (`funcionario_id`),
    CONSTRAINT `fk_solic_ciudadano` FOREIGN KEY (`ciudadano_id`) REFERENCES `ciudadanos` (`id`),
    CONSTRAINT `fk_solic_tipo` FOREIGN KEY (`tipo_tramite_id`) REFERENCES `tipos_tramites` (`id`),
    CONSTRAINT `fk_solic_estado` FOREIGN KEY (`estado_tramite_id`) REFERENCES `estados_tramites` (`id`),
    CONSTRAINT `fk_solic_funcionario` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. TABLA: seguimiento_tramites
DROP TABLE IF EXISTS `seguimiento_tramites`;
CREATE TABLE `seguimiento_tramites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `solicitud_tramite_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `estado_anterior_id` INT NULL,
    `estado_nuevo_id` INT NOT NULL,
    `observacion` TEXT NOT NULL,
    `es_publico` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_seg_tramite` FOREIGN KEY (`solicitud_tramite_id`) REFERENCES `solicitudes_tramites` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_seg_tramite_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
    CONSTRAINT `fk_seg_tramite_est_ant` FOREIGN KEY (`estado_anterior_id`) REFERENCES `estados_tramites` (`id`),
    CONSTRAINT `fk_seg_tramite_est_nue` FOREIGN KEY (`estado_nuevo_id`) REFERENCES `estados_tramites` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. TABLA: archivos_adjuntos
DROP TABLE IF EXISTS `archivos_adjuntos`;
CREATE TABLE `archivos_adjuntos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tipo_objeto` ENUM('pqrs', 'tramite', 'respuesta') NOT NULL,
    `objeto_id` INT NOT NULL,
    `nombre_original` VARCHAR(255) NOT NULL,
    `nombre_guardado` VARCHAR(255) NOT NULL,
    `ruta` VARCHAR(255) NOT NULL,
    `tamano_bytes` BIGINT NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `subido_por_usuario_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_archivos_objeto` (`tipo_objeto`, `objeto_id`),
    CONSTRAINT `fk_archivos_usuario` FOREIGN KEY (`subido_por_usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. TABLA: notificaciones
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `enlace` VARCHAR(255) NULL,
    `leida` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_notificaciones_usuario` (`usuario_id`, `leida`),
    CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. TABLA: auditoria
DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `tabla_afectada` VARCHAR(50) NOT NULL,
    `registro_id` INT NULL,
    `detalles` TEXT NULL,
    `ip` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_auditoria_usuario` (`usuario_id`),
    INDEX `idx_auditoria_fecha` (`created_at`),
    CONSTRAINT `fk_audit_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DATOS INICIALES (CATÁLOGOS)
-- ============================================================

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'admin', 'Administrador General del Sistema'),
(2, 'funcionario', 'Funcionario de la Alcaldía encargado de atender y resolver trámites y PQRS'),
(3, 'ciudadano', 'Ciudadano solicitante y usuario de servicios públicos');

INSERT INTO `dependencias` (`id`, `codigo`, `nombre`, `email`, `telefono`, `activo`) VALUES
(1, 'SEC-GOB', 'Secretaría General y de Gobierno', 'gobierno@lorica-cordoba.gov.co', '(+57) 604 773 8001', 1),
(2, 'SEC-HAC', 'Secretaría de Hacienda y Finanzas Públicas', 'hacienda@lorica-cordoba.gov.co', '(+57) 604 773 8002', 1),
(3, 'SEC-PLA', 'Secretaría de Planeación y Ordenamiento Territorial', 'planeacion@lorica-cordoba.gov.co', '(+57) 604 773 8003', 1),
(4, 'SEC-SAL', 'Secretaría de Salud y Seguridad Social', 'salud@lorica-cordoba.gov.co', '(+57) 604 773 8004', 1),
(5, 'SEC-INF', 'Secretaría de Infraestructura y Obras Públicas', 'infraestructura@lorica-cordoba.gov.co', '(+57) 604 773 8005', 1),
(6, 'OFI-ATC', 'Oficina de Atención y Servicio al Ciudadano', 'atencionciudadano@lorica-cordoba.gov.co', '(+57) 604 773 8006', 1);

INSERT INTO `tipos_pqrs` (`id`, `nombre`, `descripcion`, `dias_termino`, `activo`) VALUES
(1, 'Petición', 'Solicitud formal para requerir información, documentos o prestación de servicios', 15, 1),
(2, 'Queja', 'Manifestación de inconformidad respecto a la conducta o atención irregular de un funcionario', 15, 1),
(3, 'Reclamo', 'Manifestación por la prestación indebida o deficiente de un servicio municipal', 15, 1),
(4, 'Sugerencia', 'Proposición o idea formulada para mejorar la atención, servicios o funciones públicas', 15, 1);

INSERT INTO `estados_pqrs` (`id`, `codigo`, `nombre`, `color_badge`, `descripcion`) VALUES
(1, 'radicada', 'Radicada', 'primary', 'Solicitud registrada exitosamente en el sistema'),
(2, 'pendiente_asignacion', 'Pendiente de asignación', 'warning', 'Esperando designación de funcionario o dependencia encargada'),
(3, 'asignada', 'Asignada', 'info', 'Asignada formalmente al funcionario responsable'),
(4, 'en_revision', 'En revisión', 'secondary', 'Documentación y antecedentes bajo evaluación'),
(5, 'en_proceso', 'En proceso', 'primary', 'Trámite activo de respuesta y preparación de concepto'),
(6, 'respondida', 'Respondida', 'success', 'Respuesta oficial redactada y remitida al ciudadano'),
(7, 'cerrada', 'Cerrada', 'dark', 'Gestión administrativa concluida formalmente');

INSERT INTO `tipos_tramites` (`id`, `codigo`, `nombre`, `descripcion`, `requisitos`, `instrucciones`, `detalle_campos`, `dias_termino`, `dependencia_id`, `activo`) VALUES
(1, 'CERT-RES', 'Certificado de residencia', 'Certificación expedida para constatar la permanencia y residencia habitual del ciudadano en el Municipio de Lorica (Demostración académica).', '- Fotocopia legible del documento de identidad.\n- Recibo de servicio público reciente (agua, luz o gas) del predio.\n- Declaración o manifestación bajo juramento.', '1. Diligencie el formulario con su dirección exacta y barrio.\n2. Adjunte el recibo de servicio público en PDF o JPG.\n3. Espere la revisión y validación institucional.', '[{"name":"direccion_residencia","label":"Dirección de residencia","type":"text","required":true},{"name":"barrio","label":"Barrio o vereda","type":"text","required":true},{"name":"tiempo_residencia","label":"Tiempo de residencia en Lorica","type":"text","required":true},{"name":"motivo","label":"Motivo de la solicitud","type":"textarea","required":true}]', 5, 1, 1),
(2, 'CERT-VEC', 'Certificado de vecindad', 'Constancia formal que acredita vecindad y arraigo en el territorio municipal para fines laborales o académicos (Demostración académica).', '- Copia de la cédula de ciudadanía.\n- Constancia de junta de acción comunal o testigos de la vereda/barrio.\n- Tiempo mínimo de permanencia en el municipio.', '1. Ingrese los datos de su residencia y periodo vivido en Lorica.\n2. Adjunte soporte vecinal.\n3. El funcionario validará la solicitud.', '[{"name":"direccion_residencia","label":"Dirección de residencia","type":"text","required":true},{"name":"barrio","label":"Barrio o vereda","type":"text","required":true},{"name":"tiempo_residencia","label":"Tiempo de residencia en Lorica","type":"text","required":true},{"name":"testigo","label":"Nombre de testigo o JAC (opcional)","type":"text","required":false}]', 5, 1, 1),
(3, 'SOL-INFO', 'Solicitud de información administrativa', 'Requerimiento ciudadano para acceder a datos públicos, decretos, resoluciones y estadísticas oficiales.', '- Identificación del solicitante.\n- Descripción puntual de la información requerida.', '1. Detalle con claridad la información o estadística solicitada.\n2. Especifique el periodo o tema de consulta.', '[{"name":"asunto_info","label":"Información o dato solicitado","type":"textarea","required":true},{"name":"periodo","label":"Periodo o vigencia de interés","type":"text","required":false}]', 10, 6, 1),
(4, 'SOL-DOCS', 'Solicitud de documentos administrativos', 'Petición formal de copias de actas, acuerdos municipales, planos o resoluciones no reservadas.', '- Formulario con el tipo de documento específico.\n- Justificación o motivo de la consulta.', '1. Indique el año y número de radicado o acto administrativo.\n2. Adjunte documentos de interés si aplica.', '[{"name":"documento_solicitado","label":"Documento solicitado","type":"textarea","required":true},{"name":"anno","label":"Año del documento","type":"text","required":false}]', 10, 1, 1),
(5, 'PAZ-SALVO', 'Otros trámites disponibles', 'Solicitud genérica para otros certificados administrativos municipales contemplados en ventanilla única.', '- Documento de identidad.\n- Carta o memorial explicando la solicitud específica.', '1. Redacte su petición concreta en el formulario.\n2. Cargue sus soportes en formato PDF.', '[{"name":"tramite_solicitado","label":"Trámite o certificado requerido","type":"text","required":true},{"name":"descripcion","label":"Descripción de la solicitud","type":"textarea","required":true}]', 8, 2, 1);

INSERT INTO `estados_tramites` (`id`, `codigo`, `nombre`, `color_badge`, `descripcion`) VALUES
(1, 'recibida', 'Recibida', 'info', 'Solicitud radicada en ventanilla digital'),
(2, 'pendiente_revision', 'Pendiente de revisión', 'warning', 'En cola de verificación de requisitos documentales'),
(3, 'en_revision', 'En revisión', 'primary', 'Documentación en verificación por funcionario'),
(4, 'requiere_info', 'Requiere información adicional', 'warning', 'Documentos incompletos o aclaración requerida'),
(5, 'aprobada', 'Aprobada', 'success', 'Solicitud aprobada y documento certificado generado'),
(6, 'rechazada', 'Rechazada', 'danger', 'Solicitud no cumple con los requisitos normativos'),
(7, 'finalizada', 'Finalizada', 'dark', 'Trámite completado y archivado satisfactoriamente');

-- ============================================================
-- USUARIOS DE DEMOSTRACIÓN (Contraseñas cifradas con BCRYPT)
-- Admin123!       => $2y$10$dtT7BwWsX.SyL1hMbxoXNuzj4TKasEZ3JkuiqwkPKBZVqlYkHpsyK
-- Funcionario123! => $2y$10$4xe7U/CEJpx4OCZeMRhaYOrxPEDMYgGoFkSa3a1VoY399Tf2hmnf6
-- Ciudadano123!   => $2y$10$wEmwIBXkdhRvfdNljDi2NuK3qTdNnbMo3rGU55L6KTtc1fKvNt5gO
-- ============================================================

INSERT INTO `usuarios` (`id`, `rol_id`, `email`, `password`, `activo`) VALUES
(1, 1, 'admin@lorica.test', '$2y$10$dtT7BwWsX.SyL1hMbxoXNuzj4TKasEZ3JkuiqwkPKBZVqlYkHpsyK', 1),
(2, 2, 'funcionario@lorica.test', '$2y$10$4xe7U/CEJpx4OCZeMRhaYOrxPEDMYgGoFkSa3a1VoY399Tf2hmnf6', 1),
(3, 3, 'ciudadano@lorica.test', '$2y$10$wEmwIBXkdhRvfdNljDi2NuK3qTdNnbMo3rGU55L6KTtc1fKvNt5gO', 1),
(4, 2, 'carlos.mendoza@lorica.test', '$2y$10$4xe7U/CEJpx4OCZeMRhaYOrxPEDMYgGoFkSa3a1VoY399Tf2hmnf6', 1),
(5, 3, 'maria.gomez@lorica.test', '$2y$10$wEmwIBXkdhRvfdNljDi2NuK3qTdNnbMo3rGU55L6KTtc1fKvNt5gO', 1),
(6, 3, 'jose.martinez@lorica.test', '$2y$10$wEmwIBXkdhRvfdNljDi2NuK3qTdNnbMo3rGU55L6KTtc1fKvNt5gO', 1);

INSERT INTO `funcionarios` (`id`, `usuario_id`, `dependencia_id`, `cargo`, `nombres`, `apellidos`, `telefono`) VALUES
(1, 2, 6, 'Profesional Universitario de Atención al Ciudadano', 'Ana Milena', 'Paternina Ramos', '3104567890'),
(2, 4, 3, 'Ingeniero de Planeación Municipal', 'Carlos Andrés', 'Mendoza Hoyos', '3127891234');

INSERT INTO `ciudadanos` (`id`, `usuario_id`, `tipo_documento`, `numero_documento`, `nombres`, `apellidos`, `telefono`, `direccion`, `barrio`) VALUES
(1, 3, 'CC', '1067892345', 'Juan Carlos', 'Doria Negrete', '3008912345', 'Carrera 15 # 12-40', 'San Pedro'),
(2, 5, 'CC', '1065432109', 'María Fernanda', 'Gómez Jattin', '3146781200', 'Calle 8 # 19-33', 'Arenales'),
(3, 6, 'CC', '1068998877', 'José Gregorio', 'Martínez Burgos', '3114455667', 'Calle 1 # 4-15', 'Centro');

-- ============================================================
-- REGISTROS FICTICIOS DE DEMOSTRACIÓN (PQRS)
-- ============================================================

INSERT INTO `pqrs` (`id`, `radicado`, `ciudadano_id`, `tipo_pqrs_id`, `estado_pqrs_id`, `funcionario_id`, `dependencia_id`, `asunto`, `descripcion`, `medio_contacto`, `email_contacto`, `telefono_contacto`, `fecha_radicacion`, `fecha_vencimiento`, `fecha_cierre`) VALUES
(1, 'PQRS-2026-000001', 1, 1, 6, 1, 1, 'Solicitud de copia del Plan de Desarrollo Municipal 2024-2027', 'Solicito respetuosamente copia digital del Plan de Desarrollo Municipal aprobado para fines de investigación académica sobre el Río Sinú.', 'correo', 'ciudadano@lorica.test', '3008912345', '2026-01-10 09:15:00', '2026-01-31', '2026-01-20 15:30:00'),
(2, 'PQRS-2026-000002', 1, 2, 7, 1, 6, 'Inconformidad en demora de trámite en ventanilla única', 'El pasado lunes 12 de enero me acerqué al palacio municipal y la fila tardó más de dos horas sin orientación clara al adulto mayor.', 'correo', 'ciudadano@lorica.test', '3008912345', '2026-01-14 11:00:00', '2026-02-04', '2026-01-25 17:00:00'),
(3, 'PQRS-2026-000003', 2, 3, 5, 2, 5, 'Hueco profundo en la calle 14 con carrera 20 de Arenales', 'Presento reclamo por el mal estado de la vía vehicular donde se abrió un cráter que representa peligro para motociclistas.', 'telefono', 'maria.gomez@lorica.test', '3146781200', '2026-02-01 08:30:00', '2026-02-22', NULL),
(4, 'PQRS-2026-000004', 3, 4, 3, 1, 6, 'Sugerencia para implementar turnero digital en la sede central', 'Propongo colocar un quiosco con pantalla táctil para asignar turnos y evitar aglomeraciones en horas pico.', 'correo', 'jose.martinez@lorica.test', '3114455667', '2026-02-05 14:20:00', '2026-02-26', NULL),
(5, 'PQRS-2026-000005', 1, 1, 5, 2, 3, 'Información sobre licencias de construcción en el Malecón', 'Deseo conocer la normativa de uso de suelo comercial para la zona ribereña del Malecón del Sinú.', 'correo', 'ciudadano@lorica.test', '3008912345', '2026-02-08 10:45:00', '2026-03-01', NULL),
(6, 'PQRS-2026-000006', 2, 2, 4, 1, 4, 'Falta de brigadas de fumigación contra el dengue en barrio Arenales', 'Se solicita fumigación prioritaria ante el aumento de zancudos por temporada de lluvias cerca de la ciénaga.', 'correo', 'maria.gomez@lorica.test', '3146781200', '2026-02-12 16:10:00', '2026-03-05', NULL),
(7, 'PQRS-2026-000007', 3, 1, 2, NULL, 2, 'Consulta sobre fechas de descuento de impuesto predial 2026', 'Agradezco me informen el calendario tributario para el pronto pago con incentivo del predial unificado.', 'correo', 'jose.martinez@lorica.test', '3114455667', '2026-02-15 09:00:00', '2026-03-08', NULL),
(8, 'PQRS-2026-000008', 1, 3, 1, NULL, NULL, 'Alumbrado público defectuoso en parque central La Concordia', 'Luminarias apagadas desde el fin de semana pasado, generando inseguridad en horas nocturnas.', 'telefono', 'ciudadano@lorica.test', '3008912345', '2026-02-18 18:30:00', '2026-03-11', NULL),
(9, 'PQRS-2026-000009', 2, 4, 6, 1, 6, 'Felicitación por atención en feria artesanal y cultural', 'Excelente organización de las muestras gastronómicas y apoyo a los artesanos locales en el Mercado Público.', 'correo', 'maria.gomez@lorica.test', '3146781200', '2026-02-20 12:00:00', '2026-03-13', '2026-02-24 10:15:00'),
(10, 'PQRS-2026-000010', 3, 1, 7, 2, 3, 'Petición de plano topográfico de la vereda El Rodeo', 'Requiero información cartográfica para deslinde de predio rural.', 'correo', 'jose.martinez@lorica.test', '3114455667', '2026-02-22 15:40:00', '2026-03-15', '2026-03-01 16:30:00'),
(11, 'PQRS-2026-000011', 1, 2, 1, NULL, NULL, 'Ruido excesivo de establecimiento en zona residencial del barrio San Pedro', 'Solicitud de inspección de policía ambiental por emisión sonora durante la madrugada.', 'correo', 'ciudadano@lorica.test', '3008912345', '2026-03-02 08:15:00', '2026-03-23', NULL),
(12, 'PQRS-2026-000012', 2, 1, 5, 1, 1, 'Copia de decreto sobre horarios de cargue y descargue en el centro', 'Solicito norma vigente que regula movilidad pesada en el sector histórico de Lorica.', 'correo', 'maria.gomez@lorica.test', '3146781200', '2026-03-04 11:25:00', '2026-03-25', NULL);

-- Historial / Seguimiento PQRS
INSERT INTO `seguimiento_pqrs` (`pqrs_id`, `usuario_id`, `estado_anterior_id`, `estado_nuevo_id`, `observacion`, `es_publico`, `created_at`) VALUES
(1, 3, NULL, 1, 'Radicación inicial exitosa por parte del ciudadano.', 1, '2026-01-10 09:15:00'),
(1, 1, 1, 3, 'Asignado a funcionario Ana Milena Paternina para trámite.', 1, '2026-01-11 10:00:00'),
(1, 2, 3, 5, 'En proceso: recopilando el archivo PDF oficial de Secretaría General.', 1, '2026-01-15 14:00:00'),
(1, 2, 5, 6, 'Respuesta emitida formalmente adjuntando enlace del acuerdo municipal.', 1, '2026-01-20 15:30:00'),
(2, 3, NULL, 1, 'Radicación ciudadana por ventanilla.', 1, '2026-01-14 11:00:00'),
(2, 1, 1, 3, 'Asignado a control de servicio al ciudadano.', 1, '2026-01-15 08:30:00'),
(2, 2, 3, 7, 'Se implementaron medidas de orientación preferencial y se dio cierre.', 1, '2026-01-25 17:00:00'),
(3, 5, NULL, 1, 'Radicado por María Fernanda Gómez.', 1, '2026-02-01 08:30:00'),
(3, 1, 1, 3, 'Asignado a infraestructura vial.', 1, '2026-02-02 09:00:00'),
(3, 4, 3, 5, 'Inspección técnica programada con cuadrilla de bacheo.', 1, '2026-02-05 11:30:00'),
(9, 5, NULL, 1, 'Registro de felicitación y sugerencia cultural.', 1, '2026-02-20 12:00:00'),
(9, 2, 1, 6, 'Se transmitió la felicitación al comité de cultura y turismo.', 1, '2026-02-24 10:15:00');

-- Respuestas formales a PQRS
INSERT INTO `respuestas_pqrs` (`pqrs_id`, `funcionario_id`, `contenido`, `fecha_respuesta`, `created_at`) VALUES
(1, 1, 'Estimado ciudadano Juan Carlos Doria: De manera atenta le informamos que el Plan de Desarrollo Municipal 2024-2027 "Lorica Avanza con su Gente" se encuentra disponible públicamente. Ponemos a su disposición copia en la Secretaría General de Gobierno y en el portal institucional.', '2026-01-20 15:30:00', '2026-01-20 15:30:00'),
(9, 1, 'Apreciada ciudadana María Fernanda: Agradecemos sinceramente su valioso reconocimiento a nuestros artesanos y a la Dirección de Cultura y Turismo de Lorica. Continuaremos fortaleciendo estos espacios.', '2026-02-24 10:15:00', '2026-02-24 10:15:00');

-- ============================================================
-- REGISTROS FICTICIOS DE DEMOSTRACIÓN (TRÁMITES)
-- ============================================================

INSERT INTO `solicitudes_tramites` (`id`, `radicado`, `ciudadano_id`, `tipo_tramite_id`, `estado_tramite_id`, `funcionario_id`, `datos_adicionales`, `motivo_rechazo`, `fecha_radicacion`, `fecha_cierre`) VALUES
(1, 'TRAM-2026-000001', 1, 1, 5, 1, '{"tiempo_residencia": "12 anos", "motivo": "Demostracion academica y tramite de subsidio", "barrio": "San Pedro"}', NULL, '2026-01-12 10:00:00', '2026-01-16 16:00:00'),
(2, 'TRAM-2026-000002', 2, 2, 5, 1, '{"tiempo_residencia": "8 anos", "motivo": "Presentacion de hoja de vida", "barrio": "Arenales"}', NULL, '2026-01-18 14:30:00', '2026-01-22 11:00:00'),
(3, 'TRAM-2026-000003', 3, 3, 3, 2, '{"asunto_info": "Estadisticas de censo comercial 2025", "periodo": "2025"}', NULL, '2026-02-02 09:15:00', NULL),
(4, 'TRAM-2026-000004', 1, 4, 4, 1, '{"documento_solicitado": "Copia de resolucion de estratificacion predio 010203", "anno": "2023"}', 'Falta adjuntar certificado de libertad y tradición o recibo predial legible.', '2026-02-10 16:45:00', NULL),
(5, 'TRAM-2026-000005', 2, 1, 1, NULL, '{"tiempo_residencia": "5 anos", "motivo": "Matricula universitaria", "barrio": "Arenales"}', NULL, '2026-02-25 10:20:00', NULL),
(6, 'TRAM-2026-000006', 3, 2, 6, 1, '{"tiempo_residencia": "2 meses", "motivo": "Contrato", "barrio": "Centro"}', 'No cumple con el tiempo mínimo requerido de permanencia demostrable para vecindad (mínimo 6 meses).', '2026-02-28 11:30:00', '2026-03-02 14:00:00'),
(7, 'TRAM-2026-000007', 1, 1, 2, 1, '{"tiempo_residencia": "15 anos", "motivo": "Tramite notarial", "barrio": "San Pedro"}', NULL, '2026-03-05 08:40:00', NULL);

-- Historial Trámites
INSERT INTO `seguimiento_tramites` (`solicitud_tramite_id`, `usuario_id`, `estado_anterior_id`, `estado_nuevo_id`, `observacion`, `es_publico`, `created_at`) VALUES
(1, 3, NULL, 1, 'Radicación digital de Certificado de residencia.', 1, '2026-01-12 10:00:00'),
(1, 2, 1, 3, 'Verificación de recibo de servicios y documento de identidad.', 1, '2026-01-13 11:00:00'),
(1, 2, 3, 5, 'Certificado aprobado y expedido con firma digital de demostración.', 1, '2026-01-16 16:00:00'),
(4, 3, NULL, 1, 'Radicación de solicitud de copias.', 1, '2026-02-10 16:45:00'),
(4, 2, 1, 4, 'Se solicita al ciudadano aportar recibo legible o certificado catastral.', 1, '2026-02-12 10:00:00'),
(6, 6, NULL, 1, 'Radicación de certificado de vecindad.', 1, '2026-02-28 11:30:00'),
(6, 2, 1, 6, 'Rechazado por no cumplir con la antigüedad mínima según constancia aportada.', 1, '2026-03-02 14:00:00');

-- Notificaciones de ejemplo
INSERT INTO `notificaciones` (`usuario_id`, `titulo`, `mensaje`, `enlace`, `leida`, `created_at`) VALUES
(3, 'PQRS Respondida', 'Su solicitud con radicado PQRS-2026-000001 ha recibido respuesta oficial.', 'pqrs/detalle?radicado=PQRS-2026-000001', 1, '2026-01-20 15:30:00'),
(3, 'Certificado Aprobado', 'Su trámite TRAM-2026-000001 ha sido aprobado satisfactoriamente.', 'tramites/detalle?radicado=TRAM-2026-000001', 0, '2026-01-16 16:00:00'),
(2, 'Nueva PQRS Asignada', 'Se le ha asignado el radicado PQRS-2026-000004 para atención.', 'funcionario/pqrs/gestionar?id=4', 0, '2026-02-05 14:25:00');

-- Auditoría inicial
INSERT INTO `auditoria` (`usuario_id`, `accion`, `tabla_afectada`, `registro_id`, `detalles`, `ip`) VALUES
(1, 'CREAR_USUARIOS_DEMO', 'usuarios', 1, 'Inicialización del sistema y cuentas semilla para demostración local', '127.0.0.1'),
(3, 'RADICAR_PQRS', 'pqrs', 1, 'Radicación de PQRS-2026-000001 por ciudadano Juan Carlos Doria', '127.0.0.1'),
(2, 'RESPONDER_PQRS', 'respuestas_pqrs', 1, 'Emisión de respuesta formal a PQRS-2026-000001', '127.0.0.1');
