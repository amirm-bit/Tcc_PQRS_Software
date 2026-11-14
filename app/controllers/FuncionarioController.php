<?php
// app/controllers/FuncionarioController.php - Panel del funcionario

class FuncionarioController {
    private function funcionarioActual(): array {
        $funcionario = Usuario::funcionarioPorUsuario((int)Auth::id());
        if (!$funcionario) {
            flash('danger', 'No se encontró el perfil de funcionario.');
            Response::redirect('logout');
        }
        return $funcionario;
    }

    public function dashboard(): void {
        $funcionario = $this->funcionarioActual();
        $stats = Dashboard::funcionario((int)$funcionario['id']);
        $pqrs = Pqrs::porFuncionario((int)$funcionario['id'], ['limite' => 6]);
        $tramites = Tramite::porFuncionario((int)$funcionario['id']);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ep.nombre, ep.color_badge, COUNT(p.id) AS total
            FROM estados_pqrs ep
            LEFT JOIN pqrs p ON p.estado_pqrs_id = ep.id AND p.funcionario_id = :f
            GROUP BY ep.id ORDER BY ep.id
        ");
        $stmt->execute(['f' => (int)$funcionario['id']]);
        $porEstado = $stmt->fetchAll();

        View::render('funcionario/dashboard', [
            'funcionario' => $funcionario,
            'stats'       => $stats,
            'pqrs'        => $pqrs,
            'tramites'    => $tramites,
            'porEstado'   => $porEstado,
        ], 'panel', [
            'title'       => 'Dashboard',
            'pageTitle'   => 'Tablero de trabajo',
            'pageSubtitle'=> 'Solicitudes asignadas y pendientes',
            'activeMenu'  => 'dashboard',
        ]);
    }

    public function historial(): void {
        $funcionario = $this->funcionarioActual();
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT * FROM auditoria
            WHERE usuario_id = :u
            ORDER BY created_at DESC
            LIMIT 100
        ");
        $stmt->execute(['u' => (int)Auth::id()]);
        $auditoria = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT s.*, p.radicado
            FROM seguimiento_pqrs s
            INNER JOIN pqrs p ON p.id = s.pqrs_id
            WHERE s.usuario_id = :u
            ORDER BY s.created_at DESC
            LIMIT 50
        ");
        $stmt->execute(['u' => (int)Auth::id()]);
        $seguimientos = $stmt->fetchAll();

        View::render('funcionario/historial', [
            'funcionario' => $funcionario,
            'auditoria'   => $auditoria,
            'seguimientos'=> $seguimientos,
        ], 'panel', [
            'title'       => 'Historial de actividades',
            'pageTitle'   => 'Historial de actividades',
            'pageSubtitle'=> 'Registro de sus gestiones',
            'activeMenu'  => 'historial',
        ]);
    }
}
