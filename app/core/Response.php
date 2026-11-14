<?php
// app/core/Response.php - Utilidades de respuesta HTTP

class Response {
    public static function redirect(string $path): void {
        header('Location: ' . url($path));
        exit;
    }

    public static function back(string $fallback = '/'): void {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if ($referer) {
            header('Location: ' . $referer);
            exit;
        }
        self::redirect($fallback);
    }

    public static function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(int $status, string $message): void {
        http_response_code($status);
        $data = ['status' => $status, 'message' => $message];
        if ($status === 404) {
            View::render('errors/404', ['message' => $message], 'public', ['title' => 'No encontrado']);
            return;
        }
        View::render('errors/error', $data, 'public', ['title' => 'Error']);
    }
}
