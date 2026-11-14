<?php
// app/controllers/PqrsFuncionarioController.php - Gestión de PQRS asignadas

class PqrsFuncionarioController {
    private function funcionarioActual(): array {
        $funcionario = Usuario::funcionarioPorUsuario((int)Auth::id());
        if (!$funcionario) {
            flash('danger', 'No se encontró el perfil de funcionario.');
            Response::redirect('logout');
        }
        return $funcionario;
    }

    public function index(): void {
        $funcionario = $this->funcionarioActual();
        $filtros = [
            'q'      => trim($_GET['q'] ?? ''),
            'estado' => ($_GET['estado'] ?? '') !== '' ? (int)$_GET['estado'] : null,
        ];
        $pqrs = Pqrs::porFuncionario((int)$funcionario['id'], $filtros);

        View::render('funcionario/pqrs_index', [
            'pqrs'    => $pqrs,
            'filtros' => $filtros,
            'estados' => Pqrs::estados(),
        ], 'panel', [
            'title'       => 'PQRS asignadas',
            'pageTitle'   => 'Mis PQRS asignadas',
            'pageSubtitle'=> 'Solicitudes bajo su responsabilidad',
            'activeMenu'  => 'pqrs',
        ]);
    }

    public function pendientes(): void {
        $funcionario = $this->funcionarioActual();
        $todas = Pqrs::porFuncionario((int)$funcionario['id']);
        $pqrs = array_values(array_filter($todas, fn($p) => (int)$p['estado_pqrs_id'] <= 5));

        View::render('funcionario/pqrs_pendientes', [
            'pqrs' => $pqrs,
        ], 'panel', [
            'title'       => 'Solicitudes pendientes',
            'pageTitle'   => 'Solicitudes pendientes',
            'pageSubtitle'=> 'PQRS que requieren gestión',
            'activeMenu'  => 'pendientes',
        ]);
    }

