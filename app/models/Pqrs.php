<?php
// app/models/Pqrs.php - Acceso a datos de PQRS

class Pqrs {
    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM pqrs WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function detalle(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.*,
                   tp.nombre AS tipo_nombre,
                   tp.dias_termino,
                   ep.nombre AS estado_nombre,
                   ep.codigo AS estado_codigo,
                   ep.color_badge,
                   c.nombres AS ciudadano_nombres,
                   c.apellidos AS ciudadano_apellidos,
                   c.numero_documento,
                   c.telefono AS ciudadano_telefono,
                   c.direccion AS ciudadano_direccion,
                   c.barrio AS ciudadano_barrio,
                   u.email AS ciudadano_email,
                   f.nombres AS funcionario_nombres,
                   f.apellidos AS funcionario_apellidos,
                   f.cargo AS funcionario_cargo,
                   d.nombre AS dependencia_nombre
            FROM pqrs p
            INNER JOIN tipos_pqrs tp ON tp.id = p.tipo_pqrs_id
            INNER JOIN estados_pqrs ep ON ep.id = p.estado_pqrs_id
            INNER JOIN ciudadanos c ON c.id = p.ciudadano_id
            INNER JOIN usuarios u ON u.id = c.usuario_id
            LEFT JOIN funcionarios f ON f.id = p.funcionario_id
            LEFT JOIN dependencias d ON d.id = p.dependencia_id
            WHERE p.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function porRadicado(string $radicado): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM pqrs WHERE radicado = :r LIMIT 1");
        $stmt->execute(['r' => $radicado]);
        $id = $stmt->fetchColumn();
        return $id ? self::detalle((int)$id) : null;
    }

