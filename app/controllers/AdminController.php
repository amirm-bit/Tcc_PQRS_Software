<?php
// app/controllers/AdminController.php - Administración general del sistema

class AdminController {
    public function dashboard(): void {
        $stats = Dashboard::admin();
        $recientes = Pqrs::todos(['limite' => 8]);
        $tramitesRecientes = Tramite::todas(['limite' => 5]);
        $porTipo = Pqrs::contarPorTipo();
        $porEstado = Pqrs::contarPorEstado();
        $porMes = Pqrs::porMes((int)date('Y'));
        $actividad = Dashboard::activosFuncionarios();
        $tramitesPorEstado = Tramite::contarPorEstado();

        View::render('admin/dashboard', [
            'stats'              => $stats,
            'recientes'          => $recientes,
            'tramitesRecientes'  => $tramitesRecientes,
            'porTipo'            => $porTipo,
            'porEstado'          => $porEstado,
            'porMes'             => $porMes,
            'actividad'          => $actividad,
            'tramitesPorEstado'  => $tramitesPorEstado,
        ], 'panel', [
            'title'       => 'Dashboard general',
            'pageTitle'   => 'Dashboard general',
            'pageSubtitle'=> 'Indicadores institucionales en tiempo real',
            'activeMenu'  => 'dashboard',
        ]);
    }

    public function usuarios(): void {
        $filtros = [
            'rol' => $_GET['rol'] ?? '',
            'q'   => trim($_GET['q'] ?? ''),
        ];
        View::render('admin/usuarios', [
            'usuarios' => Usuario::all($filtros),
            'filtros'  => $filtros,
        ], 'panel', [
            'title'       => 'Gestión de usuarios',
            'pageTitle'   => 'Gestión de usuarios',
            'pageSubtitle'=> 'Administradores, funcionarios y ciudadanos',
            'activeMenu'  => 'usuarios',
        ]);
    }

    public function crearUsuario(): void {
        $db = Database::getConnection();
        View::render('admin/usuario_crear', [
            'dependencias' => $db->query("SELECT * FROM dependencias WHERE activo = 1 ORDER BY nombre")->fetchAll(),
        ], 'panel', [
            'title'       => 'Crear usuario',
            'pageTitle'   => 'Crear nuevo usuario',
            'pageSubtitle'=> 'Registro administrativo de cuentas',
            'activeMenu'  => 'usuarios',
        ]);
    }

    public function guardarUsuario(): void {
        $rol = $_POST['rol'] ?? '';
        if (!in_array($rol, ['admin', 'funcionario', 'ciudadano'], true)) {
            flash('danger', 'Debe seleccionar un rol válido.');
            Response::redirect('admin/usuarios/crear');
        }

        $validator = new Validator($_POST);
        $validator->required('email', 'correo electrónico')->email('email', 'correo electrónico')
                  ->required('password', 'contraseña')->min('password', 8, 'contraseña')
                  ->required('password_confirm', 'confirmación de contraseña')
                  ->same('password_confirm', 'password', 'confirmación de contraseña', 'la contraseña');

        if (in_array($rol, ['funcionario', 'ciudadano'], true)) {
            $validator->required('nombres', 'nombres')->required('apellidos', 'apellidos');
        }
        if ($rol === 'funcionario') {
            $validator->required('dependencia_id', 'dependencia')->required('cargo', 'cargo');
        }
        if ($rol === 'ciudadano') {
            $validator->required('tipo_documento', 'tipo de documento')
                      ->in('tipo_documento', ['CC','TI','CE','PASAPORTE','NIT'], 'tipo de documento')
                      ->required('numero_documento', 'número de documento')
                      ->required('telefono', 'teléfono')->required('direccion', 'dirección')->required('barrio', 'barrio');
        }

        if (!$validator->fails()) {
            if (Usuario::emailExiste(trim($_POST['email']))) {
                $validator->addError('email', 'Ya existe un usuario con este correo electrónico.');
            }
            if ($rol === 'ciudadano' && Usuario::documentoExiste(trim($_POST['numero_documento']))) {
                $validator->addError('numero_documento', 'Ya existe un ciudadano con este documento.');
            }
        }

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('admin/usuarios/crear');
        }

