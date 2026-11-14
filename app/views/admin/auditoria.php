<div class="page-heading">
    <h1>Historial de actividad del sistema</h1>
    <p>Trazabilidad de las acciones importantes realizadas por los usuarios.</p>
</div>

<form class="filter-bar" method="get" action="<?= url('admin/auditoria') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label mb-1">Acción</label>
            <input type="text" class="form-control" name="accion" value="<?= e($filtros['accion']) ?>" placeholder="Ej: CREAR_USUARIO">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Desde</label>
            <input type="date" class="form-control" name="desde" value="<?= e($filtros['desde']) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label mb-1">Hasta</label>
            <input type="date" class="form-control" name="hasta" value="<?= e($filtros['hasta']) ?>">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
            <a href="<?= url('admin/auditoria') ?>" class="btn btn-soft"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-check me-1"></i> Registros de auditoría</span>
        <span class="small text-muted"><?= count($registros) ?> registro(s) · últimos 300</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($registros)): ?>
            <div class="empty-state"><i class="bi bi-shield-check"></i>No hay registros de auditoría con los filtros aplicados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Tabla</th><th>Registro</th><th>Detalle</th><th>IP</th></tr></thead>
                    <tbody>
                        <?php foreach ($registros as $r): ?>
                            <tr>
                                <td class="small text-muted text-nowrap"><?= format_fecha($r['created_at'], true) ?></td>
                                <td class="small">
                                    <?= e($r['email'] ?? 'Sistema') ?>
                                    <?php if (!empty($r['rol'])): ?><div class="text-muted"><?= e(role_label($r['rol'])) ?></div><?php endif; ?>
                                </td>
                                <td><span class="badge bg-primary"><?= e($r['accion']) ?></span></td>
                                <td class="small text-muted"><?= e($r['tabla_afectada']) ?></td>
                                <td class="small"><?= $r['registro_id'] !== null ? '#' . (int)$r['registro_id'] : '—' ?></td>
                                <td class="small"><?= e(truncate((string)$r['detalles'], 70)) ?></td>
                                <td class="small text-muted"><?= e($r['ip'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
