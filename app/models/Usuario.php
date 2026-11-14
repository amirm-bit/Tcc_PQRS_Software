<?php
// app/models/Usuario.php - Gestión de usuarios (administración)

class Usuario {
    public static function all(array $filtros = []): array {
        $db = Database::getConnection();
        $sql = "
            SELECT u.*, r.nombre AS rol,
                   c.nombres AS c_nombres, c.apellidos AS c_apellidos, c.numero_documento,
                   f.nombres AS f_nombres, f.apellidos AS f_apellidos, f.cargo,
                   d.nombre AS dependencia_nombre
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            LEFT JOIN ciudadanos c ON c.usuario_id = u.id
            LEFT JOIN funcionarios f ON f.usuario_id = u.id
            LEFT JOIN dependencias d ON d.id = f.dependencia_id
            WHERE 1=1
        ";
        $params = [];
        if (!empty($filtros['rol'])) {
            if ($filtros['rol'] === 'admin') {
                $sql .= " AND r.nombre = 'admin'";
            } else {
                $sql .= " AND r.nombre = :rol";
                $params['rol'] = $filtros['rol'];
            }
        }
        if (!empty($filtros['q'])) {
            $sql .= " AND (u.email LIKE :q OR c.nombres LIKE :q OR c.apellidos LIKE :q OR f.nombres LIKE :q OR f.apellidos LIKE :q OR c.numero_documento LIKE :q)";
            $params['q'] = '%' . $filtros['q'] . '%';
        }
        $sql .= " ORDER BY u.id ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, r.nombre AS rol
            FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
            WHERE u.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function emailExiste(string $email, ?int $exceptoId = null): bool {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $params = ['email' => strtolower(trim($email))];
        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params['id'] = $exceptoId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function documentoExiste(string $documento): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM ciudadanos WHERE numero_documento = :d");
        $stmt->execute(['d' => $documento]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function crearCiudadano(array $data): int {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("
                INSERT INTO usuarios (rol_id, email, password, activo, created_at)
                VALUES (3, :email, :password, 1, NOW())
            ");
            $stmt->execute([
                'email'    => strtolower(trim($data['email'])),
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ]);
            $usuarioId = (int)$db->lastInsertId();

            $stmt = $db->prepare("
                INSERT INTO ciudadanos
                    (usuario_id, tipo_documento, numero_documento, nombres, apellidos, telefono, direccion, barrio)
                VALUES
                    (:usuario, :tipo_doc, :documento, :nombres, :apellidos, :telefono, :direccion, :barrio)
            ");
            $stmt->execute([
                'usuario'   => $usuarioId,
                'tipo_doc'  => $data['tipo_documento'] ?? 'CC',
                'documento' => $data['numero_documento'],
                'nombres'   => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'telefono'  => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'barrio'    => $data['barrio'] ?? null,
            ]);

            $db->commit();
            return $usuarioId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function crearAdmin(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO usuarios (rol_id, email, password, activo, created_at)
            VALUES (1, :email, :password, 1, NOW())
        ");
        $stmt->execute([
            'email'    => strtolower(trim($data['email'])),
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);
        return (int)$db->lastInsertId();
    }

    public static function crearFuncionario(array $data): int {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("
                INSERT INTO usuarios (rol_id, email, password, activo, created_at)
                VALUES (2, :email, :password, 1, NOW())
            ");
            $stmt->execute([
                'email'    => strtolower(trim($data['email'])),
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ]);
            $usuarioId = (int)$db->lastInsertId();

            $stmt = $db->prepare("
                INSERT INTO funcionarios (usuario_id, dependencia_id, cargo, nombres, apellidos, telefono)
                VALUES (:usuario, :dependencia, :cargo, :nombres, :apellidos, :telefono)
            ");
            $stmt->execute([
                'usuario'     => $usuarioId,
                'dependencia' => $data['dependencia_id'],
                'cargo'       => $data['cargo'],
                'nombres'     => $data['nombres'],
                'apellidos'   => $data['apellidos'],
                'telefono'    => $data['telefono'] ?? null,
            ]);

            $db->commit();
            return $usuarioId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function actualizarCiudadano(int $usuarioId, array $data): void {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $sql = "UPDATE usuarios SET email = :email";
            $params = ['email' => strtolower(trim($data['email'])), 'usuario' => $usuarioId];
            if (!empty($data['password'])) {
                $sql .= ", password = :password";
                $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            $sql .= " WHERE id = :usuario";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $stmt = $db->prepare("
                UPDATE ciudadanos SET
                    tipo_documento = :tipo_doc,
                    numero_documento = :documento,
                    nombres = :nombres,
                    apellidos = :apellidos,
                    telefono = :telefono,
                    direccion = :direccion,
                    barrio = :barrio
                WHERE usuario_id = :usuario
            ");
            $stmt->execute([
                'tipo_doc'  => $data['tipo_documento'] ?? 'CC',
                'documento' => $data['numero_documento'],
                'nombres'   => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'telefono'  => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'barrio'    => $data['barrio'] ?? null,
                'usuario'   => $usuarioId,
            ]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function actualizarFuncionario(int $usuarioId, array $data): void {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            $sql = "UPDATE usuarios SET email = :email";
            $params = ['email' => strtolower(trim($data['email'])), 'usuario' => $usuarioId];
            if (!empty($data['password'])) {
                $sql .= ", password = :password";
                $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            $sql .= " WHERE id = :usuario";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $stmt = $db->prepare("
                UPDATE funcionarios SET
                    dependencia_id = :dependencia,
                    cargo = :cargo,
                    nombres = :nombres,
                    apellidos = :apellidos,
                    telefono = :telefono
                WHERE usuario_id = :usuario
            ");
            $stmt->execute([
                'dependencia' => $data['dependencia_id'],
                'cargo'       => $data['cargo'],
                'nombres'     => $data['nombres'],
                'apellidos'   => $data['apellidos'],
                'telefono'    => $data['telefono'] ?? null,
                'usuario'     => $usuarioId,
            ]);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function cambiarPassword(int $usuarioId, string $password): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
        $stmt->execute(['password' => password_hash($password, PASSWORD_BCRYPT), 'id' => $usuarioId]);
    }

    public static function setActivo(int $id, bool $activo): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET activo = :activo WHERE id = :id");
        $stmt->execute(['activo' => $activo ? 1 : 0, 'id' => $id]);
    }

    public static function funcionarios(?int $dependenciaId = null): array {
        $db = Database::getConnection();
        $sql = "
            SELECT f.*, u.email, d.nombre AS dependencia_nombre
            FROM funcionarios f
            INNER JOIN usuarios u ON u.id = f.usuario_id
            LEFT JOIN dependencias d ON d.id = f.dependencia_id
            WHERE u.activo = 1
        ";
        $params = [];
        if ($dependenciaId !== null) {
            $sql .= " AND f.dependencia_id = :dep";
            $params['dep'] = $dependenciaId;
        }
        $sql .= " ORDER BY f.nombres";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function funcionarioPorUsuario(int $usuarioId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM funcionarios WHERE usuario_id = :id LIMIT 1");
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetch() ?: null;
    }

    public static function ciudadanoPorUsuario(int $usuarioId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM ciudadanos WHERE usuario_id = :id LIMIT 1");
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetch() ?: null;
    }

    public static function ciudadanos(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT c.*, u.email, u.activo, u.created_at
            FROM ciudadanos c
            INNER JOIN usuarios u ON u.id = c.usuario_id
            ORDER BY c.nombres
        ")->fetchAll();
    }
}
