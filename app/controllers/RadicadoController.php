<?php
// app/controllers/RadicadoController.php - Consulta pública de radicados

class RadicadoController {
    public function index(): void {
        View::render('public/consulta', [], 'public', ['title' => 'Consultar radicado', 'active' => 'consulta']);
    }

    public function consultar(): void {
        $radicado = strtoupper(trim($_POST['radicado'] ?? ''));
        if ($radicado === '') {
            flash('danger', 'Ingrese un número de radicado para consultar.');
            Response::redirect('consultar-radicado');
        }

        $pqrs = Pqrs::porRadicado($radicado);
        if ($pqrs) {
            View::render('public/consulta_resultado', [
                'tipo'        => 'pqrs',
                'registro'    => $pqrs,
                'seguimiento' => Pqrs::seguimiento((int)$pqrs['id'], true),
            ], 'public', ['title' => 'Resultado de consulta', 'active' => 'consulta']);
            return;
        }

        $tramite = Tramite::porRadicado($radicado);
        if ($tramite) {
            View::render('public/consulta_resultado', [
                'tipo'        => 'tramite',
                'registro'    => $tramite,
                'seguimiento' => Tramite::seguimiento((int)$tramite['id'], true),
            ], 'public', ['title' => 'Resultado de consulta', 'active' => 'consulta']);
            return;
        }

        flash('warning', 'No se encontró ninguna solicitud con el radicado ' . $radicado . '. Verifique e intente nuevamente.');
        Response::redirect('consultar-radicado');
    }
}
