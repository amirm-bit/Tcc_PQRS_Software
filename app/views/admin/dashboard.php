<?php
$meses = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
$mesLabels = [];
$mesValues = [];
foreach ($porMes as $m => $v) {
    $mesLabels[] = $meses[(int)$m] ?? (string)$m;
    $mesValues[] = (int)$v;
}
$tipoLabels = array_map(fn($t) => $t['nombre'], $porTipo);
$tipoValues = array_map(fn($t) => (int)$t['total'], $porTipo);
$estadoLabels = array_map(fn($e) => $e['nombre'], $porEstado);
$estadoValues = array_map(fn($e) => (int)$e['total'], $porEstado);
$tramiteLabels = array_map(fn($e) => $e['nombre'], $tramitesPorEstado);
$tramiteValues = array_map(fn($e) => (int)$e['total'], $tramitesPorEstado);
$actLabels = array_map(fn($a) => $a['nombre'], $actividad);
$actValues = array_map(fn($a) => (int)$a['total'], $actividad);
?>
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Dashboard general</h1>
        <p>Indicadores institucionales calculados desde la base de datos en tiempo real.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/reportes') ?>" class="btn btn-outline-primary"><i class="bi bi-bar-chart-line me-1"></i> Reportes</a>
        <button class="btn btn-soft" onclick="window.print()"><i class="bi bi-printer me-1"></i> Imprimir</button>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-inbox"></i></div>
            <div class="stat-value"><?= number_format($stats['total_pqrs']) ?></div>
            <div class="stat-label">Total PQRS</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value"><?= number_format($stats['pqrs_pendientes']) ?></div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
            <div class="stat-value"><?= number_format($stats['pqrs_en_proceso']) ?></div>
            <div class="stat-label">En proceso</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-chat-left-text"></i></div>
            <div class="stat-value"><?= number_format($stats['pqrs_respondidas']) ?></div>
            <div class="stat-label">Respondidas</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon slate"><i class="bi bi-archive"></i></div>
            <div class="stat-value"><?= number_format($stats['pqrs_cerradas']) ?></div>
            <div class="stat-label">PQRS cerradas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-value"><?= number_format($stats['total_ciudadanos']) ?></div>
            <div class="stat-label">Ciudadanos registrados</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-person-badge"></i></div>
            <div class="stat-value"><?= number_format($stats['total_funcionarios']) ?></div>
            <div class="stat-label">Funcionarios</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-file-earmark-check"></i></div>
            <div class="stat-value"><?= number_format($stats['total_tramites']) ?></div>
            <div class="stat-label">Solicitudes de trámites</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart me-1"></i> PQRS por tipo</div>
            <div class="card-body"><div class="chart-box"><canvas></canvas><script type="application/json" class="chart-data"><?= json_encode(['type'=>'doughnut','labels'=>$tipoLabels,'values'=>$tipoValues,'label'=>'PQRS']) ?></script></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bar-chart me-1"></i> PQRS por estado</div>
            <div class="card-body"><div class="chart-box"><canvas></canvas><script type="application/json" class="chart-data"><?= json_encode(['type'=>'bar','labels'=>$estadoLabels,'values'=>$estadoValues,'label'=>'PQRS','legend'=>false]) ?></script></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-file-earmark-bar-graph me-1"></i> Trámites por estado</div>
            <div class="card-body"><div class="chart-box"><canvas></canvas><script type="application/json" class="chart-data"><?= json_encode(['type'=>'bar','labels'=>$tramiteLabels,'values'=>$tramiteValues,'label'=>'Trámites','legend'=>false]) ?></script></div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-graph-up me-1"></i> Solicitudes registradas por mes (<?= date('Y') ?>)</div>
            <div class="card-body"><div class="chart-box"><canvas></canvas><script type="application/json" class="chart-data"><?= json_encode(['type'=>'line','labels'=>$mesLabels,'values'=>$mesValues,'label'=>'PQRS por mes','fill'=>true,'legend'=>false]) ?></script></div></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-person-workspace me-1"></i> Actividad de funcionarios</div>
            <div class="card-body"><div class="chart-box"><canvas></canvas><script type="application/json" class="chart-data"><?= json_encode(['type'=>'bar','labels'=>$actLabels,'values'=>$actValues,'label'=>'PQRS asignadas','legend'=>false]) ?></script></div></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-1"></i> Solicitudes recientes</span>
        <a href="<?= url('admin/pqrs') ?>" class="small">Ver todas</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Radicado</th><th>Ciudadano</th><th>Tipo</th><th>Fecha</th><th>Estado</th><th>Funcionario</th><th class="text-end">Acción</th></tr></thead>
                <tbody>
                    <?php foreach ($recientes as $p): ?>
                        <tr>
                            <td><span class="radicado-pill"><?= e($p['radicado']) ?></span></td>
                            <td class="small"><?= e(trim(($p['ciudadano_nombres'] ?? '') . ' ' . ($p['ciudadano_apellidos'] ?? ''))) ?></td>
                            <td class="small"><?= e($p['tipo_nombre']) ?></td>
                            <td class="small text-muted"><?= format_fecha($p['fecha_radicacion']) ?></td>
                            <td><span class="badge bg-<?= badge_estado($p['color_badge']) ?>"><?= e($p['estado_nombre']) ?></span></td>
                            <td class="small text-muted"><?= e(trim(($p['funcionario_nombres'] ?? '') . ' ' . ($p['funcionario_apellidos'] ?? '')) ?: 'Por asignar') ?></td>
                            <td class="text-end"><a href="<?= url('admin/pqrs/detalle/' . (int)$p['id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recientes)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay PQRS registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
