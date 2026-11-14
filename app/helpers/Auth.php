<?php
// app/helpers/Auth.php - Autenticación centralizada y control de roles

class Auth {
    private static ?array $user = null;
    private static bool $loaded = false;

    public static function attempt(string $email, string $password): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            WHERE u.email = :email
            LIMIT 1
        ");
        $stmt->execute(['email' => strtolower(trim($email))]);
        $user = $stmt->fetch();

        if (!$user || !$user['activo']) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        self::login((int)$user['id']);

        $upd = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
        $upd->execute(['id' => $user['id']]);

        return true;
    }

    public static function login(int $userId): void {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $userId;
        $_SESSION['_last_activity'] = time();
        unset($_SESSION['_csrf_token']);
        self::$user = null;
        self::$loaded = false;
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        self::$user = null;
        self::$loaded = false;
    }

    public static function check(): bool {
        return self::user() !== null;
    }

    public static function user(): ?array {
        if (self::$loaded) {
            return self::$user;
        }
        self::$loaded = true;

        $userId = $_SESSION['usuario_id'] ?? null;
        if (!$userId) {
            return self::$user = null;
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                SELECT u.id, u.email, u.rol_id, u.activo, u.ultimo_acceso, u.created_at,
                       r.nombre AS rol
                FROM usuarios u
                INNER JOIN roles r ON r.id = u.rol_id
                WHERE u.id = :id AND u.activo = 1
                LIMIT 1
            ");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            return self::$user = null;
        }

        if (!$user) {
            return self::$user = null;
        }

        if ($user['rol'] === 'ciudadano') {
            $stmt = $db->prepare("SELECT * FROM ciudadanos WHERE usuario_id = :id LIMIT 1");
            $stmt->execute(['id' => $userId]);
            $user['perfil'] = $stmt->fetch() ?: [];
        } elseif ($user['rol'] === 'funcionario') {
            $stmt = $db->prepare("
                SELECT f.*, d.nombre AS dependencia_nombre
                FROM funcionarios f
                LEFT JOIN dependencias d ON d.id = f.dependencia_id
                WHERE f.usuario_id = :id LIMIT 1
            ");
            $stmt->execute(['id' => $userId]);
            $user['perfil'] = $stmt->fetch() ?: [];
        } else {
            $user['perfil'] = ['nombres' => 'Administrador', 'apellidos' => 'del Sistema'];
        }

        return self::$user = $user;
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): ?string {
        $user = self::user();
        return $user['rol'] ?? null;
    }

    public static function is(string $role): bool {
        return self::role() === $role;
    }

    public static function name(): string {
        $user = self::user();
        if (!$user) return 'Invitado';
        $perfil = $user['perfil'] ?? [];
        $nombre = trim(($perfil['nombres'] ?? '') . ' ' . ($perfil['apellidos'] ?? ''));
        return $nombre !== '' ? $nombre : $user['email'];
    }

    public static function perfil(): array {
        return self::user()['perfil'] ?? [];
    }

    public static function refresh(): void {
        self::$user = null;
        self::$loaded = false;
        self::user();
    }
}
