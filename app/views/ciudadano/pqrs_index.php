<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Mis PQRS</h1>
        <p>Consulte el estado y seguimiento de sus solicitudes.</p>
    </div>
    <a href="<?= url('ciudadano/pqrs/nueva') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Nueva PQRS</a>
</div>

<form class="filter-bar" method="get" action="<?= url('ciudadano/pqrs') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Buscar</label>
            <input type="text" class="form-control" name="q" value="<?= e($filtros['q'] ?? '') ?>" placeholder="Radicado o asunto">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Tipo</label>
            <select class="form-select" name="tipo">
                <option value="">Todos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= (int)$t['id'] ?>" <?= (int)($filtros['tipo'] ?? 0) === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Estado</label>
            <select class="form-select" name="estado">
                <option value="">Todos</option>
                <?php foreach ($estados as $es): ?>
                    <option value="<?= (int)$es['id'] ?>" <?= (int)($filtros['estado'] ?? 0) === (int)$es['id'] ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
            <a href="<?= url('ciudadano/pqrs') ?>" class="btn btn-soft" title="Limpiar"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($pqrs)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                No se encontraron PQRS con los criterios de búsqueda.
                <div class="mt-2"><a href="<?= url('ciudadano/pqrs/nueva') ?>" class="btn btn-sm btn-primary">Radicar nueva PQRS</a></div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr><th>Radicado</th><th>Tipo</th><th>Asunto</th><th>Radicado el</th><th>Estado</th><th>Funcionario</th><th class="text-end">Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pqrs as $p): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($p['radicado']) ?></span></td>
                                <td class="small"><?= e($p['tipo_nombre']) ?></td>
                                <td class="small"><?= e(truncate($p['asunto'], 50)) ?></td>
                                <td class="small text-muted"><?= format_fecha($p['fecha_radicacion']) ?></td>
                                <td><span class="badge bg-<?= badge_estado($p['color_badge']) ?>"><?= e($p['estado_nombre']) ?></span></td>
                                <td class="small text-muted"><?= e(trim(($p['funcionario_nombres'] ?? '') . ' ' . ($p['funcionario_apellidos'] ?? '')) ?: 'Por asignar') ?></td>
                                <td class="text-end">
                                    <a href="<?= url('ciudadano/pqrs/detalle/' . (int)$p['id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-eye me-1"></i> Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
