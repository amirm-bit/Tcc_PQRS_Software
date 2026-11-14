<?php
// app/controllers/PqrsCiudadanoController.php - Radicación y consulta de PQRS del ciudadano

class PqrsCiudadanoController {
    private function ciudadanoActual(): array {
        $ciudadano = Usuario::ciudadanoPorUsuario((int)Auth::id());
        if (!$ciudadano) {
            flash('danger', 'No se encontró el perfil de ciudadano.');
            Response::redirect('logout');
        }
        return $ciudadano;
    }

    public function index(): void {
        $ciudadano = $this->ciudadanoActual();
        $filtros = [
            'q'      => trim($_GET['q'] ?? ''),
            'estado' => ($_GET['estado'] ?? '') !== '' ? (int)$_GET['estado'] : null,
            'tipo'   => ($_GET['tipo'] ?? '') !== '' ? (int)$_GET['tipo'] : null,
        ];
        $pqrs = Pqrs::porCiudadano((int)$ciudadano['id'], $filtros);

        View::render('ciudadano/pqrs_index', [
            'pqrs'    => $pqrs,
            'filtros' => $filtros,
            'estados' => Pqrs::estados(),
            'tipos'   => Pqrs::tipos(),
        ], 'panel', [
            'title'       => 'Mis PQRS',
            'pageTitle'   => 'Mis PQRS',
            'pageSubtitle'=> 'Historial de peticiones, quejas, reclamos y sugerencias',
            'activeMenu'  => 'mis-pqrs',
        ]);
    }

    public function crear(): void {
        $ciudadano = $this->ciudadanoActual();
        $db = Database::getConnection();
        $dependencias = $db->query("SELECT * FROM dependencias WHERE activo = 1 ORDER BY nombre")->fetchAll();

        View::render('ciudadano/pqrs_crear', [
            'ciudadano'    => $ciudadano,
            'tipos'        => Pqrs::tipos(),
            'dependencias' => $dependencias,
            'usuario'      => Auth::user(),
        ], 'panel', [
            'title'       => 'Nueva PQRS',
            'pageTitle'   => 'Radicar nueva PQRS',
            'pageSubtitle'=> 'Petición, queja, reclamo o sugerencia',
            'activeMenu'  => 'nueva-pqrs',
        ]);
    }

    public function guardar(): void {
        $ciudadano = $this->ciudadanoActual();

        $validator = new Validator($_POST);
        $validator
            ->required('tipo_pqrs_id', 'tipo de solicitud')
            ->required('asunto', 'asunto')->min('asunto', 5, 'asunto')->max('asunto', 255, 'asunto')
            ->required('descripcion', 'descripción')->min('descripcion', 20, 'descripción')
            ->required('medio_contacto', 'medio de contacto')
            ->in('medio_contacto', ['correo', 'telefono', 'direccion'], 'medio de contacto')
            ->required('email_contacto', 'correo electrónico')->email('email_contacto', 'correo electrónico')
            ->required('telefono_contacto', 'teléfono')->max('telefono_contacto', 30, 'teléfono');

        $tiposValidos = array_map(fn($t) => (string)$t['id'], Pqrs::tipos());
        if (!in_array((string)($_POST['tipo_pqrs_id'] ?? ''), $tiposValidos, true)) {
            $validator->addError('tipo_pqrs_id', 'El tipo de solicitud seleccionado no es válido.');
        }

        if (!empty($_FILES['adjuntos']['name'][0] ?? null)) {
            $files = $_FILES['adjuntos'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
                $single = ['name'=>$files['name'][$i],'type'=>$files['type'][$i],'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]];
                $err = UploadService::validate($single);
                if ($err !== null) {
                    $validator->addError('adjuntos', $err);
                    break;
                }
            }
        }

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('ciudadano/pqrs/nueva');
        }

        $tipo = null;
        foreach (Pqrs::tipos() as $t) {
            if ((int)$t['id'] === (int)$_POST['tipo_pqrs_id']) { $tipo = $t; break; }
        }
        $estado = Pqrs::estadoPorCodigo('radicada');

        try {
            $radicado = RadicadoService::generar('pqrs', 'PQRS');
            $dependenciaId = ($_POST['dependencia_id'] ?? '') !== '' ? (int)$_POST['dependencia_id'] : null;
            $pqrsId = Pqrs::crear([
                'radicado'       => $radicado,
                'ciudadano_id'   => (int)$ciudadano['id'],
                'tipo_pqrs_id'   => (int)$_POST['tipo_pqrs_id'],
                'estado_pqrs_id' => (int)$estado['id'],
                'dependencia_id' => $dependenciaId,
                'asunto'         => trim($_POST['asunto']),
                'descripcion'    => trim($_POST['descripcion']),
                'medio_contacto' => $_POST['medio_contacto'],
                'email_contacto' => trim($_POST['email_contacto']),
                'telefono_contacto' => trim($_POST['telefono_contacto']),
                'fecha_vencimiento' => date('Y-m-d', strtotime('+' . (int)($tipo['dias_termino'] ?? 15) . ' days')),
            ]);

            Pqrs::agregarSeguimiento($pqrsId, (int)Auth::id(), null, (int)$estado['id'], 'Radicación inicial exitosa. Su solicitud fue registrada en el sistema.', true);
            UploadService::processMultiple('adjuntos', 'pqrs', $pqrsId, 'pqrs');
            Audit::log('RADICAR_PQRS', 'pqrs', $pqrsId, 'Radicación de ' . $radicado);

            // Notificar a administradores sobre nueva solicitud
            $db = Database::getConnection();
            $admins = $db->query("SELECT u.id FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id WHERE r.nombre = 'admin' AND u.activo = 1")->fetchAll();
            foreach ($admins as $admin) {
                NotificationService::crear((int)$admin['id'], 'Nueva PQRS radicada', 'Se radicó la solicitud ' . $radicado . ' pendiente de asignación.', 'admin/pqrs/detalle/' . $pqrsId);
            }

            flash('success', '¡PQRS radicada correctamente! Radicado: ' . $radicado);
            Response::redirect('ciudadano/pqrs/detalle/' . $pqrsId);
        } catch (Exception $e) {
            error_log('Error al radicar PQRS: ' . $e->getMessage());
            flash('danger', 'No fue posible radicar la solicitud: ' . $e->getMessage());
            Response::redirect('ciudadano/pqrs/nueva');
        }
    }

    public function detalle(string $id): void {
        $ciudadano = $this->ciudadanoActual();
        $pqrs = Pqrs::detalle((int)$id);
        if (!$pqrs || (int)$pqrs['ciudadano_id'] !== (int)$ciudadano['id']) {
            http_response_code(403);
            View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
            return;
        }

        View::render('ciudadano/pqrs_detalle', [
            'pqrs'        => $pqrs,
            'seguimiento' => Pqrs::seguimiento((int)$pqrs['id'], true),
            'respuestas'  => Pqrs::respuestas((int)$pqrs['id']),
            'archivos'    => UploadService::findByObjeto('pqrs', (int)$pqrs['id']),
        ], 'panel', [
            'title'       => 'Detalle PQRS',
            'pageTitle'   => 'Detalle de la solicitud',
            'pageSubtitle'=> $pqrs['radicado'],
            'activeMenu'  => 'mis-pqrs',
        ]);
    }
}
