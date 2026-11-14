<?php
// app/models/Dashboard.php - Métricas y estadísticas calculadas desde MySQL

class Dashboard {
    public static function admin(): array {
        $db = Database::getConnection();

        $stats = [];
        $stats['total_pqrs']        = (int)$db->query("SELECT COUNT(*) FROM pqrs")->fetchColumn();
        $stats['pqrs_pendientes']   = (int)$db->query("SELECT COUNT(*) FROM pqrs WHERE estado_pqrs_id IN (1,2,3)")->fetchColumn();
        $stats['pqrs_en_proceso']   = (int)$db->query("SELECT COUNT(*) FROM pqrs WHERE estado_pqrs_id IN (4,5)")->fetchColumn();
        $stats['pqrs_respondidas']  = (int)$db->query("SELECT COUNT(*) FROM pqrs WHERE estado_pqrs_id = 6")->fetchColumn();
        $stats['pqrs_cerradas']     = (int)$db->query("SELECT COUNT(*) FROM pqrs WHERE estado_pqrs_id = 7")->fetchColumn();
        $stats['total_ciudadanos']  = (int)$db->query("SELECT COUNT(*) FROM ciudadanos")->fetchColumn();
        $stats['total_funcionarios']= (int)$db->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn();
        $stats['total_tramites']    = (int)$db->query("SELECT COUNT(*) FROM solicitudes_tramites")->fetchColumn();
        $stats['tramites_pendientes'] = (int)$db->query("SELECT COUNT(*) FROM solicitudes_tramites WHERE estado_tramite_id IN (1,2,3,4)")->fetchColumn();
        $stats['tramites_finalizados'] = (int)$db->query("SELECT COUNT(*) FROM solicitudes_tramites WHERE estado_tramite_id IN (5,7)")->fetchColumn();

        return $stats;
    }

    public static function funcionario(int $funcionarioId): array {
        $db = Database::getConnection();
        $stats = [
            'asignadas'   => 0,
            'pendientes'  => 0,
            'en_proceso'  => 0,
            'respondidas' => 0,
            'tramites'    => 0,
        ];
        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE funcionario_id = :f");
        $stmt->execute(['f' => $funcionarioId]);
        $stats['asignadas'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE funcionario_id = :f AND estado_pqrs_id IN (1,2,3,4)");
        $stmt->execute(['f' => $funcionarioId]);
        $stats['pendientes'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE funcionario_id = :f AND estado_pqrs_id IN (5)");
        $stmt->execute(['f' => $funcionarioId]);
        $stats['en_proceso'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE funcionario_id = :f AND estado_pqrs_id IN (6,7)");
        $stmt->execute(['f' => $funcionarioId]);
        $stats['respondidas'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM solicitudes_tramites WHERE funcionario_id = :f");
        $stmt->execute(['f' => $funcionarioId]);
        $stats['tramites'] = (int)$stmt->fetchColumn();

        return $stats;
    }

    public static function ciudadano(int $ciudadanoId): array {
        $db = Database::getConnection();
        $stats = [
            'total'      => 0,
            'abiertas'   => 0,
            'respondidas'=> 0,
            'cerradas'   => 0,
            'tramites'   => 0,
        ];
        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE ciudadano_id = :c");
        $stmt->execute(['c' => $ciudadanoId]);
        $stats['total'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE ciudadano_id = :c AND estado_pqrs_id IN (1,2,3,4,5)");
        $stmt->execute(['c' => $ciudadanoId]);
        $stats['abiertas'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE ciudadano_id = :c AND estado_pqrs_id = 6");
        $stmt->execute(['c' => $ciudadanoId]);
        $stats['respondidas'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM pqrs WHERE ciudadano_id = :c AND estado_pqrs_id = 7");
        $stmt->execute(['c' => $ciudadanoId]);
        $stats['cerradas'] = (int)$stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM solicitudes_tramites WHERE ciudadano_id = :c");
        $stmt->execute(['c' => $ciudadanoId]);
        $stats['tramites'] = (int)$stmt->fetchColumn();

        return $stats;
    }

    public static function activosFuncionarios(): array {
        $db = Database::getConnection();
        return $db->query("
            SELECT f.id, CONCAT(f.nombres, ' ', f.apellidos) AS nombre,
                   COUNT(p.id) AS total
            FROM funcionarios f
            LEFT JOIN pqrs p ON p.funcionario_id = f.id
            GROUP BY f.id
            ORDER BY total DESC
        ")->fetchAll();
    }
}
