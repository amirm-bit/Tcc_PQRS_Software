<?php
$query = http_build_query(array_filter($filtros, fn($v) => $v !== null && $v !== ''));
?>
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Reportes administrativos</h1>
        <p>Estadísticas y exportación de información de PQRS y trámites.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/reportes/csv') . ($query !== '' ? '?' . $query : '') ?>" class="btn btn-success"><i class="bi bi-filetype-csv me-1"></i> Exportar CSV</a>
        <button class="btn btn-soft" onclick="window.print()"><i class="bi bi-printer me-1"></i> Imprimir</button>
    </div>
</div>

<form class="filter-bar" method="get" action="<?= url('admin/reportes') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label mb-1">Reporte</label>
            <select class="form-select" name="tipo_reporte">
                <option value="pqrs" <?= $filtros['tipo_reporte'] === 'pqrs' ? 'selected' : '' ?>>PQRS</option>
                <option value="tramites" <?= $filtros['tipo_reporte'] === 'tramites' ? 'selected' : '' ?>>Trámites</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Desde</label>
            <input type="date" class="form-control" name="fecha_desde" value="<?= e($filtros['fecha_desde']) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Hasta</label>
            <input type="date" class="form-control" name="fecha_hasta" value="<?= e($filtros['fecha_hasta']) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Estado</label>
            <select class="form-select" name="estado">
                <option value="">Todos</option>
                <?php foreach ($estados as $es): ?>
                    <option value="<?= (int)$es['id'] ?>" <?= (string)$filtros['estado'] === (string)$es['id'] ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Tipo</label>
            <select class="form-select" name="tipo">
                <option value="">Todos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= (int)$t['id'] ?>" <?= (string)$filtros['tipo'] === (string)$t['id'] ? 'selected' : '' ?>><?= e($t['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Aplicar</button>
            <a href="<?= url('admin/reportes') ?>" class="btn btn-soft"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-collection"></i></div>
            <div class="stat-value"><?= number_format($resumen['total']) ?></div>
            <div class="stat-label">Total registros</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-check2-circle"></i></div>
            <div class="stat-value"><?= number_format($resumen['atendidas']) ?></div>
            <div class="stat-label">Atendidas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value"><?= number_format($resumen['pendientes']) ?></div>
            <div class="stat-label">Pendientes</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon slate"><i class="bi bi-stopwatch"></i></div>
            <div class="stat-value"><?= number_format((float)$resumen['promedio_dias'], 1) ?></div>
            <div class="stat-label">Promedio días de cierre</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-1"></i> <?= $esTramite ? 'Solicitudes de trámites' : 'PQRS' ?></span>
        <span class="small text-muted"><?= count($registros) ?> registro(s)</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($registros)): ?>
            <div class="empty-state"><i class="bi bi-table"></i>No hay datos para los filtros seleccionados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Radicado</th>
                            <th>Ciudadano</th>
                            <th>Documento</th>
                            <th><?= $esTramite ? 'Trámite' : 'Tipo' ?></th>
                            <?php if (!$esTramite): ?><th>Asunto</th><?php endif; ?>
                            <th>Estado</th>
                            <th>Funcionario</th>
                            <th>Radicación</th>
                            <th>Cierre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($r['radicado']) ?></span></td>
                                <td class="small"><?= e(trim(($r['ciudadano_nombres'] ?? '') . ' ' . ($r['ciudadano_apellidos'] ?? ''))) ?></td>
                                <td class="small"><?= e($r['numero_documento'] ?? '—') ?></td>
                                <td class="small"><?= e($r['tipo_nombre']) ?></td>
                                <?php if (!$esTramite): ?><td class="small"><?= e(truncate($r['asunto'], 40)) ?></td><?php endif; ?>
                                <td><span class="badge bg-<?= badge_estado($r['color_badge']) ?>"><?= e($r['estado_nombre']) ?></span></td>
                                <td class="small"><?= e(trim(($r['funcionario_nombres'] ?? '') . ' ' . ($r['funcionario_apellidos'] ?? '')) ?: '—') ?></td>
                                <td class="small text-muted"><?= format_fecha($r['fecha_radicacion']) ?></td>
                                <td class="small text-muted"><?= format_fecha($r['fecha_cierre']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
