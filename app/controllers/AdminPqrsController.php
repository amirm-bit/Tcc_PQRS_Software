<?php
// app/controllers/AdminPqrsController.php - Administración de todas las PQRS

class AdminPqrsController {
    public function index(): void {
        $filtros = [
            'q'          => trim($_GET['q'] ?? ''),
            'estado'     => ($_GET['estado'] ?? '') !== '' ? (int)$_GET['estado'] : null,
            'tipo'       => ($_GET['tipo'] ?? '') !== '' ? (int)$_GET['tipo'] : null,
            'funcionario'=> ($_GET['funcionario'] ?? '') !== '' ? (int)$_GET['funcionario'] : null,
            'fecha_desde'=> $_GET['fecha_desde'] ?? '',
            'fecha_hasta'=> $_GET['fecha_hasta'] ?? '',
        ];

        View::render('admin/pqrs_index', [
            'pqrs'         => Pqrs::todos($filtros),
            'filtros'      => $filtros,
            'estados'      => Pqrs::estados(),
            'tipos'        => Pqrs::tipos(),
            'funcionarios' => Usuario::funcionarios(),
        ], 'panel', [
            'title'       => 'Gestión de PQRS',
            'pageTitle'   => 'Gestión de PQRS',
            'pageSubtitle'=> 'Todas las solicitudes del municipio',
            'activeMenu'  => 'pqrs',
        ]);
    }

    public function detalle(string $id): void {
        $pqrs = Pqrs::detalle((int)$id);
        if (!$pqrs) {
            flash('warning', 'La PQRS solicitada no existe.');
            Response::redirect('admin/pqrs');
        }

        View::render('admin/pqrs_detalle', [
            'pqrs'         => $pqrs,
            'seguimiento'  => Pqrs::seguimiento((int)$pqrs['id'], false),
            'respuestas'   => Pqrs::respuestas((int)$pqrs['id']),
            'archivos'     => UploadService::findByObjeto('pqrs', (int)$pqrs['id']),
            'estados'      => Pqrs::estados(),
            'funcionarios' => Usuario::funcionarios(),
            'dependencias' => Database::getConnection()->query("SELECT * FROM dependencias WHERE activo = 1 ORDER BY nombre")->fetchAll(),
        ], 'panel', [
            'title'       => 'Detalle PQRS',
            'pageTitle'   => 'Detalle de la PQRS',
            'pageSubtitle'=> $pqrs['radicado'],
            'activeMenu'  => 'pqrs',
        ]);
    }

    public function asignar(string $id): void {
        $pqrs = Pqrs::find((int)$id);
        if (!$pqrs) {
            flash('warning', 'La PQRS solicitada no existe.');
            Response::redirect('admin/pqrs');
        }

        $funcionarioId = ($_POST['funcionario_id'] ?? '') !== '' ? (int)$_POST['funcionario_id'] : null;
        $dependenciaId = ($_POST['dependencia_id'] ?? '') !== '' ? (int)$_POST['dependencia_id'] : null;

        if ($funcionarioId === null) {
            flash('danger', 'Debe seleccionar un funcionario para asignar la solicitud.');
            Response::redirect('admin/pqrs/detalle/' . (int)$pqrs['id']);
        }

        try {
            Pqrs::asignar((int)$pqrs['id'], $funcionarioId, $dependenciaId);

            $estadoAsignada = Pqrs::estadoPorCodigo('asignada');
            $estadoAnteriorId = (int)$pqrs['estado_pqrs_id'];
            if (in_array($estadoAnteriorId, [1, 2], true)) {
                Pqrs::cambiarEstado((int)$pqrs['id'], (int)$estadoAsignada['id']);
                Pqrs::agregarSeguimiento((int)$pqrs['id'], (int)Auth::id(), $estadoAnteriorId, (int)$estadoAsignada['id'], 'Solicitud asignada a funcionario responsable.', true);
            } else {
                Pqrs::agregarSeguimiento((int)$pqrs['id'], (int)Auth::id(), $estadoAnteriorId, $estadoAnteriorId, 'Solicitud reasignada a nuevo funcionario responsable.', true);
            }

            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT usuario_id FROM funcionarios WHERE id = :id");
            $stmt->execute(['id' => $funcionarioId]);
            $funcionarioUsuario = $stmt->fetchColumn();
            if ($funcionarioUsuario) {
                NotificationService::crear((int)$funcionarioUsuario, 'Nueva PQRS asignada', 'Se le asignó la solicitud ' . $pqrs['radicado'] . '.', 'funcionario/pqrs/gestionar/' . (int)$pqrs['id']);
            }

            $stmt = $db->prepare("SELECT c.usuario_id FROM pqrs p INNER JOIN ciudadanos c ON c.id = p.ciudadano_id WHERE p.id = :id");
            $stmt->execute(['id' => (int)$pqrs['id']]);
            $ciudadanoUsuario = $stmt->fetchColumn();
            if ($ciudadanoUsuario) {
                NotificationService::crear((int)$ciudadanoUsuario, 'Su PQRS fue asignada', 'La solicitud ' . $pqrs['radicado'] . ' fue asignada a un funcionario.', 'ciudadano/pqrs/detalle/' . (int)$pqrs['id']);
            }

            Audit::log('ASIGNAR_PQRS', 'pqrs', (int)$pqrs['id'], 'Asignada al funcionario ID ' . $funcionarioId);
            flash('success', 'La solicitud fue asignada correctamente.');
        } catch (Exception $e) {
            error_log('Error al asignar PQRS: ' . $e->getMessage());
            flash('danger', 'No fue posible asignar la solicitud.');
        }

        Response::redirect('admin/pqrs/detalle/' . (int)$pqrs['id']);
    }

