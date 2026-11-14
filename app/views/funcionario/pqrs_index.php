<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Mis PQRS asignadas</h1>
        <p>Solicitudes bajo su responsabilidad para gestión y respuesta.</p>
    </div>
    <a href="<?= url('funcionario/pqrs/pendientes') ?>" class="btn btn-outline-warning"><i class="bi bi-hourglass-split me-1"></i> Ver pendientes</a>
</div>

<form class="filter-bar" method="get" action="<?= url('funcionario/pqrs') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label mb-1">Buscar</label>
            <input type="text" class="form-control" name="q" value="<?= e($filtros['q'] ?? '') ?>" placeholder="Radicado o asunto">
        </div>
        <div class="col-md-4">
            <label class="form-label mb-1">Estado</label>
            <select class="form-select" name="estado">
                <option value="">Todos</option>
                <?php foreach ($estados as $es): ?>
                    <option value="<?= (int)$es['id'] ?>" <?= (int)($filtros['estado'] ?? 0) === (int)$es['id'] ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
            <a href="<?= url('funcionario/pqrs') ?>" class="btn btn-soft"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($pqrs)): ?>
            <div class="empty-state"><i class="bi bi-inbox"></i>No se encontraron solicitudes asignadas.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Radicado</th><th>Ciudadano</th><th>Tipo</th><th>Asunto</th><th>Fecha</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                    <tbody>
                        <?php foreach ($pqrs as $p): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($p['radicado']) ?></span></td>
                                <td class="small"><?= e(trim(($p['ciudadano_nombres'] ?? '') . ' ' . ($p['ciudadano_apellidos'] ?? ''))) ?></td>
                                <td class="small"><?= e($p['tipo_nombre']) ?></td>
                                <td class="small"><?= e(truncate($p['asunto'], 40)) ?></td>
                                <td class="small text-muted"><?= format_fecha($p['fecha_radicacion']) ?></td>
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
