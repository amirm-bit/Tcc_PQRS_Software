<div class="page-heading">
    <h1>Gestión de PQRS</h1>
    <p>Todas las peticiones, quejas, reclamos y sugerencias del municipio.</p>
</div>

<form class="filter-bar" method="get" action="<?= url('admin/pqrs') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label mb-1">Buscar</label>
            <input type="text" class="form-control" name="q" value="<?= e($filtros['q']) ?>" placeholder="Radicado, asunto o ciudadano">
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
        <div class="col-md-2">
            <label class="form-label mb-1">Funcionario</label>
            <select class="form-select" name="funcionario">
                <option value="">Todos</option>
                <?php foreach ($funcionarios as $f): ?>
                    <option value="<?= (int)$f['id'] ?>" <?= (string)$filtros['funcionario'] === (string)$f['id'] ? 'selected' : '' ?>><?= e($f['nombres'] . ' ' . $f['apellidos']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label mb-1">Desde</label>
            <input type="date" class="form-control" name="fecha_desde" value="<?= e($filtros['fecha_desde']) ?>">
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button class="btn btn-primary flex-grow-1" title="Filtrar"><i class="bi bi-funnel"></i></button>
        </div>
    </div>
    <div class="row g-2 mt-1">
        <div class="col-md-3">
            <label class="form-label mb-1">Hasta</label>
            <input type="date" class="form-control" name="fecha_hasta" value="<?= e($filtros['fecha_hasta']) ?>">
        </div>
        <div class="col-md-2 pt-4">
            <a href="<?= url('admin/pqrs') ?>" class="btn btn-soft w-100"><i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar</a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-inbox me-1"></i> Resultados</span>
        <span class="small text-muted"><?= count($pqrs) ?> registro(s)</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($pqrs)): ?>
            <div class="empty-state"><i class="bi bi-inbox"></i>No se encontraron solicitudes con los filtros aplicados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Radicado</th><th>Ciudadano</th><th>Tipo</th><th>Asunto</th><th>Funcionario</th><th>Fecha</th><th>Estado</th><th class="text-end"></th></tr></thead>
                    <tbody>
                        <?php foreach ($pqrs as $p): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($p['radicado']) ?></span></td>
                                <td class="small">
                                    <?= e(trim(($p['ciudadano_nombres'] ?? '') . ' ' . ($p['ciudadano_apellidos'] ?? ''))) ?>
                                    <div class="text-muted"><?= e($p['numero_documento'] ?? '') ?></div>
                                </td>
                                <td class="small"><?= e($p['tipo_nombre']) ?></td>
                                <td class="small"><?= e(truncate($p['asunto'], 40)) ?></td>
                                <td class="small"><?= e(trim(($p['funcionario_nombres'] ?? '') . ' ' . ($p['funcionario_apellidos'] ?? '')) ?: '<span class="text-muted">Sin asignar</span>') ?></td>
                                <td class="small text-muted"><?= format_fecha($p['fecha_radicacion']) ?></td>
                                <td><span class="badge bg-<?= badge_estado($p['color_badge']) ?>"><?= e($p['estado_nombre']) ?></span></td>
                                <td class="text-end"><a href="<?= url('admin/pqrs/detalle/' . (int)$p['id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
