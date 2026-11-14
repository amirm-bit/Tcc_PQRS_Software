<?php
// app/controllers/CiudadanoController.php - Panel del ciudadano

class CiudadanoController {
    public function dashboard(): void {
        $usuarioId = Auth::id();
        $ciudadano = Usuario::ciudadanoPorUsuario((int)$usuarioId);
        if (!$ciudadano) {
            flash('danger', 'No se encontró el perfil de ciudadano asociado a su cuenta.');
            Response::redirect('logout');
        }

        $stats = Dashboard::ciudadano((int)$ciudadano['id']);
        $pqrs = Pqrs::porCiudadano((int)$ciudadano['id']);
        $tramites = Tramite::porCiudadano((int)$ciudadano['id']);
        $notificaciones = NotificationService::paraUsuario((int)$usuarioId, 5);

        View::render('ciudadano/dashboard', [
            'ciudadano'      => $ciudadano,
            'stats'          => $stats,
            'pqrs'           => $pqrs,
            'tramites'       => $tramites,
            'notificaciones' => $notificaciones,
        ], 'panel', [
            'title'       => 'Dashboard',
            'pageTitle'   => 'Panel del ciudadano',
            'pageSubtitle'=> 'Resumen de sus solicitudes y trámites',
            'activeMenu'  => 'dashboard',
        ]);
    }

    public function perfil(): void {
        $ciudadano = Usuario::ciudadanoPorUsuario((int)Auth::id());
        View::render('ciudadano/perfil', [
            'ciudadano' => $ciudadano,
            'usuario'   => Auth::user(),
        ], 'panel', [
            'title'       => 'Mi perfil',
            'pageTitle'   => 'Mi perfil',
            'pageSubtitle'=> 'Datos personales y de contacto',
            'activeMenu'  => 'perfil',
        ]);
    }

    public function actualizarPerfil(): void {
        $usuarioId = (int)Auth::id();
        $ciudadano = Usuario::ciudadanoPorUsuario($usuarioId);
        if (!$ciudadano) {
            Response::redirect('logout');
        }

        $validator = new Validator($_POST);
        $validator
            ->required('nombres', 'nombres')->max('nombres', 100, 'nombres')
            ->required('apellidos', 'apellidos')->max('apellidos', 100, 'apellidos')
            ->required('tipo_documento', 'tipo de documento')
            ->in('tipo_documento', ['CC','TI','CE','PASAPORTE','NIT'], 'tipo de documento')
            ->required('numero_documento', 'número de documento')->max('numero_documento', 30, 'número de documento')
            ->required('email', 'correo electrónico')->email('email', 'correo electrónico')
            ->required('telefono', 'teléfono')
            ->required('direccion', 'dirección')
            ->required('barrio', 'barrio o vereda');

        if (!empty($_POST['password'])) {
            $validator->min('password', 8, 'contraseña');
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).+$/', (string)$_POST['password'])) {
                $validator->addError('password', 'La contraseña debe incluir al menos una letra y un número.');
            }
            $validator->required('password_confirm', 'confirmación de contraseña')
                      ->same('password_confirm', 'password', 'confirmación de contraseña', 'la contraseña');
        }

        if (!$validator->fails()) {
            if (Usuario::emailExiste(trim($_POST['email']), $usuarioId)) {
                $validator->addError('email', 'El correo electrónico ya está registrado por otra cuenta.');
            }
            $doc = trim($_POST['numero_documento']);
            if ($doc !== $ciudadano['numero_documento']) {
                if (Usuario::documentoExiste($doc)) {
                    $validator->addError('numero_documento', 'El número de documento ya está registrado.');
                }
            }
        }

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('ciudadano/perfil');
        }

        try {
            Usuario::actualizarCiudadano($usuarioId, [
                'email'            => $_POST['email'],
                'password'         => $_POST['password'] ?? '',
                'tipo_documento'   => $_POST['tipo_documento'],
                'numero_documento' => trim($_POST['numero_documento']),
                'nombres'          => trim($_POST['nombres']),
                'apellidos'        => trim($_POST['apellidos']),
                'telefono'         => trim($_POST['telefono']),
                'direccion'        => trim($_POST['direccion']),
                'barrio'           => trim($_POST['barrio']),
            ]);
            Audit::log('ACTUALIZAR_PERFIL', 'ciudadanos', (int)$ciudadano['id'], 'Actualización de datos personales');
            Auth::refresh();
            flash('success', 'Sus datos personales fueron actualizados correctamente.');
        } catch (Exception $e) {
            error_log('Error al actualizar perfil: ' . $e->getMessage());
            flash('danger', 'No fue posible actualizar el perfil. Intente nuevamente.');
        }

        Response::redirect('ciudadano/perfil');
    }

    public function notificaciones(): void {
        $notificaciones = NotificationService::paraUsuario((int)Auth::id(), 50);
        View::render('ciudadano/notificaciones', [
            'notificaciones' => $notificaciones,
        ], 'panel', [
            'title'       => 'Notificaciones',
            'pageTitle'   => 'Notificaciones',
            'pageSubtitle'=> 'Avisos sobre sus solicitudes',
            'activeMenu'  => 'notificaciones',
        ]);
    }

    public function marcarLeida(): void {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            NotificationService::marcarLeida($id, (int)Auth::id());
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT enlace FROM notificaciones WHERE id = :id AND usuario_id = :u");
            $stmt->execute(['id' => $id, 'u' => Auth::id()]);
            $enlace = $stmt->fetchColumn();
            if ($enlace) {
                Response::redirect($enlace);
            }
        }
        Response::redirect('ciudadano/notificaciones');
    }

    public function marcarTodas(): void {
        NotificationService::marcarTodasLeidas((int)Auth::id());
        flash('success', 'Todas sus notificaciones fueron marcadas como leídas.');
        Response::redirect('ciudadano/notificaciones');
    }
}
