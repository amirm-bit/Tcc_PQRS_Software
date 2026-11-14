<?php
// app/services/NotificationService.php - Notificaciones internas para usuarios

class NotificationService {
    public static function crear(int $usuarioId, string $titulo, string $mensaje, ?string $enlace = null): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO notificaciones (usuario_id, titulo, mensaje, enlace, leida, created_at)
                VALUES (:usuario_id, :titulo, :mensaje, :enlace, 0, NOW())
            ");
            $stmt->execute([
                'usuario_id' => $usuarioId,
                'titulo'     => $titulo,
                'mensaje'    => $mensaje,
                'enlace'     => $enlace,
            ]);
        } catch (Exception $e) {
            error_log('Error al crear notificación: ' . $e->getMessage());
        }
    }

    public static function paraUsuario(int $usuarioId, int $limit = 20): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM notificaciones WHERE usuario_id = :id ORDER BY created_at DESC LIMIT {$limit}");
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public static function noLeidas(int $usuarioId): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = :id AND leida = 0");
        $stmt->execute(['id' => $usuarioId]);
        return (int)$stmt->fetchColumn();
    }

    public static function marcarLeida(int $id, int $usuarioId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE notificaciones SET leida = 1 WHERE id = :id AND usuario_id = :usuario");
        $stmt->execute(['id' => $id, 'usuario' => $usuarioId]);
    }

    public static function marcarTodasLeidas(int $usuarioId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = :id");
        $stmt->execute(['id' => $usuarioId]);
    }
}
