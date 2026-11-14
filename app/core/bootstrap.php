<?php
// app/core/bootstrap.php - Arranque de la aplicación

if (session_status() === PHP_SESSION_NONE) {
    $appConfig = require __DIR__ . '/../../config/app.php';
    session_name($appConfig['session_name'] ?? 'lorica_sess_id');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.gc_maxlifetime', (string)($appConfig['session_lifetime'] ?? 7200));
    session_start();
}

date_default_timezone_set((require __DIR__ . '/../../config/app.php')['timezone'] ?? 'America/Bogota');

mb_internal_encoding('UTF-8');

$appAll = require __DIR__ . '/../../config/app.php';
if (($appAll['env'] ?? 'local') === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Autoloader simple por directorios de la app
spl_autoload_register(function (string $class): void {
    $baseDir = dirname(__DIR__);
    $directorios = ['core', 'models', 'controllers', 'helpers', 'middleware', 'services'];
    foreach ($directorios as $dir) {
        $file = $baseDir . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/../helpers/functions.php';

// Tiempo de inactividad de sesión
if (!empty($_SESSION['usuario_id']) && !empty($_SESSION['_last_activity'])) {
    $limite = (int)(require __DIR__ . '/../../config/app.php')['session_lifetime'];
    if (time() - $_SESSION['_last_activity'] > $limite) {
        Auth::logout();
        flash('warning', 'Su sesión ha expirado por inactividad. Inicie sesión nuevamente.');
        header('Location: ' . url('login'));
        exit;
    }
}
if (!empty($_SESSION['usuario_id'])) {
    $_SESSION['_last_activity'] = time();
}

// Validación global de CSRF para peticiones POST
Csrf::check();
