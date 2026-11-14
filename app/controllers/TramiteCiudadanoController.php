<?php
// app/controllers/TramiteCiudadanoController.php - Solicitud de certificados y trámites

class TramiteCiudadanoController {
    private function ciudadanoActual(): array {
        $ciudadano = Usuario::ciudadanoPorUsuario((int)Auth::id());
        if (!$ciudadano) {
            flash('danger', 'No se encontró el perfil de ciudadano.');
            Response::redirect('logout');
        }
        return $ciudadano;
    }

    public function index(): void {
        $this->ciudadanoActual();
        View::render('ciudadano/tramites_index', [
            'tipos' => Tramite::tiposActivos(),
        ], 'panel', [
            'title'       => 'Trámites disponibles',
            'pageTitle'   => 'Trámites y certificados disponibles',
            'pageSubtitle'=> 'Seleccione un trámite para iniciar su solicitud',
            'activeMenu'  => 'tramites',
        ]);
    }

    public function misSolicitudes(): void {
        $ciudadano = $this->ciudadanoActual();
        View::render('ciudadano/tramites_mis', [
            'solicitudes' => Tramite::porCiudadano((int)$ciudadano['id']),
        ], 'panel', [
            'title'       => 'Mis trámites',
            'pageTitle'   => 'Mis trámites y certificados',
            'pageSubtitle'=> 'Seguimiento de sus solicitudes',
            'activeMenu'  => 'mis-tramites',
        ]);
    }

    public function solicitar(string $id): void {
        $ciudadano = $this->ciudadanoActual();
        $tipo = Tramite::tipo((int)$id);
        if (!$tipo || !$tipo['activo']) {
            flash('warning', 'El trámite solicitado no está disponible.');
            Response::redirect('ciudadano/tramites');
        }

        $campos = json_decode($tipo['detalle_campos'] ?? '[]', true) ?: [];

        View::render('ciudadano/tramite_solicitar', [
            'tipo'     => $tipo,
            'campos'   => $campos,
            'ciudadano'=> $ciudadano,
            'usuario'  => Auth::user(),
        ], 'panel', [
            'title'       => 'Solicitar trámite',
            'pageTitle'   => $tipo['nombre'],
            'pageSubtitle'=> 'Formulario de solicitud de demostración',
            'activeMenu'  => 'tramites',
        ]);
    }

    public function guardar(string $id): void {
        $ciudadano = $this->ciudadanoActual();
        $tipo = Tramite::tipo((int)$id);
        if (!$tipo || !$tipo['activo']) {
            flash('warning', 'El trámite solicitado no está disponible.');
            Response::redirect('ciudadano/tramites');
        }

        $campos = json_decode($tipo['detalle_campos'] ?? '[]', true) ?: [];
        $datos = [];
        $errores = [];
        foreach ($campos as $campo) {
            $nombre = $campo['name'];
            $valor = trim((string)($_POST['campos'][$nombre] ?? ''));
            if (!empty($campo['required']) && $valor === '') {
                $errores[] = 'El campo "' . $campo['label'] . '" es obligatorio.';
            }
            $datos[$nombre] = $valor;
        }

        if (!empty($_FILES['adjuntos']['name'][0] ?? null)) {
            $files = $_FILES['adjuntos'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                $single = ['name'=>$files['name'][$i],'type'=>$files['type'][$i],'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]];
                $err = UploadService::validate($single);
                if ($err !== null) { $errores[] = $err; break; }
            }
        }

        if ($errores) {
            Validator::keepOld($_POST);
            flash('danger', $errores[0]);
            Response::redirect('ciudadano/tramites/solicitar/' . (int)$tipo['id']);
        }

        $estado = Tramite::estadoPorCodigo('recibida');
        try {
            $radicado = RadicadoService::generar('solicitudes_tramites', 'TRAM');
            $solicitudId = Tramite::crear([
                'radicado'          => $radicado,
                'ciudadano_id'      => (int)$ciudadano['id'],
                'tipo_tramite_id'   => (int)$tipo['id'],
                'estado_tramite_id' => (int)$estado['id'],
                'datos_adicionales' => $datos,
            ]);

            Tramite::agregarSeguimiento($solicitudId, (int)Auth::id(), null, (int)$estado['id'], 'Solicitud radicada en ventanilla digital.', true);
            UploadService::processMultiple('adjuntos', 'tramites', $solicitudId, 'tramite');
            Audit::log('RADICAR_TRAMITE', 'solicitudes_tramites', $solicitudId, 'Radicación de ' . $radicado);

            $db = Database::getConnection();
            $admins = $db->query("SELECT u.id FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id WHERE r.nombre = 'admin' AND u.activo = 1")->fetchAll();
            foreach ($admins as $admin) {
                NotificationService::crear((int)$admin['id'], 'Nueva solicitud de trámite', 'Se radicó el trámite ' . $radicado . '.', 'admin/tramites/solicitudes/detalle/' . $solicitudId);
            }

            flash('success', '¡Solicitud radicada correctamente! Radicado: ' . $radicado);
            Response::redirect('ciudadano/tramites/detalle/' . $solicitudId);
        } catch (Exception $e) {
            error_log('Error al radicar trámite: ' . $e->getMessage());
            flash('danger', 'No fue posible radicar el trámite: ' . $e->getMessage());
            Response::redirect('ciudadano/tramites/solicitar/' . (int)$tipo['id']);
        }
    }

    public function detalle(string $id): void {
        $ciudadano = $this->ciudadanoActual();
        $solicitud = Tramite::detalle((int)$id);
        if (!$solicitud || (int)$solicitud['ciudadano_id'] !== (int)$ciudadano['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
            return;
        }

        View::render('ciudadano/tramite_detalle', [
            'solicitud'   => $solicitud,
            'datos'       => json_decode($solicitud['datos_adicionales'] ?? '[]', true) ?: [],
            'seguimiento' => Tramite::seguimiento((int)$solicitud['id'], true),
            'archivos'    => UploadService::findByObjeto('tramite', (int)$solicitud['id']),
        ], 'panel', [
            'title'       => 'Detalle del trámite',
            'pageTitle'   => 'Detalle del trámite',
            'pageSubtitle'=> $solicitud['radicado'],
            'activeMenu'  => 'mis-tramites',
        ]);
    }
}
