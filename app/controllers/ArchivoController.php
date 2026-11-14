<?php
// app/controllers/ArchivoController.php - Descarga segura de archivos privados

class ArchivoController {
    public function descargar(string $id): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM archivos_adjuntos WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => (int)$id]);
        $archivo = $stmt->fetch();

        if (!$archivo) {
            http_response_code(404);
            View::render('errors/404', [], 'public', ['title' => 'Archivo no encontrado']);
            return;
        }

        if (!$this->puedeAcceder($archivo)) {
            http_response_code(403);
            View::render('errors/403', [], 'public', ['title' => 'Acceso denegado']);
            return;
        }

        $ruta = __DIR__ . '/../../storage/uploads/' . $archivo['ruta'];
        if (!is_file($ruta)) {
            http_response_code(404);
            View::render('errors/404', [], 'public', ['title' => 'Archivo no disponible']);
            return;
        }

        $mime = $archivo['mime_type'] ?: 'application/octet-stream';
        $inline = in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true);
        $nombre = $archivo['nombre_original'];

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($ruta));
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . rawurlencode($nombre) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($ruta);
        exit;
    }

    private function puedeAcceder(array $archivo): bool {
        $rol = Auth::role();
        if ($rol === 'admin') {
            return true;
        }

        $usuarioId = Auth::id();

        if ($rol === 'ciudadano') {
            $ciudadano = Usuario::ciudadanoPorUsuario((int)$usuarioId);
            if (!$ciudadano) return false;
            $dueno = $this->obtenerCiudadanoDueno($archivo);
            return $dueno !== null && (int)$dueno === (int)$ciudadano['id'];
        }

        if ($rol === 'funcionario') {
            $funcionario = Usuario::funcionarioPorUsuario((int)$usuarioId);
            if (!$funcionario) return false;
            return $this->funcionarioTieneAcceso($archivo, (int)$funcionario['id']);
        }

        return false;
    }

    private function obtenerCiudadanoDueno(array $archivo): ?int {
        $db = Database::getConnection();
        if ($archivo['tipo_objeto'] === 'pqrs') {
            $stmt = $db->prepare("SELECT ciudadano_id FROM pqrs WHERE id = :id");
            $stmt->execute(['id' => $archivo['objeto_id']]);
            $v = $stmt->fetchColumn();
            return $v !== false ? (int)$v : null;
        }
        if ($archivo['tipo_objeto'] === 'tramite') {
            $stmt = $db->prepare("SELECT ciudadano_id FROM solicitudes_tramites WHERE id = :id");
            $stmt->execute(['id' => $archivo['objeto_id']]);
            $v = $stmt->fetchColumn();
            return $v !== false ? (int)$v : null;
        }
        if ($archivo['tipo_objeto'] === 'respuesta') {
            $stmt = $db->prepare("
                SELECT p.ciudadano_id
                FROM respuestas_pqrs r
                INNER JOIN pqrs p ON p.id = r.pqrs_id
                WHERE r.id = :id
            ");
            $stmt->execute(['id' => $archivo['objeto_id']]);
            $v = $stmt->fetchColumn();
            return $v !== false ? (int)$v : null;
        }
        return null;
    }

    private function funcionarioTieneAcceso(array $archivo, int $funcionarioId): bool {
        $db = Database::getConnection();
        if ($archivo['tipo_objeto'] === 'pqrs') {
            $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE id = :id AND funcionario_id = :f");
            $stmt->execute(['id' => $archivo['objeto_id'], 'f' => $funcionarioId]);
            return (int)$stmt->fetchColumn() > 0;
        }
        if ($archivo['tipo_objeto'] === 'tramite') {
            $stmt = $db->prepare("SELECT COUNT(*) FROM solicitudes_tramites WHERE id = :id AND funcionario_id = :f");
            $stmt->execute(['id' => $archivo['objeto_id'], 'f' => $funcionarioId]);
            return (int)$stmt->fetchColumn() > 0;
        }
        if ($archivo['tipo_objeto'] === 'respuesta') {
            $stmt = $db->prepare("
                SELECT COUNT(*)
                FROM respuestas_pqrs r
                INNER JOIN pqrs p ON p.id = r.pqrs_id
                WHERE r.id = :id AND p.funcionario_id = :f
            ");
            $stmt->execute(['id' => $archivo['objeto_id'], 'f' => $funcionarioId]);
            return (int)$stmt->fetchColumn() > 0;
        }
        return false;
    }
}
