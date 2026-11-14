<?php
// app/controllers/ReporteController.php - Reportes administrativos y exportación CSV

class ReporteController {
    private function filtros(): array {
        return [
            'tipo_reporte' => $_GET['tipo_reporte'] ?? 'pqrs',
            'fecha_desde'  => $_GET['fecha_desde'] ?? '',
            'fecha_hasta'  => $_GET['fecha_hasta'] ?? '',
            'estado'       => ($_GET['estado'] ?? '') !== '' ? (int)$_GET['estado'] : null,
            'tipo'         => ($_GET['tipo'] ?? '') !== '' ? (int)$_GET['tipo'] : null,
            'funcionario'  => ($_GET['funcionario'] ?? '') !== '' ? (int)$_GET['funcionario'] : null,
        ];
    }

    private function consultar(array $filtros): array {
        if (($filtros['tipo_reporte'] ?? 'pqrs') === 'tramites') {
            return Tramite::todas($filtros);
        }
        return Pqrs::todos($filtros);
    }

    private function resumen(array $filtros): array {
        $registros = $this->consultar($filtros);
        $total = count($registros);
        $atendidas = 0;
        $pendientes = 0;
        $tiempos = [];

        $esTramite = ($filtros['tipo_reporte'] ?? 'pqrs') === 'tramites';
        foreach ($registros as $r) {
            if ($esTramite) {
                $codigo = $r['estado_codigo'] ?? '';
                if (in_array($codigo, ['aprobada', 'finalizada'], true)) {
                    $atendidas++;
                } elseif (in_array($codigo, ['recibida', 'pendiente_revision', 'en_revision', 'requiere_info'], true)) {
                    $pendientes++;
                }
            } else {
                $codigo = '';
                foreach (Pqrs::estados() as $e) { if ((int)$e['id'] === (int)$r['estado_pqrs_id']) { $codigo = $e['codigo']; break; } }
                if (in_array($codigo, ['respondida', 'cerrada'], true)) {
                    $atendidas++;
                } else {
                    $pendientes++;
                }
            }
            if (!empty($r['fecha_cierre']) && !empty($r['fecha_radicacion'])) {
                $diff = strtotime($r['fecha_cierre']) - strtotime($r['fecha_radicacion']);
                if ($diff >= 0) {
                    $tiempos[] = $diff / 86400;
                }
            }
        }

        return [
            'total'      => $total,
            'atendidas'  => $atendidas,
            'pendientes' => $pendientes,
            'promedio_dias' => $tiempos ? round(array_sum($tiempos) / count($tiempos), 1) : 0,
        ];
    }

    public function index(): void {
        $filtros = $this->filtros();
        $registros = $this->consultar($filtros);
        $resumen = $this->resumen($filtros);
        $esTramite = $filtros['tipo_reporte'] === 'tramites';

        View::render('admin/reportes', [
            'filtros'      => $filtros,
            'registros'    => $registros,
            'resumen'      => $resumen,
            'esTramite'    => $esTramite,
            'estados'      => $esTramite ? Tramite::estados() : Pqrs::estados(),
            'tipos'        => $esTramite ? Tramite::tiposTodos() : Pqrs::tipos(),
            'funcionarios' => Usuario::funcionarios(),
        ], 'panel', [
            'title'       => 'Reportes',
            'pageTitle'   => 'Reportes administrativos',
            'pageSubtitle'=> 'Estadísticas y exportación de información',
            'activeMenu'  => 'reportes',
        ]);
    }

    public function exportarCsv(): void {
        $filtros = $this->filtros();
        $registros = $this->consultar($filtros);
        $esTramite = $filtros['tipo_reporte'] === 'tramites';

        $nombre = 'reporte_' . ($esTramite ? 'tramites' : 'pqrs') . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel

        if ($esTramite) {
            fputcsv($out, ['Radicado', 'Ciudadano', 'Documento', 'Tipo de trámite', 'Estado', 'Funcionario', 'Fecha radicación', 'Fecha cierre'], ';');
            foreach ($registros as $r) {
                fputcsv($out, [
                    $r['radicado'],
                    trim(($r['ciudadano_nombres'] ?? '') . ' ' . ($r['ciudadano_apellidos'] ?? '')),
                    $r['numero_documento'] ?? '',
                    $r['tipo_nombre'],
                    $r['estado_nombre'],
                    trim(($r['funcionario_nombres'] ?? '') . ' ' . ($r['funcionario_apellidos'] ?? '')),
                    $r['fecha_radicacion'],
                    $r['fecha_cierre'] ?? '',
                ], ';');
            }
        } else {
            fputcsv($out, ['Radicado', 'Ciudadano', 'Documento', 'Tipo', 'Asunto', 'Estado', 'Funcionario', 'Fecha radicación', 'Fecha cierre'], ';');
            foreach ($registros as $r) {
                fputcsv($out, [
                    $r['radicado'],
                    trim(($r['ciudadano_nombres'] ?? '') . ' ' . ($r['ciudadano_apellidos'] ?? '')),
                    $r['numero_documento'] ?? '',
                    $r['tipo_nombre'],
                    $r['asunto'],
                    $r['estado_nombre'],
                    trim(($r['funcionario_nombres'] ?? '') . ' ' . ($r['funcionario_apellidos'] ?? '')),
                    $r['fecha_radicacion'],
                    $r['fecha_cierre'] ?? '',
                ], ';');
            }
        }

        fclose($out);
        Audit::log('EXPORTAR_REPORTE', 'reportes', null, 'Exportación CSV de ' . ($esTramite ? 'trámites' : 'PQRS'));
        exit;
    }
}
