<?php
// app/helpers/functions.php - Funciones globales de apoyo

function config(string $key = null, mixed $default = null): mixed {
    static $app = null;
    if ($app === null) {
        $app = require __DIR__ . '/../../config/app.php';
    }
    if ($key === null) {
        return $app;
    }
    return $app[$key] ?? $default;
}

function base_url(string $path = ''): string {
    $base = rtrim(config('base_url', ''), '/');
    $path = ltrim($path, '/');
    return $base . ($path !== '' ? '/' . $path : '') . ($path === '' ? '/' : '');
}

function url(string $path = ''): string {
    return base_url($path);
}

function asset(string $path): string {
    return base_url('assets/' . ltrim($path, '/'));
}

function e(?string $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function is_active(string $prefix): bool {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = rtrim(config('base_url', ''), '/');
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }
    $uri = '/' . trim($uri, '/');
    $prefix = '/' . trim($prefix, '/');
    return $uri === $prefix || str_starts_with($uri, $prefix . '/');
}

function format_fecha(?string $datetime, bool $withTime = false): string {
    if (!$datetime) {
        return '—';
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return e($datetime);
    }
    return date($withTime ? 'd/m/Y H:i' : 'd/m/Y', $ts);
}

function format_fecha_larga(?string $datetime): string {
    if (!$datetime) {
        return '—';
    }
    $ts = strtotime($datetime);
    $dias = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    $meses = ['','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    return $dias[(int)date('w', $ts)] . ', ' . date('j', $ts) . ' de ' . $meses[(int)date('n', $ts)] . ' de ' . date('Y', $ts);
}

function time_ago(?string $datetime): string {
    if (!$datetime) return '—';
    $ts = strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return 'hace un momento';
    if ($diff < 3600) return 'hace ' . floor($diff / 60) . ' min';
    if ($diff < 86400) return 'hace ' . floor($diff / 3600) . ' h';
    if ($diff < 2592000) return 'hace ' . floor($diff / 86400) . ' días';
    return format_fecha($datetime);
}

function flash(string $type, string $message): void {
    Flash::set($type, $message);
}

function old(string $key, mixed $default = ''): string {
    return e($_SESSION['_old'][$key] ?? $default);
}

function role_label(string $role): string {
    return match ($role) {
        'admin'       => 'Administrador',
        'funcionario' => 'Funcionario',
        'ciudadano'   => 'Ciudadano',
        default       => ucfirst($role),
    };
}

function badge_estado(string $color): string {
    $permitidos = ['primary','secondary','success','danger','warning','info','dark','light'];
    return in_array($color, $permitidos, true) ? $color : 'secondary';
}

function truncate(string $text, int $length = 90): string {
    if (mb_strlen($text) <= $length) return $text;
    return rtrim(mb_substr($text, 0, $length)) . '…';
}

function estado_icon(string $codigo): string {
    return match ($codigo) {
        'radicada'             => 'bi-inbox',
        'pendiente_asignacion' => 'bi-hourglass-split',
        'asignada'             => 'bi-person-check',
        'en_revision'          => 'bi-search',
        'en_proceso'           => 'bi-arrow-repeat',
        'respondida'           => 'bi-chat-left-text',
        'cerrada'              => 'bi-archive',
        'recibida'             => 'bi-inbox',
        'pendiente_revision'   => 'bi-hourglass-split',
        'en_revision'          => 'bi-search',
        'requiere_info'        => 'bi-exclamation-circle',
        'aprobada'             => 'bi-check-circle',
        'rechazada'            => 'bi-x-circle',
        'finalizada'           => 'bi-archive',
        default                => 'bi-circle',
    };
}
