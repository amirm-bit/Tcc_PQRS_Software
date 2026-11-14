<?php
// app/core/View.php - Motor de vistas con layouts

class View {
    public static function render(string $template, array $data = [], string $layout = 'public', array $layoutData = []): void {
        $viewFile = __DIR__ . '/../views/' . $template . '.php';
        if (!is_file($viewFile)) {
            throw new Exception('Vista no encontrada: ' . $template);
        }

        extract($data, EXTR_SKIP);
        $__viewFile = $viewFile;

        ob_start();
        require $__viewFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            echo $content;
            if (class_exists('Validator')) {
                Validator::clearOld();
            }
            return;
        }

        extract($layoutData, EXTR_SKIP);
        require $layoutFile;

        if (class_exists('Validator')) {
            Validator::clearOld();
        }
    }
}