    public function cambiarEstado(string $id): void {
        $pqrs = Pqrs::find((int)$id);
        if (!$pqrs) {
            flash('warning', 'La PQRS solicitada no existe.');
            Response::redirect('admin/pqrs');
        }

        $estadoId = (int)($_POST['estado_pqrs_id'] ?? 0);
        $observacion = trim($_POST['observacion'] ?? '');
        $estadosValidos = array_map(fn($e) => (int)$e['id'], Pqrs::estados());

        if (!in_array($estadoId, $estadosValidos, true)) {
            flash('danger', 'Estado no válido.');
            Response::redirect('admin/pqrs/detalle/' . (int)$pqrs['id']);
        }
        if ($observacion === '') {
            flash('danger', 'Debe registrar una observación.');
            Response::redirect('admin/pqrs/detalle/' . (int)$pqrs['id']);
        }

        $anterior = (int)$pqrs['estado_pqrs_id'];
        $fechaCierre = in_array($estadoId, [6, 7], true) ? date('Y-m-d H:i:s') : null;

        Pqrs::cambiarEstado((int)$pqrs['id'], $estadoId, $fechaCierre);
        Pqrs::agregarSeguimiento((int)$pqrs['id'], (int)Auth::id(), $anterior, $estadoId, $observacion, true);
        Audit::log('CAMBIAR_ESTADO_PQRS', 'pqrs', (int)$pqrs['id'], 'Estado actualizado por administrador a ID ' . $estadoId);

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT c.usuario_id FROM pqrs p INNER JOIN ciudadanos c ON c.id = p.ciudadano_id WHERE p.id = :id");
        $stmt->execute(['id' => (int)$pqrs['id']]);
        $ciudadanoUsuario = $stmt->fetchColumn();
        if ($ciudadanoUsuario) {
            NotificationService::crear((int)$ciudadanoUsuario, 'Actualización de su PQRS', 'La solicitud ' . $pqrs['radicado'] . ' cambió de estado.', 'ciudadano/pqrs/detalle/' . (int)$pqrs['id']);
        }

        flash('success', 'Estado actualizado y registrado en el historial.');
        Response::redirect('admin/pqrs/detalle/' . (int)$pqrs['id']);
    }

    public function agregarSeguimiento(string $id): void {
        $pqrs = Pqrs::find((int)$id);
        if (!$pqrs) {
            flash('warning', 'La PQRS solicitada no existe.');
            Response::redirect('admin/pqrs');
        }

        $observacion = trim($_POST['observacion'] ?? '');
        $esPublico = isset($_POST['es_publico']);
        if ($observacion === '') {
            flash('danger', 'Debe escribir una observación.');
            Response::redirect('admin/pqrs/detalle/' . (int)$pqrs['id']);
        }

        Pqrs::agregarSeguimiento((int)$pqrs['id'], (int)Auth::id(), (int)$pqrs['estado_pqrs_id'], (int)$pqrs['estado_pqrs_id'], $observacion, $esPublico);
        Audit::log('SEGUIMIENTO_PQRS', 'seguimiento_pqrs', (int)$pqrs['id'], 'Observación administrativa agregada');
        flash('success', 'Observación agregada al historial.');
        Response::redirect('admin/pqrs/detalle/' . (int)$pqrs['id']);
    }
}
