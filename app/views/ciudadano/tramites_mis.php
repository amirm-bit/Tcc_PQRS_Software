<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Mis trámites y certificados</h1>
        <p>Seguimiento de las solicitudes de trámites radicadas.</p>
    </div>
    <a href="<?= url('ciudadano/tramites') ?>" class="btn btn-outline-success"><i class="bi bi-plus-circle me-1"></i> Nuevo trámite</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($solicitudes)): ?>
            <div class="empty-state">
                <i class="bi bi-file-earmark-x"></i>
                No ha radicado solicitudes de trámites.
                <div class="mt-2"><a href="<?= url('ciudadano/tramites') ?>" class="btn btn-sm btn-success">Ver trámites disponibles</a></div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Radicado</th><th>Trámite</th><th>Radicado el</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($s['radicado']) ?></span></td>
                                <td class="small"><?= e($s['tipo_nombre']) ?></td>
                                <td class="small text-muted"><?= format_fecha($s['fecha_radicacion']) ?></td>
                                <td><span class="badge bg-<?= badge_estado($s['color_badge']) ?>"><?= e($s['estado_nombre']) ?></span></td>
                                <td class="text-end"><a href="<?= url('ciudadano/tramites/detalle/' . (int)$s['id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-eye me-1"></i> Ver</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
