<?php
// app/controllers/AuthController.php - Registro, inicio y cierre de sesión

class AuthController {
    public function showLogin(): void {
        View::render('auth/login', [], 'auth', ['title' => 'Iniciar sesión']);
    }

    public function login(): void {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $validator = new Validator($_POST);
        $validator->required('email', 'correo electrónico')->email('email', 'correo electrónico')
                  ->required('password', 'contraseña');
        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('login');
        }

        if (!Auth::attempt($email, $password)) {
            Validator::keepOld($_POST);
            flash('danger', 'Credenciales incorrectas. Verifique su correo y contraseña, o confirme que su cuenta esté activa.');
            Audit::log('LOGIN_FALLIDO', 'usuarios', null, 'Intento de acceso fallido para ' . $email);
            Response::redirect('login');
        }

        Audit::log('LOGIN', 'usuarios', Auth::id(), 'Inicio de sesión exitoso');
        flash('success', 'Bienvenido(a), ' . Auth::name() . '.');
        Response::redirect(dashboard_for_role(Auth::role()));
    }

    public function showRegister(): void {
        View::render('auth/register', [], 'auth', ['title' => 'Registro de ciudadano']);
    }

    public function register(): void {
        $validator = new Validator($_POST);
        $validator
            ->required('nombres', 'nombres')->max('nombres', 100, 'nombres')
            ->required('apellidos', 'apellidos')->max('apellidos', 100, 'apellidos')
            ->required('tipo_documento', 'tipo de documento')
            ->in('tipo_documento', ['CC','TI','CE','PASAPORTE','NIT'], 'tipo de documento')
            ->required('numero_documento', 'número de documento')->max('numero_documento', 30, 'número de documento')
            ->required('email', 'correo electrónico')->email('email', 'correo electrónico')->max('email', 150, 'correo')
            ->required('password', 'contraseña')->min('password', 8, 'contraseña')
            ->required('password_confirm', 'confirmación de contraseña')
            ->same('password_confirm', 'password', 'confirmación de contraseña', 'la contraseña')
            ->required('telefono', 'teléfono')->max('telefono', 30, 'teléfono')
            ->required('direccion', 'dirección')->max('direccion', 200, 'dirección')
            ->required('barrio', 'barrio o vereda')->max('barrio', 100, 'barrio');

        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).+$/', (string)($_POST['password'] ?? ''))) {
            $validator->addError('password', 'La contraseña debe incluir al menos una letra y un número.');
        }

        if (!$validator->fails()) {
            $email = strtolower(trim($_POST['email']));
            if (Usuario::emailExiste($email)) {
                $validator->addError('email', 'Ya existe una cuenta registrada con este correo electrónico.');
            }
            if (Usuario::documentoExiste(trim($_POST['numero_documento']))) {
                $validator->addError('numero_documento', 'Ya existe un ciudadano registrado con este número de documento.');
            }
        }

        if ($validator->fails()) {
            Validator::keepOld($_POST);
            flash('danger', $validator->firstError());
            Response::redirect('registro');
        }

        try {
            $usuarioId = Usuario::crearCiudadano([
                'email'            => $_POST['email'],
                'password'         => $_POST['password'],
                'tipo_documento'   => $_POST['tipo_documento'],
                'numero_documento' => trim($_POST['numero_documento']),
                'nombres'          => trim($_POST['nombres']),
                'apellidos'        => trim($_POST['apellidos']),
                'telefono'         => trim($_POST['telefono']),
                'direccion'        => trim($_POST['direccion']),
                'barrio'           => trim($_POST['barrio']),
            ]);

            Auth::login($usuarioId);
            Audit::log('REGISTRO_CIUDADANO', 'usuarios', $usuarioId, 'Nuevo ciudadano registrado: ' . $email);
            NotificationService::crear($usuarioId, 'Bienvenido al sistema', 'Su cuenta fue creada correctamente. Ya puede radicar PQRS y solicitar trámites.', 'ciudadano/dashboard');
            flash('success', 'Su registro fue exitoso. Bienvenido(a) al sistema de trámites y PQRS.');
            Response::redirect('ciudadano/dashboard');
        } catch (Exception $e) {
            error_log('Error al registrar ciudadano: ' . $e->getMessage());
            flash('danger', 'No fue posible completar el registro. Intente nuevamente.');
            Response::redirect('registro');
        }
    }

    public function showForgot(): void {
        View::render('auth/forgot', [], 'auth', ['title' => 'Recuperar acceso']);
    }

    public function forgot(): void {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('danger', 'Ingrese un correo electrónico válido.');
            Response::redirect('recuperar');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $usuarioId = $stmt->fetchColumn();

        if (!$usuarioId) {
            flash('warning', 'No existe una cuenta registrada con el correo ' . $email . '.');
            Response::redirect('recuperar');
        }

        $temporal = 'Lorica' . random_int(1000, 9999) . '!';
        Usuario::cambiarPassword((int)$usuarioId, $temporal);
        Audit::log('RECUPERACION_ACCESO', 'usuarios', (int)$usuarioId, 'Restablecimiento de contraseña temporal para ' . $email);

        flash('success', 'Contraseña temporal generada correctamente. En un entorno productivo se enviaría por correo electrónico.');
        flash('info', 'Contraseña temporal de demostración: ' . $temporal . ' — Cámbiela al iniciar sesión.');
        Response::redirect('login');
    }

    public function logout(): void {
        if (Auth::check()) {
            Audit::log('LOGOUT', 'usuarios', Auth::id(), 'Cierre de sesión');
        }
        Auth::logout();
        flash('success', 'Ha cerrado sesión correctamente.');
        Response::redirect('login');
    }
}
