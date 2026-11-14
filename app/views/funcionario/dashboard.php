<?php
$labelsEstado = array_map(fn($e) => $e['nombre'], $porEstado);
$valuesEstado = array_map(fn($e) => (int)$e['total'], $porEstado);
?>
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Tablero de trabajo</h1>
        <p>Hola, <?= e(trim(($funcionario['nombres'] ?? '') . ' ' . ($funcionario['apellidos'] ?? ''))) ?>. Estas son sus solicitudes asignadas.</p>
    </div>
    <a href="<?= url('funcionario/pqrs') ?>" class="btn btn-primary"><i class="bi bi-inbox me-1"></i> Ver mis PQRS</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-inbox"></i></div>
            <div class="stat-value"><?= (int)$stats['asignadas'] ?></div>
            <div class="stat-label">PQRS asignadas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value"><?= (int)$stats['pendientes'] ?></div>
            <div class="stat-label">Pendientes de gestión</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-check2-circle"></i></div>
            <div class="stat-value"><?= (int)$stats['respondidas'] ?></div>
            <div class="stat-label">Atendidas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon slate"><i class="bi bi-file-earmark-check"></i></div>
            <div class="stat-value"><?= (int)$stats['tramites'] ?></div>
            <div class="stat-label">Trámites asignados</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-inbox me-1"></i> Solicitudes recientes</span>
                <a href="<?= url('funcionario/pqrs') ?>" class="small">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pqrs)): ?>
                    <div class="empty-state"><i class="bi bi-inbox"></i>No tiene PQRS asignadas por el momento.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Radicado</th><th>Ciudadano</th><th>Tipo</th><th>Estado</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach ($pqrs as $p): ?>
                                    <tr>
                                        <td><span class="radicado-pill"><?= e($p['radicado']) ?></span></td>
                                        <td class="small"><?= e(trim(($p['ciudadano_nombres'] ?? '') . ' ' . ($p['ciudadano_apellidos'] ?? ''))) ?></td>
                                        <td class="small"><?= e($p['tipo_nombre']) ?></td>
                                        <td><span class="badge bg-<?= badge_estado($p['color_badge']) ?>"><?= e($p['estado_nombre']) ?></span></td>
                                        <td class="text-end"><a href="<?= url('funcionario/pqrs/gestionar/' . (int)$p['id']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-tools me-1"></i> Gestionar</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-pie-chart me-1"></i> Distribución por estado</div>
            <div class="card-body">
                <div class="chart-box sm">
                    <canvas></canvas>
                    <script type="application/json" class="chart-data">
                        <?= json_encode(['type' => 'doughnut', 'labels' => $labelsEstado, 'values' => $valuesEstado, 'label' => 'PQRS']) ?>
                    </script>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-file-earmark-check me-1"></i> Trámites asignados</div>
            <div class="list-group list-group-flush">
                <?php if (empty($tramites)): ?>
                    <div class="empty-state py-3"><i class="bi bi-file-earmark"></i>Sin trámites asignados</div>
                <?php else: foreach (array_slice($tramites, 0, 5) as $t): ?>
                    <a href="<?= url('funcionario/tramites/gestionar/' . (int)$t['id']) ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="radicado-pill"><?= e($t['radicado']) ?></span>
                            <span class="badge bg-<?= badge_estado($t['color_badge']) ?>"><?= e($t['estado_nombre']) ?></span>
                        </div>
                        <div class="small text-muted mt-1"><?= e($t['tipo_nombre']) ?></div>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
