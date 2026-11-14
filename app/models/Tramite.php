<?php
// app/models/Tramite.php - Acceso a datos de trámites y certificados

class Tramite {
    public static function tiposActivos(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT t.*, d.nombre AS dependencia_nombre
            FROM tipos_tramites t
            LEFT JOIN dependencias d ON d.id = t.dependencia_id
            WHERE t.activo = 1
            ORDER BY t.id
        ")->fetchAll();
    }

    public static function tiposTodos(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT t.*, d.nombre AS dependencia_nombre
            FROM tipos_tramites t
            LEFT JOIN dependencias d ON d.id = t.dependencia_id
            ORDER BY t.id
        ")->fetchAll();
    }

    public static function tipo(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT t.*, d.nombre AS dependencia_nombre
            FROM tipos_tramites t
            LEFT JOIN dependencias d ON d.id = t.dependencia_id
            WHERE t.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function estados(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM estados_tramites ORDER BY id")->fetchAll();
    }

    public static function estadoPorCodigo(string $codigo): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM estados_tramites WHERE codigo = :c LIMIT 1");
        $stmt->execute(['c' => $codigo]);
        return $stmt->fetch() ?: null;
    }

    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM solicitudes_tramites WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function detalle(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.*,
                   t.nombre AS tipo_nombre,
                   t.descripcion AS tipo_descripcion,
                   t.requisitos,
                   t.instrucciones,
                   t.detalle_campos,
                   e.nombre AS estado_nombre,
                   e.codigo AS estado_codigo,
                   e.color_badge,
                   c.nombres AS ciudadano_nombres,
                   c.apellidos AS ciudadano_apellidos,
                   c.numero_documento,
                   c.telefono AS ciudadano_telefono,
                   c.direccion AS ciudadano_direccion,
                   c.barrio AS ciudadano_barrio,
                   u.email AS ciudadano_email,
                   f.nombres AS funcionario_nombres,
                   f.apellidos AS funcionario_apellidos,
                   d.nombre AS dependencia_nombre
            FROM solicitudes_tramites s
            INNER JOIN tipos_tramites t ON t.id = s.tipo_tramite_id
            INNER JOIN estados_tramites e ON e.id = s.estado_tramite_id
            INNER JOIN ciudadanos c ON c.id = s.ciudadano_id
            INNER JOIN usuarios u ON u.id = c.usuario_id
            LEFT JOIN funcionarios f ON f.id = s.funcionario_id
            LEFT JOIN dependencias d ON d.id = t.dependencia_id
            WHERE s.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function porRadicado(string $radicado): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM solicitudes_tramites WHERE radicado = :r LIMIT 1");
        $stmt->execute(['r' => $radicado]);
        $id = $stmt->fetchColumn();
        return $id ? self::detalle((int)$id) : null;
    }

    public static function porCiudadano(int $ciudadanoId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.*, t.nombre AS tipo_nombre, e.nombre AS estado_nombre, e.color_badge
            FROM solicitudes_tramites s
            INNER JOIN tipos_tramites t ON t.id = s.tipo_tramite_id
            INNER JOIN estados_tramites e ON e.id = s.estado_tramite_id
            WHERE s.ciudadano_id = :ciudadano
            ORDER BY s.fecha_radicacion DESC
        ");
        $stmt->execute(['ciudadano' => $ciudadanoId]);
        return $stmt->fetchAll();
    }

    public static function porFuncionario(int $funcionarioId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.*, t.nombre AS tipo_nombre, e.nombre AS estado_nombre, e.color_badge,
                   c.nombres AS ciudadano_nombres, c.apellidos AS ciudadano_apellidos
            FROM solicitudes_tramites s
            INNER JOIN tipos_tramites t ON t.id = s.tipo_tramite_id
            INNER JOIN estados_tramites e ON e.id = s.estado_tramite_id
            INNER JOIN ciudadanos c ON c.id = s.ciudadano_id
            WHERE s.funcionario_id = :funcionario
            ORDER BY s.fecha_radicacion DESC
        ");
        $stmt->execute(['funcionario' => $funcionarioId]);
        return $stmt->fetchAll();
    }