    public function gestionar(string $id): void {
        $funcionario = $this->funcionarioActual();
        $pqrs = Pqrs::detalle((int)$id);
        if (!$pqrs || (int)$pqrs['funcionario_id'] !== (int)$funcionario['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
            return;
        }

        View::render('funcionario/pqrs_gestionar', [
            'pqrs'        => $pqrs,
            'seguimiento' => Pqrs::seguimiento((int)$pqrs['id'], false),
            'respuestas'  => Pqrs::respuestas((int)$pqrs['id']),
            'archivos'    => UploadService::findByObjeto('pqrs', (int)$pqrs['id']),
            'estados'     => Pqrs::estados(),
        ], 'panel', [
            'title'       => 'Gestionar PQRS',
            'pageTitle'   => 'Gestión de la solicitud',
            'pageSubtitle'=> $pqrs['radicado'],
            'activeMenu'  => 'pqrs',
        ]);
    }

    private function verificarAsignacion(int $pqrsId): array {
        $funcionario = $this->funcionarioActual();
        $pqrs = Pqrs::find($pqrsId);
        if (!$pqrs || (int)$pqrs['funcionario_id'] !== (int)$funcionario['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
            exit;
        }
        return [$funcionario, $pqrs];
    }

    public function cambiarEstado(string $id): void {
        [$funcionario, $pqrs] = $this->verificarAsignacion((int)$id);

        $estadoId = (int)($_POST['estado_pqrs_id'] ?? 0);
        $observacion = trim($_POST['observacion'] ?? '');
        $estadosValidos = array_map(fn($e) => (int)$e['id'], Pqrs::estados());

        if (!in_array($estadoId, $estadosValidos, true)) {
            flash('danger', 'El estado seleccionado no es válido.');
            Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
        }

        if ($observacion === '') {
            flash('danger', 'Debe registrar una observación para el cambio de estado.');
            Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
        }

        $anterior = (int)$pqrs['estado_pqrs_id'];
        if ($anterior === $estadoId) {
            flash('info', 'La solicitud ya se encuentra en el estado seleccionado.');
            Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
        }

        $fechaCierre = in_array($estadoId, [6, 7], true) ? date('Y-m-d H:i:s') : null;

        Pqrs::cambiarEstado((int)$pqrs['id'], $estadoId, $fechaCierre);
        Pqrs::agregarSeguimiento((int)$pqrs['id'], (int)Auth::id(), $anterior, $estadoId, $observacion, true);
        Audit::log('CAMBIAR_ESTADO_PQRS', 'pqrs', (int)$pqrs['id'], 'Estado actualizado a ID ' . $estadoId);

        // Notificar al ciudadano
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT c.usuario_id FROM pqrs p INNER JOIN ciudadanos c ON c.id = p.ciudadano_id WHERE p.id = :id");
        $stmt->execute(['id' => (int)$pqrs['id']]);
        $ciudadanoUsuario = $stmt->fetchColumn();
        if ($ciudadanoUsuario) {
            NotificationService::crear((int)$ciudadanoUsuario, 'Actualización de su PQRS', 'La solicitud ' . $pqrs['radicado'] . ' cambió de estado. Consulte el seguimiento.', 'ciudadano/pqrs/detalle/' . (int)$pqrs['id']);
        }

        flash('success', 'El estado de la solicitud fue actualizado y registrado en el historial.');
        Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
    }

    public function responder(string $id): void {
        [$funcionario, $pqrs] = $this->verificarAsignacion((int)$id);

        $contenido = trim($_POST['contenido'] ?? '');
        if (mb_strlen($contenido) < 20) {
            flash('danger', 'La respuesta debe tener al menos 20 caracteres.');
            Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
        }

        $archivoRespuesta = null;
        if (!empty($_FILES['archivo_respuesta']['name'])) {
            try {
                $meta = UploadService::store($_FILES['archivo_respuesta'], 'respuestas');
                if ($meta) {
                    $archivoRespuesta = $meta['ruta'];
                }
            } catch (Exception $e) {
                flash('danger', 'Archivo de respuesta inválido: ' . $e->getMessage());
                Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
            }
        }

        try {
            $respuestaId = Pqrs::crearRespuesta((int)$pqrs['id'], (int)$funcionario['id'], $contenido, $archivoRespuesta);
            if ($archivoRespuesta) {
                UploadService::register((int)$respuestaId, 'respuesta', [
                    'nombre_original' => $_FILES['archivo_respuesta']['name'],
                    'nombre_guardado' => basename($archivoRespuesta),
                    'ruta'            => $archivoRespuesta,
                    'tamano_bytes'    => (int)$_FILES['archivo_respuesta']['size'],
                    'mime_type'       => $_FILES['archivo_respuesta']['type'] ?: 'application/octet-stream',
                ]);
            }

            $estadoResp = Pqrs::estadoPorCodigo('respondida');
            Pqrs::cambiarEstado((int)$pqrs['id'], (int)$estadoResp['id'], date('Y-m-d H:i:s'));
            Pqrs::agregarSeguimiento((int)$pqrs['id'], (int)Auth::id(), (int)$pqrs['estado_pqrs_id'], (int)$estadoResp['id'], 'Respuesta oficial emitida al ciudadano.', true);
            Audit::log('RESPONDER_PQRS', 'respuestas_pqrs', $respuestaId, 'Respuesta a ' . $pqrs['radicado']);

            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT c.usuario_id FROM pqrs p INNER JOIN ciudadanos c ON c.id = p.ciudadano_id WHERE p.id = :id");
            $stmt->execute(['id' => (int)$pqrs['id']]);
            $ciudadanoUsuario = $stmt->fetchColumn();
            if ($ciudadanoUsuario) {
                NotificationService::crear((int)$ciudadanoUsuario, 'PQRS respondida', 'Su solicitud ' . $pqrs['radicado'] . ' ha recibido respuesta oficial.', 'ciudadano/pqrs/detalle/' . (int)$pqrs['id']);
            }

            flash('success', 'La respuesta oficial fue registrada y notificada al ciudadano.');
        } catch (Exception $e) {
            error_log('Error al responder PQRS: ' . $e->getMessage());
            flash('danger', 'No fue posible registrar la respuesta.');
        }

        Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
    }

    public function agregarSeguimiento(string $id): void {
        [$funcionario, $pqrs] = $this->verificarAsignacion((int)$id);

        $observacion = trim($_POST['observacion'] ?? '');
        $esPublico = isset($_POST['es_publico']) ? true : false;

        if ($observacion === '') {
            flash('danger', 'Debe escribir una observación.');
            Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
        }

        Pqrs::agregarSeguimiento((int)$pqrs['id'], (int)Auth::id(), (int)$pqrs['estado_pqrs_id'], (int)$pqrs['estado_pqrs_id'], $observacion, $esPublico);
        Audit::log('SEGUIMIENTO_PQRS', 'seguimiento_pqrs', (int)$pqrs['id'], 'Observación registrada');
        flash('success', 'La observación fue agregada al historial de seguimiento.');
        Response::redirect('funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
    }
}
