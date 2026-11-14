<?php
// public/index.php - Punto de entrada único (Front Controller)

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/core/bootstrap.php';

try {
    $router = new Router(config('base_url'));
    require BASE_PATH . '/routes/web.php';

    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    error_log('[Lorica] Error no controlado: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());

    if (!headers_sent()) {
        http_response_code(500);
    }

    $mensaje = (config('env') === 'local') ? $e->getMessage() : null;

    if (class_exists('View')) {
        try {
            View::render('errors/error', ['message' => $mensaje]);
        } catch (Throwable $inner) {
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family:sans-serif;padding:3rem;text-align:center;">';
            echo '<h1>Error interno del servidor</h1><p>No fue posible procesar su solicitud. Intente nuevamente en unos minutos.</p>';
            echo '<p><a href="' . htmlspecialchars(url(''), ENT_QUOTES, 'UTF-8') . '">Volver al inicio</a></p>';
            echo '</body></html>';
        }
    } else {
        echo 'Error interno del servidor.';
    }
}
