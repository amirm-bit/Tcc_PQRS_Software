-- ============================================================
-- SCRIPT DE SEMILLA / REINICIO DE DEMOSTRACIÓN: seed_demo.sql
-- Ejecutar en caso de desear restaurar los datos iniciales
-- ============================================================

USE `alcaldia_lorica`;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `notificaciones`;
DELETE FROM `auditoria`;
DELETE FROM `archivos_adjuntos`;
DELETE FROM `respuestas_pqrs`;
DELETE FROM `seguimiento_pqrs`;
DELETE FROM `pqrs`;
DELETE FROM `seguimiento_tramites`;
DELETE FROM `solicitudes_tramites`;
DELETE FROM `ciudadanos`;
DELETE FROM `funcionarios`;
DELETE FROM `usuarios`;

-- Restaurar usuarios de demostración
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

-- PQRS
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

-- Historial PQRS
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

-- Respuestas
INSERT INTO `respuestas_pqrs` (`pqrs_id`, `funcionario_id`, `contenido`, `fecha_respuesta`, `created_at`) VALUES
(1, 1, 'Estimado ciudadano Juan Carlos Doria: De manera atenta le informamos que el Plan de Desarrollo Municipal 2024-2027 "Lorica Avanza con su Gente" se encuentra disponible públicamente. Ponemos a su disposición copia en la Secretaría General de Gobierno y en el portal institucional.', '2026-01-20 15:30:00', '2026-01-20 15:30:00'),
(9, 1, 'Apreciada ciudadana María Fernanda: Agradecemos sinceramente su valioso reconocimiento a nuestros artesanos y a la Dirección de Cultura y Turismo de Lorica. Continuaremos fortaleciendo estos espacios.', '2026-02-24 10:15:00', '2026-02-24 10:15:00');

-- Trámites
INSERT INTO `solicitudes_tramites` (`id`, `radicado`, `ciudadano_id`, `tipo_tramite_id`, `estado_tramite_id`, `funcionario_id`, `datos_adicionales`, `motivo_rechazo`, `fecha_radicacion`, `fecha_cierre`) VALUES
(1, 'TRAM-2026-000001', 1, 1, 5, 1, '{"tiempo_residencia": "12 anos", "motivo": "Demostracion academica y tramite de subsidio", "barrio": "San Pedro"}', NULL, '2026-01-12 10:00:00', '2026-01-16 16:00:00'),
(2, 'TRAM-2026-000002', 2, 2, 5, 1, '{"tiempo_residencia": "8 anos", "motivo": "Presentacion de hoja de vida", "barrio": "Arenales"}', NULL, '2026-01-18 14:30:00', '2026-01-22 11:00:00'),
(3, 'TRAM-2026-000003', 3, 3, 3, 2, '{"asunto_info": "Estadisticas de censo comercial 2025", "periodo": "2025"}', NULL, '2026-02-02 09:15:00', NULL),
(4, 'TRAM-2026-000004', 1, 4, 4, 1, '{"documento_solicitado": "Copia de resolucion de estratificacion predio 010203", "anno": "2023"}', 'Falta adjuntar certificado de libertad y tradición o recibo predial legible.', '2026-02-10 16:45:00', NULL),
(5, 'TRAM-2026-000005', 2, 1, 1, NULL, '{"tiempo_residencia": "5 anos", "motivo": "Matricula universitaria", "barrio": "Arenales"}', NULL, '2026-02-25 10:20:00', NULL),
(6, 'TRAM-2026-000006', 3, 2, 6, 1, '{"tiempo_residencia": "2 meses", "motivo": "Contrato", "barrio": "Centro"}', 'No cumple con el tiempo mínimo requerido de permanencia demostrable para vecindad (mínimo 6 meses).', '2026-02-28 11:30:00', '2026-03-02 14:00:00'),
(7, 'TRAM-2026-000007', 1, 1, 2, 1, '{"tiempo_residencia": "15 anos", "motivo": "Tramite notarial", "barrio": "San Pedro"}', NULL, '2026-03-05 08:40:00', NULL);

-- Seguimiento Trámites
INSERT INTO `seguimiento_tramites` (`solicitud_tramite_id`, `usuario_id`, `estado_anterior_id`, `estado_nuevo_id`, `observacion`, `es_publico`, `created_at`) VALUES
(1, 3, NULL, 1, 'Radicación digital de Certificado de residencia.', 1, '2026-01-12 10:00:00'),
(1, 2, 1, 3, 'Verificación de recibo de servicios y documento de identidad.', 1, '2026-01-13 11:00:00'),
(1, 2, 3, 5, 'Certificado aprobado y expedido con firma digital de demostración.', 1, '2026-01-16 16:00:00'),
(4, 3, NULL, 1, 'Radicación de solicitud de copias.', 1, '2026-02-10 16:45:00'),
(4, 2, 1, 4, 'Se solicita al ciudadano aportar recibo legible o certificado catastral.', 1, '2026-02-12 10:00:00'),
(6, 6, NULL, 1, 'Radicación de certificado de vecindad.', 1, '2026-02-28 11:30:00'),
(6, 2, 1, 6, 'Rechazado por no cumplir con la antigüedad mínima según constancia aportada.', 1, '2026-03-02 14:00:00');

SET FOREIGN_KEY_CHECKS = 1;
