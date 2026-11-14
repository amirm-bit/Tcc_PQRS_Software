<?php
// app/controllers/HomeController.php - Páginas públicas institucionales

class HomeController {
    public function index(): void {
        $tiposTramites = Tramite::tiposActivos();
        $db = Database::getConnection();
        $totalPqrs = (int)$db->query("SELECT COUNT(*) FROM pqrs")->fetchColumn();
        $totalTramites = (int)$db->query("SELECT COUNT(*) FROM solicitudes_tramites")->fetchColumn();
        $totalCiudadanos = (int)$db->query("SELECT COUNT(*) FROM ciudadanos")->fetchColumn();

        View::render('public/home', [
            'tiposTramites'  => $tiposTramites,
            'totalPqrs'      => $totalPqrs,
            'totalTramites'  => $totalTramites,
            'totalCiudadanos'=> $totalCiudadanos,
        ], 'public', ['title' => 'Inicio institucional', 'active' => 'inicio']);
    }

    public function about(): void {
        View::render('public/about', [], 'public', ['title' => 'Acerca del sistema', 'active' => 'acerca']);
    }

    public function servicios(): void {
        View::render('public/servicios', [
            'tiposTramites' => Tramite::tiposActivos(),
            'tiposPqrs'     => Pqrs::tipos(),
        ], 'public', ['title' => 'Servicios y trámites', 'active' => 'servicios']);
    }
}