    public static function todas(array $filtros = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT s.*, t.nombre AS tipo_nombre, e.nombre AS estado_nombre, e.codigo AS estado_codigo, e.color_badge,
                   c.nombres AS ciudadano_nombres, c.apellidos AS ciudadano_apellidos, c.numero_documento,
                   f.nombres AS funcionario_nombres, f.apellidos AS funcionario_apellidos
            FROM solicitudes_tramites s
            INNER JOIN tipos_tramites t ON t.id = s.tipo_tramite_id
            INNER JOIN estados_tramites e ON e.id = s.estado_tramite_id
            INNER JOIN ciudadanos c ON c.id = s.ciudadano_id
            LEFT JOIN funcionarios f ON f.id = s.funcionario_id
            WHERE 1=1
        ";
        $params = [];
        if (!empty($filtros['q'])) {
            $sql .= " AND (s.radicado LIKE :q OR c.nombres LIKE :q OR c.apellidos LIKE :q)";
            $params['q'] = '%' . $filtros['q'] . '%';
        }
        if (!empty($filtros['estado'])) {
            $sql .= " AND s.estado_tramite_id = :estado";
            $params['estado'] = $filtros['estado'];
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND s.tipo_tramite_id = :tipo";
            $params['tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['funcionario'])) {
            $sql .= " AND s.funcionario_id = :funcionario";
            $params['funcionario'] = $filtros['funcionario'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(s.fecha_radicacion) >= :fecha_desde";
            $params['fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(s.fecha_radicacion) <= :fecha_hasta";
            $params['fecha_hasta'] = $filtros['fecha_hasta'];
        }
        $sql .= " ORDER BY s.fecha_radicacion DESC";
        if (!empty($filtros['limite'])) {
            $sql .= " LIMIT " . (int)$filtros['limite'];
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function crear(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO solicitudes_tramites
                (radicado, ciudadano_id, tipo_tramite_id, estado_tramite_id, funcionario_id,
                 datos_adicionales, fecha_radicacion, created_at)
            VALUES
                (:radicado, :ciudadano, :tipo, :estado, :funcionario,
                 :datos, NOW(), NOW())
        ");
        $stmt->execute([
            'radicado'    => $data['radicado'],
            'ciudadano'   => $data['ciudadano_id'],
            'tipo'        => $data['tipo_tramite_id'],
            'estado'      => $data['estado_tramite_id'],
            'funcionario' => $data['funcionario_id'] ?? null,
            'datos'       => json_encode($data['datos_adicionales'] ?? [], JSON_UNESCAPED_UNICODE),
        ]);
        return (int)$db->lastInsertId();
    }

    public static function cambiarEstado(int $id, int $estadoId, ?string $motivo = null, ?string $fechaCierre = null): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE solicitudes_tramites
            SET estado_tramite_id = :estado,
                motivo_rechazo = COALESCE(:motivo, motivo_rechazo),
                fecha_cierre = :cierre
            WHERE id = :id
        ");
        $stmt->execute(['estado' => $estadoId, 'motivo' => $motivo, 'cierre' => $fechaCierre, 'id' => $id]);
    }

    public static function asignar(int $id, ?int $funcionarioId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE solicitudes_tramites SET funcionario_id = :f WHERE id = :id");
        $stmt->execute(['f' => $funcionarioId, 'id' => $id]);
    }

    public static function seguimiento(int $id, bool $soloPublico = false): array {
        $db = Database::getConnection();
        $sql = "
            SELECT s.*,
                   ea.nombre AS estado_anterior,
                   en.nombre AS estado_nuevo,
                   en.color_badge,
                   u.email AS usuario_email,
                   r.nombre AS usuario_rol,
                   f.nombres AS funcionario_nombres,
                   f.apellidos AS funcionario_apellidos
            FROM seguimiento_tramites s
            LEFT JOIN estados_tramites ea ON ea.id = s.estado_anterior_id
            INNER JOIN estados_tramites en ON en.id = s.estado_nuevo_id
            INNER JOIN usuarios u ON u.id = s.usuario_id
            INNER JOIN roles r ON r.id = u.rol_id
            LEFT JOIN funcionarios f ON f.usuario_id = u.id
            WHERE s.solicitud_tramite_id = :id
        ";
        if ($soloPublico) {
            $sql .= " AND s.es_publico = 1";
        }
        $sql .= " ORDER BY s.created_at ASC, s.id ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    public static function agregarSeguimiento(int $solicitudId, int $usuarioId, ?int $estadoAnterior, int $estadoNuevo, string $observacion, bool $esPublico = true): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO seguimiento_tramites (solicitud_tramite_id, usuario_id, estado_anterior_id, estado_nuevo_id, observacion, es_publico, created_at)
            VALUES (:solicitud, :usuario, :anterior, :nuevo, :observacion, :publico, NOW())
        ");
        $stmt->execute([
            'solicitud'  => $solicitudId,
            'usuario'    => $usuarioId,
            'anterior'   => $estadoAnterior,
            'nuevo'      => $estadoNuevo,
            'observacion'=> $observacion,
            'publico'    => $esPublico ? 1 : 0,
        ]);
        return (int)$db->lastInsertId();
    }

    public static function contarPorEstado(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT e.codigo, e.nombre, e.color_badge, COUNT(s.id) AS total
            FROM estados_tramites e
            LEFT JOIN solicitudes_tramites s ON s.estado_tramite_id = e.id
            GROUP BY e.id
            ORDER BY e.id
        ");
        return $stmt->fetchAll();
    }
}
