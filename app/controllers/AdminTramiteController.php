<?php
// app/controllers/AdminTramiteController.php - Gestión de tipos de trámites y solicitudes

class AdminTramiteController {
    public function tipos(): void {
        View::render('admin/tramites_tipos', [
            'tipos' => Tramite::tiposTodos(),
        ], 'panel', [
            'title'       => 'Tipos de trámites',
            'pageTitle'   => 'Tipos de trámites y certificados',
            'pageSubtitle'=> 'Catálogo de servicios de ventanilla',
            'activeMenu'  => 'tipos-tramites',
        ]);
    }

    public function crearTipo(): void {
        View::render('admin/tramite_tipo_form', [
            'tipo'         => null,
            'dependencias' => Database::getConnection()->query("SELECT * FROM dependencias WHERE activo = 1 ORDER BY nombre")->fetchAll(),
            'camposTexto'  => '',
        ], 'panel', [
            'title'       => 'Crear tipo de trámite',
            'pageTitle'   => 'Crear tipo de trámite',
            'pageSubtitle'=> 'Nuevo servicio de ventanilla',
            'activeMenu'  => 'tipos-tramites',
        ]);
    }

    public function guardarTipo(): void {
        $validator = new Validator($_POST);
        $validator->required('codigo', 'código')->max('codigo', 30, 'código')
                  ->required('nombre', 'nombre')->max('nombre', 150, 'nombre')
                  ->required('descripcion', 'descripción')
                  ->required('requisitos', 'requisitos')
                  ->required('instrucciones', 'instrucciones')
                  ->required('dias_termino', 'días de término')->numeric('dias_termino', 'días de término');

        $campos = $this->parseCampos($_POST['campos_definicion'] ?? '');
        if ($campos === null) {
            $validator->addError('campos_definicion', 'El formato de campos no es válido. Use: nombre|Etiqueta|tipo|1');
        }

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('admin/tramites/crear');
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO tipos_tramites
                    (codigo, nombre, descripcion, requisitos, instrucciones, detalle_campos, dias_termino, dependencia_id, activo, created_at)
                VALUES
                    (:codigo, :nombre, :descripcion, :requisitos, :instrucciones, :campos, :dias, :dependencia, :activo, NOW())
            ");
            $stmt->execute([
                'codigo'       => strtoupper(trim($_POST['codigo'])),
                'nombre'       => trim($_POST['nombre']),
                'descripcion'  => trim($_POST['descripcion']),
                'requisitos'   => trim($_POST['requisitos']),
                'instrucciones'=> trim($_POST['instrucciones']),
                'campos'       => json_encode($campos, JSON_UNESCAPED_UNICODE),
                'dias'         => (int)$_POST['dias_termino'],
                'dependencia'  => ($_POST['dependencia_id'] ?? '') !== '' ? (int)$_POST['dependencia_id'] : null,
                'activo'       => isset($_POST['activo']) ? 1 : 1,
            ]);
            Audit::log('CREAR_TIPO_TRAMITE', 'tipos_tramites', (int)$db->lastInsertId(), 'Tipo de trámite creado');
            flash('success', 'Tipo de trámite creado correctamente.');
            Response::redirect('admin/tramites');
        } catch (Exception $e) {
            error_log('Error al crear tipo de trámite: ' . $e->getMessage());
            flash('danger', 'No fue posible crear el tipo de trámite. Verifique que el código no esté duplicado.');
            Response::redirect('admin/tramites/crear');
        }
    }

    public function editarTipo(string $id): void {
        $tipo = Tramite::tipo((int)$id);
        if (!$tipo) {
            flash('warning', 'El tipo de trámite no existe.');
            Response::redirect('admin/tramites');
        }

        $campos = json_decode($tipo['detalle_campos'] ?? '[]', true) ?: [];
        $lineas = [];
        foreach ($campos as $c) {
            $lineas[] = ($c['name'] ?? '') . '|' . ($c['label'] ?? '') . '|' . ($c['type'] ?? 'text') . '|' . (!empty($c['required']) ? '1' : '0');
        }

        View::render('admin/tramite_tipo_form', [
            'tipo'         => $tipo,
            'dependencias' => Database::getConnection()->query("SELECT * FROM dependencias WHERE activo = 1 ORDER BY nombre")->fetchAll(),
            'camposTexto'  => implode("\n", $lineas),
        ], 'panel', [
            'title'       => 'Editar tipo de trámite',
            'pageTitle'   => 'Editar tipo de trámite',
            'pageSubtitle'=> $tipo['nombre'],
            'activeMenu'  => 'tipos-tramites',
        ]);
    }

    public function actualizarTipo(string $id): void {
        $tipo = Tramite::tipo((int)$id);
        if (!$tipo) {
            flash('warning', 'El tipo de trámite no existe.');
            Response::redirect('admin/tramites');
        }

        $validator = new Validator($_POST);
        $validator->required('codigo', 'código')->max('codigo', 30, 'código')
                  ->required('nombre', 'nombre')->max('nombre', 150, 'nombre')
                  ->required('descripcion', 'descripción')
                  ->required('requisitos', 'requisitos')
                  ->required('instrucciones', 'instrucciones')
                  ->required('dias_termino', 'días de término')->numeric('dias_termino', 'días de término');

        $campos = $this->parseCampos($_POST['campos_definicion'] ?? '');
        if ($campos === null) {
            $validator->addError('campos_definicion', 'El formato de campos no es válido.');
        }

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('admin/tramites/editar/' . (int)$id);
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE tipos_tramites SET
                    codigo = :codigo,
                    nombre = :nombre,
                    descripcion = :descripcion,
                    requisitos = :requisitos,
                    instrucciones = :instrucciones,
                    detalle_campos = :campos,
                    dias_termino = :dias,
                    dependencia_id = :dependencia,
                    activo = :activo
                WHERE id = :id
            ");
            $stmt->execute([
                'codigo'       => strtoupper(trim($_POST['codigo'])),
                'nombre'       => trim($_POST['nombre']),
                'descripcion'  => trim($_POST['descripcion']),
                'requisitos'   => trim($_POST['requisitos']),
                'instrucciones'=> trim($_POST['instrucciones']),
                'campos'       => json_encode($campos, JSON_UNESCAPED_UNICODE),
                'dias'         => (int)$_POST['dias_termino'],
                'dependencia'  => ($_POST['dependencia_id'] ?? '') !== '' ? (int)$_POST['dependencia_id'] : null,
                'activo'       => isset($_POST['activo']) ? 1 : 0,
                'id'           => (int)$id,
            ]);
            Audit::log('ACTUALIZAR_TIPO_TRAMITE', 'tipos_tramites', (int)$id, 'Tipo de trámite actualizado');
            flash('success', 'Tipo de trámite actualizado correctamente.');
            Response::redirect('admin/tramites');
        } catch (Exception $e) {
            error_log('Error al actualizar tipo de trámite: ' . $e->getMessage());
            flash('danger', 'No fue posible actualizar el tipo de trámite.');
            Response::redirect('admin/tramites/editar/' . (int)$id);
        }
    }

    public function cambiarEstadoTipo(string $id): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT activo FROM tipos_tramites WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        $activo = $stmt->fetchColumn();
        if ($activo === false) {
            flash('warning', 'El tipo de trámite no existe.');
            Response::redirect('admin/tramites');
        }
        $stmt = $db->prepare("UPDATE tipos_tramites SET activo = :activo WHERE id = :id");
        $stmt->execute(['activo' => (int)$activo === 1 ? 0 : 1, 'id' => (int)$id]);
        Audit::log('CAMBIAR_ESTADO_TIPO_TRAMITE', 'tipos_tramites', (int)$id, 'Estado modificado');
        flash('success', 'Disponibilidad del trámite actualizada.');
        Response::redirect('admin/tramites');
    }

    public function solicitudes(): void {
        $filtros = [
            'q'          => trim($_GET['q'] ?? ''),
            'estado'     => ($_GET['estado'] ?? '') !== '' ? (int)$_GET['estado'] : null,
            'tipo'       => ($_GET['tipo'] ?? '') !== '' ? (int)$_GET['tipo'] : null,
            'funcionario'=> ($_GET['funcionario'] ?? '') !== '' ? (int)$_GET['funcionario'] : null,
        ];

        View::render('admin/tramites_solicitudes', [
            'solicitudes'  => Tramite::todas($filtros),
            'filtros'      => $filtros,
            'estados'      => Tramite::estados(),
            'tipos'        => Tramite::tiposTodos(),
            'funcionarios' => Usuario::funcionarios(),
        ], 'panel', [
            'title'       => 'Solicitudes de trámites',
            'pageTitle'   => 'Solicitudes de trámites',
            'pageSubtitle'=> 'Gestión de certificados y trámites radicados',
            'activeMenu'  => 'solicitudes-tramites',
        ]);
    }

    public function detalleSolicitud(string $id): void {
        $solicitud = Tramite::detalle((int)$id);
        if (!$solicitud) {
            flash('warning', 'La solicitud no existe.');
            Response::redirect('admin/tramites/solicitudes');
        }

        View::render('admin/tramite_detalle', [
            'solicitud'    => $solicitud,
            'datos'        => json_decode($solicitud['datos_adicionales'] ?? '[]', true) ?: [],
            'seguimiento'  => Tramite::seguimiento((int)$solicitud['id'], false),
            'archivos'     => UploadService::findByObjeto('tramite', (int)$solicitud['id']),
            'estados'      => Tramite::estados(),
            'funcionarios' => Usuario::funcionarios(),
        ], 'panel', [
            'title'       => 'Detalle del trámite',
            'pageTitle'   => 'Detalle de la solicitud',
            'pageSubtitle'=> $solicitud['radicado'],
            'activeMenu'  => 'solicitudes-tramites',
        ]);
    }

    public function cambiarEstadoSolicitud(string $id): void {
        $solicitud = Tramite::find((int)$id);
        if (!$solicitud) {
            flash('warning', 'La solicitud no existe.');
            Response::redirect('admin/tramites/solicitudes');
        }

        $estadoId = (int)($_POST['estado_tramite_id'] ?? 0);
        $motivo = trim($_POST['observacion'] ?? '');

        $estadoNuevo = null;
        foreach (Tramite::estados() as $e) {
            if ((int)$e['id'] === $estadoId) { $estadoNuevo = $e; break; }
        }
        if (!$estadoNuevo) {
            flash('danger', 'Estado no válido.');
            Response::redirect('admin/tramites/solicitudes/detalle/' . (int)$solicitud['id']);
        }

        $requiereMotivo = in_array($estadoNuevo['codigo'], ['rechazada', 'requiere_info'], true);
        if ($requiereMotivo && $motivo === '') {
            flash('danger', 'Debe registrar la razón u observación.');
            Response::redirect('admin/tramites/solicitudes/detalle/' . (int)$solicitud['id']);
        }
        if ($motivo === '') {
            $motivo = 'Estado actualizado a ' . $estadoNuevo['nombre'] . '.';
        }

        $fechaCierre = in_array($estadoNuevo['codigo'], ['aprobada', 'rechazada', 'finalizada'], true) ? date('Y-m-d H:i:s') : null;
        Tramite::cambiarEstado((int)$solicitud['id'], $estadoId, $requiereMotivo ? $motivo : null, $fechaCierre);
        Tramite::agregarSeguimiento((int)$solicitud['id'], (int)Auth::id(), (int)$solicitud['estado_tramite_id'], $estadoId, $motivo, true);
        Audit::log('CAMBIAR_ESTADO_TRAMITE', 'solicitudes_tramites', (int)$solicitud['id'], 'Estado a ' . $estadoNuevo['nombre']);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT c.usuario_id FROM solicitudes_tramites s INNER JOIN ciudadanos c ON c.id = s.ciudadano_id WHERE s.id = :id");
        $stmt->execute(['id' => (int)$solicitud['id']]);
        $ciudadanoUsuario = $stmt->fetchColumn();
        if ($ciudadanoUsuario) {
            NotificationService::crear((int)$ciudadanoUsuario, 'Actualización de su trámite', 'La solicitud ' . $solicitud['radicado'] . ' cambió de estado.', 'ciudadano/tramites/detalle/' . (int)$solicitud['id']);
        }

        flash('success', 'Estado del trámite actualizado correctamente.');
        Response::redirect('admin/tramites/solicitudes/detalle/' . (int)$solicitud['id']);
    }

    public function asignarSolicitud(string $id): void {
        $solicitud = Tramite::find((int)$id);
        if (!$solicitud) {
            flash('warning', 'La solicitud no existe.');
            Response::redirect('admin/tramites/solicitudes');
        }

        $funcionarioId = ($_POST['funcionario_id'] ?? '') !== '' ? (int)$_POST['funcionario_id'] : null;
        if ($funcionarioId === null) {
            flash('danger', 'Debe seleccionar un funcionario.');
            Response::redirect('admin/tramites/solicitudes/detalle/' . (int)$solicitud['id']);
        }

        Tramite::asignar((int)$solicitud['id'], $funcionarioId);
        Tramite::agregarSeguimiento((int)$solicitud['id'], (int)Auth::id(), (int)$solicitud['estado_tramite_id'], (int)$solicitud['estado_tramite_id'], 'Solicitud asignada a funcionario responsable.', true);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT usuario_id FROM funcionarios WHERE id = :id");
        $stmt->execute(['id' => $funcionarioId]);
        $funcionarioUsuario = $stmt->fetchColumn();
        if ($funcionarioUsuario) {
            NotificationService::crear((int)$funcionarioUsuario, 'Nuevo trámite asignado', 'Se le asignó la solicitud ' . $solicitud['radicado'] . '.', 'funcionario/tramites/gestionar/' . (int)$solicitud['id']);
        }

        Audit::log('ASIGNAR_TRAMITE', 'solicitudes_tramites', (int)$solicitud['id'], 'Asignada al funcionario ID ' . $funcionarioId);
        flash('success', 'Solicitud asignada correctamente.');
        Response::redirect('admin/tramites/solicitudes/detalle/' . (int)$solicitud['id']);
    }

    private function parseCampos(string $texto): ?array {
        $texto = trim($texto);
        if ($texto === '') {
            return [];
        }
        $campos = [];
        $lineas = preg_split('/\r\n|\r|\n/', $texto);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if ($linea === '') continue;
            $partes = array_map('trim', explode('|', $linea));
            if (count($partes) < 2) {
                return null;
            }
            $name = $partes[0];
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
                return null;
            }
            $tipo = strtolower($partes[2] ?? 'text');
            if (!in_array($tipo, ['text', 'textarea', 'number', 'date', 'email'], true)) {
                $tipo = 'text';
            }
            $campos[] = [
                'name'     => $name,
                'label'    => $partes[1] !== '' ? $partes[1] : $name,
                'type'     => $tipo,
                'required' => ($partes[3] ?? '1') === '1',
            ];
        }
        return $campos;
    }
}
