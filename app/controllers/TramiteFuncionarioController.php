<?php
// app/controllers/TramiteFuncionarioController.php - Gestión de solicitudes de trámites

class TramiteFuncionarioController {
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
        $todas = Tramite::porFuncionario((int)$funcionario['id']);

        View::render('funcionario/tramites_index', [
            'solicitudes' => $todas,
            'funcionario' => $funcionario,
        ], 'panel', [
            'title'       => 'Gestión de trámites',
            'pageTitle'   => 'Solicitudes de trámites asignadas',
            'pageSubtitle'=> 'Certificados y trámites bajo su responsabilidad',
            'activeMenu'  => 'tramites',
        ]);
    }

    public function gestionar(string $id): void {
        $funcionario = $this->funcionarioActual();
        $solicitud = Tramite::detalle((int)$id);
        if (!$solicitud || (int)$solicitud['funcionario_id'] !== (int)$funcionario['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
            return;
        }

        View::render('funcionario/tramite_gestionar', [
            'solicitud'   => $solicitud,
            'datos'       => json_decode($solicitud['datos_adicionales'] ?? '[]', true) ?: [],
            'seguimiento' => Tramite::seguimiento((int)$solicitud['id'], false),
            'archivos'    => UploadService::findByObjeto('tramite', (int)$solicitud['id']),
            'estados'     => Tramite::estados(),
        ], 'panel', [
            'title'       => 'Gestionar trámite',
            'pageTitle'   => 'Gestión del trámite',
            'pageSubtitle'=> $solicitud['radicado'],
            'activeMenu'  => 'tramites',
        ]);
    }

    private function verificarAsignacion(int $id): array {
        $funcionario = $this->funcionarioActual();
        $solicitud = Tramite::find($id);
        if (!$solicitud || (int)$solicitud['funcionario_id'] !== (int)$funcionario['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
            exit;
        }
        return [$funcionario, $solicitud];
    }

    public function cambiarEstado(string $id): void {
        [$funcionario, $solicitud] = $this->verificarAsignacion((int)$id);

        $estadoId = (int)($_POST['estado_tramite_id'] ?? 0);
        $motivo = trim($_POST['observacion'] ?? '');
        $estadosValidos = array_map(fn($e) => (int)$e['id'], Tramite::estados());

        if (!in_array($estadoId, $estadosValidos, true)) {
            flash('danger', 'El estado seleccionado no es válido.');
            Response::redirect('funcionario/tramites/gestionar/' . (int)$solicitud['id']);
        }

        $estadoNuevo = null;
        foreach (Tramite::estados() as $e) {
            if ((int)$e['id'] === $estadoId) { $estadoNuevo = $e; break; }
        }
        if ($estadoNuevo === null) {
            flash('danger', 'Estado no encontrado.');
            Response::redirect('funcionario/tramites/gestionar/' . (int)$solicitud['id']);
        }

        $requiereMotivo = in_array($estadoNuevo['codigo'] ?? '', ['rechazada', 'requiere_info'], true);
        if ($requiereMotivo && $motivo === '') {
            flash('danger', 'Debe registrar la razón u observación para este estado.');
            Response::redirect('funcionario/tramites/gestionar/' . (int)$solicitud['id']);
        }
        if ($motivo === '') {
            $motivo = 'Estado actualizado a ' . ($estadoNuevo['nombre'] ?? '') . '.';
        }

        $anterior = (int)$solicitud['estado_tramite_id'];
        $fechaCierre = in_array($estadoNuevo['codigo'] ?? '', ['aprobada', 'rechazada', 'finalizada'], true) ? date('Y-m-d H:i:s') : null;

        Tramite::cambiarEstado((int)$solicitud['id'], $estadoId, $requiereMotivo ? $motivo : null, $fechaCierre);
        Tramite::agregarSeguimiento((int)$solicitud['id'], (int)Auth::id(), $anterior, $estadoId, $motivo, true);
        Audit::log('CAMBIAR_ESTADO_TRAMITE', 'solicitudes_tramites', (int)$solicitud['id'], 'Estado actualizado a ' . ($estadoNuevo['nombre'] ?? ''));

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT c.usuario_id FROM solicitudes_tramites s INNER JOIN ciudadanos c ON c.id = s.ciudadano_id WHERE s.id = :id");
        $stmt->execute(['id' => (int)$solicitud['id']]);
        $ciudadanoUsuario = $stmt->fetchColumn();
        if ($ciudadanoUsuario) {
            NotificationService::crear((int)$ciudadanoUsuario, 'Actualización de su trámite', 'La solicitud ' . $solicitud['radicado'] . ' cambió de estado.', 'ciudadano/tramites/detalle/' . (int)$solicitud['id']);
        }

        flash('success', 'El estado del trámite fue actualizado correctamente.');
        Response::redirect('funcionario/tramites/gestionar/' . (int)$solicitud['id']);
    }

    public function agregarSeguimiento(string $id): void {
        [$funcionario, $solicitud] = $this->verificarAsignacion((int)$id);

        $observacion = trim($_POST['observacion'] ?? '');
        $esPublico = isset($_POST['es_publico']) ? true : false;

        if ($observacion === '') {
            flash('danger', 'Debe escribir una observación.');
            Response::redirect('funcionario/tramites/gestionar/' . (int)$solicitud['id']);
        }

        Tramite::agregarSeguimiento((int)$solicitud['id'], (int)Auth::id(), (int)$solicitud['estado_tramite_id'], (int)$solicitud['estado_tramite_id'], $observacion, $esPublico);
        Audit::log('SEGUIMIENTO_TRAMITE', 'seguimiento_tramites', (int)$solicitud['id'], 'Observación registrada');
        flash('success', 'La observación fue agregada al historial.');
        Response::redirect('funcionario/tramites/gestionar/' . (int)$solicitud['id']);
    }
}