    public static function porCiudadano(int $ciudadanoId, array $filtros = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT p.*, tp.nombre AS tipo_nombre, ep.nombre AS estado_nombre, ep.color_badge,
                   f.nombres AS funcionario_nombres, f.apellidos AS funcionario_apellidos
            FROM pqrs p
            INNER JOIN tipos_pqrs tp ON tp.id = p.tipo_pqrs_id
            INNER JOIN estados_pqrs ep ON ep.id = p.estado_pqrs_id
            LEFT JOIN funcionarios f ON f.id = p.funcionario_id
            WHERE p.ciudadano_id = :ciudadano
        ";
        $params = ['ciudadano' => $ciudadanoId];

        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado_pqrs_id = :estado";
            $params['estado'] = $filtros['estado'];
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND p.tipo_pqrs_id = :tipo";
            $params['tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['q'])) {
            $sql .= " AND (p.radicado LIKE :q OR p.asunto LIKE :q)";
            $params['q'] = '%' . $filtros['q'] . '%';
        }

        $sql .= " ORDER BY p.fecha_radicacion DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function porFuncionario(int $funcionarioId, array $filtros = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT p.*, tp.nombre AS tipo_nombre, ep.nombre AS estado_nombre, ep.color_badge,
                   c.nombres AS ciudadano_nombres, c.apellidos AS ciudadano_apellidos
            FROM pqrs p
            INNER JOIN tipos_pqrs tp ON tp.id = p.tipo_pqrs_id
            INNER JOIN estados_pqrs ep ON ep.id = p.estado_pqrs_id
            INNER JOIN ciudadanos c ON c.id = p.ciudadano_id
            WHERE p.funcionario_id = :funcionario
        ";
        $params = ['funcionario' => $funcionarioId];

        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado_pqrs_id = :estado";
            $params['estado'] = $filtros['estado'];
        }
        if (!empty($filtros['q'])) {
            $sql .= " AND (p.radicado LIKE :q OR p.asunto LIKE :q)";
            $params['q'] = '%' . $filtros['q'] . '%';
        }
        $sql .= " ORDER BY p.fecha_radicacion DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function todos(array $filtros = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT p.*, tp.nombre AS tipo_nombre, ep.nombre AS estado_nombre, ep.codigo AS estado_codigo, ep.color_badge,
                   c.nombres AS ciudadano_nombres, c.apellidos AS ciudadano_apellidos, c.numero_documento,
                   f.nombres AS funcionario_nombres, f.apellidos AS funcionario_apellidos
            FROM pqrs p
            INNER JOIN tipos_pqrs tp ON tp.id = p.tipo_pqrs_id
            INNER JOIN estados_pqrs ep ON ep.id = p.estado_pqrs_id
            INNER JOIN ciudadanos c ON c.id = p.ciudadano_id
            LEFT JOIN funcionarios f ON f.id = p.funcionario_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['q'])) {
            $sql .= " AND (p.radicado LIKE :q OR p.asunto LIKE :q OR c.nombres LIKE :q OR c.apellidos LIKE :q)";
            $params['q'] = '%' . $filtros['q'] . '%';
        }
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado_pqrs_id = :estado";
            $params['estado'] = $filtros['estado'];
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND p.tipo_pqrs_id = :tipo";
            $params['tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['funcionario'])) {
            $sql .= " AND p.funcionario_id = :funcionario";
            $params['funcionario'] = $filtros['funcionario'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(p.fecha_radicacion) >= :fecha_desde";
            $params['fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(p.fecha_radicacion) <= :fecha_hasta";
            $params['fecha_hasta'] = $filtros['fecha_hasta'];
        }
        if (!empty($filtros['ciudadano'])) {
            $sql .= " AND p.ciudadano_id = :ciudadano";
            $params['ciudadano'] = $filtros['ciudadano'];
        }

        $sql .= " ORDER BY p.fecha_radicacion DESC";
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
            INSERT INTO pqrs
                (radicado, ciudadano_id, tipo_pqrs_id, estado_pqrs_id, funcionario_id, dependencia_id,
                 asunto, descripcion, medio_contacto, email_contacto, telefono_contacto,
                 fecha_radicacion, fecha_vencimiento, created_at)
            VALUES
                (:radicado, :ciudadano_id, :tipo_pqrs_id, :estado_pqrs_id, :funcionario_id, :dependencia_id,
                 :asunto, :descripcion, :medio_contacto, :email_contacto, :telefono_contacto,
                 NOW(), :fecha_vencimiento, NOW())
        ");
        $stmt->execute([
            'radicado'        => $data['radicado'],
            'ciudadano_id'    => $data['ciudadano_id'],
            'tipo_pqrs_id'    => $data['tipo_pqrs_id'],
            'estado_pqrs_id'  => $data['estado_pqrs_id'],
            'funcionario_id'  => $data['funcionario_id'] ?? null,
            'dependencia_id'  => $data['dependencia_id'] ?? null,
            'asunto'          => $data['asunto'],
            'descripcion'     => $data['descripcion'],
            'medio_contacto'  => $data['medio_contacto'],
            'email_contacto'  => $data['email_contacto'],
            'telefono_contacto' => $data['telefono_contacto'] ?? null,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
        ]);
        return (int)$db->lastInsertId();
    }

    public static function asignar(int $id, ?int $funcionarioId, ?int $dependenciaId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE pqrs SET funcionario_id = :funcionario, dependencia_id = COALESCE(:dependencia, dependencia_id) WHERE id = :id");
        $stmt->execute(['funcionario' => $funcionarioId, 'dependencia' => $dependenciaId, 'id' => $id]);
    }

    public static function cambiarEstado(int $id, int $estadoId, ?string $fechaCierre = null): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE pqrs SET estado_pqrs_id = :estado, fecha_cierre = :cierre WHERE id = :id");
        $stmt->execute(['estado' => $estadoId, 'cierre' => $fechaCierre, 'id' => $id]);
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
            FROM seguimiento_pqrs s
            LEFT JOIN estados_pqrs ea ON ea.id = s.estado_anterior_id
            INNER JOIN estados_pqrs en ON en.id = s.estado_nuevo_id
            INNER JOIN usuarios u ON u.id = s.usuario_id
            INNER JOIN roles r ON r.id = u.rol_id
            LEFT JOIN funcionarios f ON f.usuario_id = u.id
            WHERE s.pqrs_id = :id
        ";
        if ($soloPublico) {
            $sql .= " AND s.es_publico = 1";
        }
        $sql .= " ORDER BY s.created_at ASC, s.id ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    public static function agregarSeguimiento(int $pqrsId, int $usuarioId, ?int $estadoAnterior, int $estadoNuevo, string $observacion, bool $esPublico = true): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO seguimiento_pqrs (pqrs_id, usuario_id, estado_anterior_id, estado_nuevo_id, observacion, es_publico, created_at)
            VALUES (:pqrs, :usuario, :anterior, :nuevo, :observacion, :publico, NOW())
        ");
        $stmt->execute([
            'pqrs'       => $pqrsId,
            'usuario'    => $usuarioId,
            'anterior'   => $estadoAnterior,
            'nuevo'      => $estadoNuevo,
            'observacion'=> $observacion,
            'publico'    => $esPublico ? 1 : 0,
        ]);
        return (int)$db->lastInsertId();
    }

    public static function respuestas(int $id): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, f.nombres AS funcionario_nombres, f.apellidos AS funcionario_apellidos, f.cargo
            FROM respuestas_pqrs r
            INNER JOIN funcionarios f ON f.id = r.funcionario_id
            WHERE r.pqrs_id = :id
            ORDER BY r.fecha_respuesta ASC
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    public static function crearRespuesta(int $pqrsId, int $funcionarioId, string $contenido, ?string $archivo = null): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO respuestas_pqrs (pqrs_id, funcionario_id, contenido, fecha_respuesta, archivo_respuesta, created_at)
            VALUES (:pqrs, :funcionario, :contenido, NOW(), :archivo, NOW())
        ");
        $stmt->execute([
            'pqrs'        => $pqrsId,
            'funcionario' => $funcionarioId,
            'contenido'   => $contenido,
            'archivo'     => $archivo,
        ]);
        return (int)$db->lastInsertId();
    }

    public static function tipos(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM tipos_pqrs WHERE activo = 1 ORDER BY id")->fetchAll();
    }

    public static function estados(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM estados_pqrs ORDER BY id")->fetchAll();
    }

    public static function estadoPorCodigo(string $codigo): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM estados_pqrs WHERE codigo = :c LIMIT 1");
        $stmt->execute(['c' => $codigo]);
        return $stmt->fetch() ?: null;
    }

    public static function contarPorEstado(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT ep.codigo, ep.nombre, ep.color_badge, COUNT(p.id) AS total
            FROM estados_pqrs ep
            LEFT JOIN pqrs p ON p.estado_pqrs_id = ep.id
            GROUP BY ep.id
            ORDER BY ep.id
        ");
        return $stmt->fetchAll();
    }

    public static function contarPorTipo(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT tp.nombre, COUNT(p.id) AS total
            FROM tipos_pqrs tp
            LEFT JOIN pqrs p ON p.tipo_pqrs_id = tp.id
            GROUP BY tp.id
            ORDER BY tp.id
        ");
        return $stmt->fetchAll();
    }

    public static function porMes(int $anio): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT MONTH(fecha_radicacion) AS mes, COUNT(*) AS total
            FROM pqrs
            WHERE YEAR(fecha_radicacion) = :anio
            GROUP BY MONTH(fecha_radicacion)
            ORDER BY mes
        ");
        $stmt->execute(['anio' => $anio]);
        $rows = $stmt->fetchAll();
        $resultado = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $resultado[(int)$row['mes']] = (int)$row['total'];
        }
        return $resultado;
    }
}
