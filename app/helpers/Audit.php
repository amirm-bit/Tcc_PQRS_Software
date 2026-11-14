<?php
// app/helpers/Audit.php - Registro de auditoría de acciones

class Audit {
    public static function log(string $accion, string $tabla, ?int $registroId = null, ?string $detalles = null): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO auditoria (usuario_id, accion, tabla_afectada, registro_id, detalles, ip, created_at)
                VALUES (:usuario_id, :accion, :tabla, :registro_id, :detalles, :ip, NOW())
            ");
            $stmt->execute([
                'usuario_id'  => Auth::id(),
                'accion'      => $accion,
                'tabla'       => $tabla,
                'registro_id' => $registroId,
                'detalles'    => $detalles,
                'ip'          => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Exception $e) {
            error_log('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}