        try {
            if ($rol === 'admin') {
                $id = Usuario::crearAdmin($_POST);
            } elseif ($rol === 'funcionario') {
                $id = Usuario::crearFuncionario($_POST);
            } else {
                $id = Usuario::crearCiudadano($_POST);
            }
            Audit::log('CREAR_USUARIO', 'usuarios', $id, 'Usuario con rol ' . $rol . ' creado');
            flash('success', 'Usuario creado correctamente con rol ' . role_label($rol) . '.');
            Response::redirect('admin/usuarios');
        } catch (Exception $e) {
            error_log('Error al crear usuario: ' . $e->getMessage());
            flash('danger', 'No fue posible crear el usuario.');
            Response::redirect('admin/usuarios/crear');
        }
    }

    public function editarUsuario(string $id): void {
        $usuario = Usuario::find((int)$id);
        if (!$usuario) {
            flash('warning', 'El usuario solicitado no existe.');
            Response::redirect('admin/usuarios');
        }

        $perfil = [];
        if ($usuario['rol'] === 'ciudadano') {
            $perfil = Usuario::ciudadanoPorUsuario((int)$id) ?: [];
        } elseif ($usuario['rol'] === 'funcionario') {
            $perfil = Usuario::funcionarioPorUsuario((int)$id) ?: [];
        }

        $db = Database::getConnection();
        View::render('admin/usuario_editar', [
            'usuario'      => $usuario,
            'perfil'       => $perfil,
            'dependencias' => $db->query("SELECT * FROM dependencias WHERE activo = 1 ORDER BY nombre")->fetchAll(),
        ], 'panel', [
            'title'       => 'Editar usuario',
            'pageTitle'   => 'Editar usuario',
            'pageSubtitle'=> $usuario['email'],
            'activeMenu'  => 'usuarios',
        ]);
    }

    public function actualizarUsuario(string $id): void {
        $usuario = Usuario::find((int)$id);
        if (!$usuario) {
            flash('warning', 'El usuario solicitado no existe.');
            Response::redirect('admin/usuarios');
        }

        $validator = new Validator($_POST);
        $validator->required('email', 'correo electrónico')->email('email', 'correo electrónico');
        if (!empty($_POST['password'])) {
            $validator->min('password', 8, 'contraseña');
            $validator->required('password_confirm', 'confirmación de contraseña')
                      ->same('password_confirm', 'password', 'confirmación de contraseña', 'la contraseña');
        }

        $rol = $usuario['rol'];
        if (in_array($rol, ['funcionario', 'ciudadano'], true)) {
            $validator->required('nombres', 'nombres')->required('apellidos', 'apellidos');
        }
        if ($rol === 'funcionario') {
            $validator->required('dependencia_id', 'dependencia')->required('cargo', 'cargo');
        }
        if ($rol === 'ciudadano') {
            $validator->required('tipo_documento', 'tipo de documento')
                      ->required('numero_documento', 'número de documento')
                      ->required('telefono', 'teléfono')->required('direccion', 'dirección')->required('barrio', 'barrio');
        }

        if (!$validator->fails() && Usuario::emailExiste(trim($_POST['email']), (int)$id)) {
            $validator->addError('email', 'Otro usuario ya utiliza este correo electrónico.');
        }

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('admin/usuarios/editar/' . (int)$id);
        }

        try {
            if ($rol === 'ciudadano') {
                Usuario::actualizarCiudadano((int)$id, $_POST);
            } elseif ($rol === 'funcionario') {
                Usuario::actualizarFuncionario((int)$id, $_POST);
            } else {
                $db = Database::getConnection();
                $sql = "UPDATE usuarios SET email = :email";
                $params = ['email' => strtolower(trim($_POST['email'])), 'id' => (int)$id];
                if (!empty($_POST['password'])) {
                    $sql .= ", password = :password";
                    $params['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
                }
                $sql .= " WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
            }
            Audit::log('ACTUALIZAR_USUARIO', 'usuarios', (int)$id, 'Datos de usuario actualizados');
            flash('success', 'Usuario actualizado correctamente.');
        } catch (Exception $e) {
            error_log('Error al actualizar usuario: ' . $e->getMessage());
            flash('danger', 'No fue posible actualizar el usuario.');
        }
        Response::redirect('admin/usuarios');
    }

    public function cambiarEstadoUsuario(string $id): void {
        $usuario = Usuario::find((int)$id);
        if (!$usuario) {
            flash('warning', 'El usuario solicitado no existe.');
            Response::redirect('admin/usuarios');
        }
        if ((int)$id === (int)Auth::id()) {
            flash('danger', 'No puede desactivar su propia cuenta.');
            Response::redirect('admin/usuarios');
        }

        $nuevoEstado = (int)$usuario['activo'] === 1 ? 0 : 1;
        Usuario::setActivo((int)$id, $nuevoEstado === 1);
        Audit::log('CAMBIAR_ESTADO_USUARIO', 'usuarios', (int)$id, $nuevoEstado ? 'Activado' : 'Desactivado');
        flash('success', 'El usuario fue ' . ($nuevoEstado ? 'activado' : 'desactivado') . ' correctamente.');
        Response::redirect('admin/usuarios');
    }

    public function ciudadanos(): void {
        View::render('admin/ciudadanos', [
            'ciudadanos' => Usuario::ciudadanos(),
        ], 'panel', [
            'title'       => 'Gestión de ciudadanos',
            'pageTitle'   => 'Ciudadanos registrados',
            'pageSubtitle'=> 'Base de datos de la comunidad',
            'activeMenu'  => 'ciudadanos',
        ]);
    }

    public function funcionarios(): void {
        $funcionarios = Usuario::all(['rol' => 'funcionario']);
        View::render('admin/funcionarios', [
            'funcionarios' => $funcionarios,
        ], 'panel', [
            'title'       => 'Gestión de funcionarios',
            'pageTitle'   => 'Funcionarios de la Alcaldía',
            'pageSubtitle'=> 'Equipo de atención institucional',
            'activeMenu'  => 'funcionarios',
        ]);
    }

    public function dependencias(): void {
        $db = Database::getConnection();
        $dependencias = $db->query("
            SELECT d.*, (SELECT COUNT(*) FROM funcionarios f WHERE f.dependencia_id = d.id) AS total_funcionarios
            FROM dependencias d ORDER BY d.nombre
        ")->fetchAll();

        View::render('admin/dependencias', [
            'dependencias' => $dependencias,
        ], 'panel', [
            'title'       => 'Dependencias',
            'pageTitle'   => 'Dependencias municipales',
            'pageSubtitle'=> 'Estructura administrativa',
            'activeMenu'  => 'dependencias',
        ]);
    }

    public function guardarDependencia(): void {
        $validator = new Validator($_POST);
        $validator->required('codigo', 'código')->max('codigo', 20, 'código')
                  ->required('nombre', 'nombre')->max('nombre', 150, 'nombre')
                  ->required('email', 'correo')->email('email', 'correo');

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('admin/dependencias');
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO dependencias (codigo, nombre, email, telefono, activo, created_at)
                VALUES (:codigo, :nombre, :email, :telefono, 1, NOW())
            ");
            $stmt->execute([
                'codigo'   => strtoupper(trim($_POST['codigo'])),
                'nombre'   => trim($_POST['nombre']),
                'email'    => trim($_POST['email']),
                'telefono' => trim($_POST['telefono'] ?? ''),
            ]);
            Audit::log('CREAR_DEPENDENCIA', 'dependencias', (int)$db->lastInsertId(), 'Dependencia creada');
            flash('success', 'Dependencia creada correctamente.');
        } catch (Exception $e) {
            error_log('Error al crear dependencia: ' . $e->getMessage());
            flash('danger', 'No fue posible crear la dependencia. Verifique que el código no esté duplicado.');
        }
        Response::redirect('admin/dependencias');
    }

    public function cambiarEstadoDependencia(string $id): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT activo FROM dependencias WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        $activo = $stmt->fetchColumn();
        if ($activo === false) {
            flash('warning', 'La dependencia no existe.');
            Response::redirect('admin/dependencias');
        }

        $stmt = $db->prepare("UPDATE dependencias SET activo = :activo WHERE id = :id");
        $stmt->execute(['activo' => (int)$activo === 1 ? 0 : 1, 'id' => (int)$id]);
        Audit::log('CAMBIAR_ESTADO_DEPENDENCIA', 'dependencias', (int)$id, 'Estado modificado');
        flash('success', 'Estado de la dependencia actualizado.');
        Response::redirect('admin/dependencias');
    }

    public function auditoria(): void {
        $db = Database::getConnection();
        $filtros = [
            'accion' => trim($_GET['accion'] ?? ''),
            'desde'  => $_GET['desde'] ?? '',
            'hasta'  => $_GET['hasta'] ?? '',
        ];
        $sql = "
            SELECT a.*, u.email, r.nombre AS rol
            FROM auditoria a
            LEFT JOIN usuarios u ON u.id = a.usuario_id
            LEFT JOIN roles r ON r.id = u.rol_id
            WHERE 1=1
        ";
        $params = [];
        if ($filtros['accion'] !== '') {
            $sql .= " AND a.accion LIKE :accion";
            $params['accion'] = '%' . $filtros['accion'] . '%';
        }
        if ($filtros['desde'] !== '') { $sql .= " AND DATE(a.created_at) >= :desde"; $params['desde'] = $filtros['desde']; }
        if ($filtros['hasta'] !== '') { $sql .= " AND DATE(a.created_at) <= :hasta"; $params['hasta'] = $filtros['hasta']; }
        $sql .= " ORDER BY a.created_at DESC LIMIT 300";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        View::render('admin/auditoria', [
            'registros' => $stmt->fetchAll(),
            'filtros'   => $filtros,
        ], 'panel', [
            'title'       => 'Auditoría',
            'pageTitle'   => 'Historial de actividad del sistema',
            'pageSubtitle'=> 'Trazabilidad de acciones importantes',
            'activeMenu'  => 'auditoria',
        ]);
    }

    public function configuracion(): void {
        View::render('admin/configuracion', [
            'app'     => config(),
            'usuario' => Auth::user(),
        ], 'panel', [
            'title'       => 'Configuración',
            'pageTitle'   => 'Configuración del sistema',
            'pageSubtitle'=> 'Parámetros generales y seguridad',
            'activeMenu'  => 'configuracion',
        ]);
    }

    public function cambiarPasswordAdmin(): void {
        $validator = new Validator($_POST);
        $validator->required('password_actual', 'contraseña actual')
                  ->required('password', 'nueva contraseña')->min('password', 8, 'nueva contraseña')
                  ->required('password_confirm', 'confirmación')
                  ->same('password_confirm', 'password', 'confirmación', 'la nueva contraseña');

        if ($validator->fails()) {
            flash('danger', $validator->firstError());
            Response::redirect('admin/configuracion');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => Auth::id()]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($_POST['password_actual'], $hash)) {
            flash('danger', 'La contraseña actual no es correcta.');
            Response::redirect('admin/configuracion');
        }

        Usuario::cambiarPassword((int)Auth::id(), $_POST['password']);
        Audit::log('CAMBIAR_PASSWORD', 'usuarios', Auth::id(), 'Cambio de contraseña del administrador');
        flash('success', 'Su contraseña fue actualizada correctamente.');
        Response::redirect('admin/configuracion');
    }
}
