<div class="page-heading">
    <h1>Solicitudes de trámites</h1>
    <p>Certificados y trámites radicados por los ciudadanos.</p>
</div>

<form class="filter-bar" method="get" action="<?= url('admin/tramites/solicitudes') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label mb-1">Buscar</label>
            <input type="text" class="form-control" name="q" value="<?= e($filtros['q']) ?>" placeholder="Radicado o ciudadano">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Estado</label>
            <select class="form-select" name="estado">
                <option value="">Todos</option>
                <?php foreach ($estados as $es): ?>
                    <option value="<?= (int)$es['id'] ?>" <?= (string)$filtros['estado'] === (string)$es['id'] ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Tipo de trámite</label>
            <select class="form-select" name="tipo">
                <option value="">Todos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= (int)$t['id'] ?>" <?= (string)$filtros['tipo'] === (string)$t['id'] ? 'selected' : '' ?>><?= e($t['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
            <a href="<?= url('admin/tramites/solicitudes') ?>" class="btn btn-soft"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-check me-1"></i> Resultados</span>
        <span class="small text-muted"><?= count($solicitudes) ?> registro(s)</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($solicitudes)): ?>
            <div class="empty-state"><i class="bi bi-file-earmark-x"></i>No se encontraron solicitudes.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Radicado</th><th>Ciudadano</th><th>Trámite</th><th>Funcionario</th><th>Fecha</th><th>Estado</th><th class="text-end"></th></tr></thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($s['radicado']) ?></span></td>
                                <td class="small">
                                    <?= e(trim(($s['ciudadano_nombres'] ?? '') . ' ' . ($s['ciudadano_apellidos'] ?? ''))) ?>
                                    <div class="text-muted"><?= e($s['numero_documento'] ?? '') ?></div>
                                </td>
                                <td class="small"><?= e($s['tipo_nombre']) ?></td>
                                <td class="small"><?= e(trim(($s['funcionario_nombres'] ?? '') . ' ' . ($s['funcionario_apellidos'] ?? '')) ?: 'Sin asignar') ?></td>
                                <td class="small text-muted"><?= format_fecha($s['fecha_radicacion']) ?></td>
                                <td><span class="badge bg-<?= badge_estado($s['color_badge']) ?>"><?= e($s['estado_nombre']) ?></span></td>
                                <td class="text-end"><a href="<?= url('admin/tramites/solicitudes/detalle/' . (int)$s['id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
