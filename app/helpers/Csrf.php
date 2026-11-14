<?php
// app/helpers/Csrf.php - Protección CSRF

class Csrf {
    public static function token(): string {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function field(): string {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function validate(?string $token): bool {
        if (empty($_SESSION['_csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    public static function check(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            if (!self::validate($token)) {
                http_response_code(419);
                View::render('errors/419', [], 'public', ['title' => 'Sesión expirada']);
                exit;
            }
        }
    }
}
