<?php
// app/middleware/Middleware.php - Verificación de autenticación y permisos por rol

class Middleware {
    public static function run(string $spec): array {
        $spec = trim($spec);
        if ($spec === '') {
            return [true];
        }

        [$name, $arg] = array_pad(explode(':', $spec, 2), 2, null);

        switch ($name) {
            case 'auth':
                if (!Auth::check()) {
                    flash('warning', 'Debe iniciar sesión para acceder a esta sección.');
                    Response::redirect('login');
                    return [false];
                }
                return [true];

            case 'guest':
                if (Auth::check()) {
                    Response::redirect(dashboard_for_role(Auth::role()));
                    return [false];
                }
                return [true];

            case 'role':
                if (!Auth::check()) {
                    flash('warning', 'Debe iniciar sesión para acceder a esta sección.');
                    Response::redirect('login');
                    return [false];
                }
                $allowed = array_map('trim', explode(',', (string)$arg));
                if (!in_array(Auth::role(), $allowed, true)) {
                    http_response_code(403);
                    View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
                    return [false];
                }
                return [true];
        }

        return [true];
    }
}

function dashboard_for_role(?string $role): string {
    return match ($role) {
        'admin'       => 'admin/dashboard',
        'funcionario' => 'funcionario/dashboard',
        'ciudadano'   => 'ciudadano/dashboard',
        default       => 'login',
    };
}
