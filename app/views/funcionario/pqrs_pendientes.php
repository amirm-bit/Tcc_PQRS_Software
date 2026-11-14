<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Solicitudes pendientes</h1>
        <p>PQRS que aún requieren gestión o respuesta.</p>
    </div>
    <a href="<?= url('funcionario/pqrs') ?>" class="btn btn-soft"><i class="bi bi-list me-1"></i> Todas mis PQRS</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($pqrs)): ?>
            <div class="empty-state"><i class="bi bi-check2-circle text-success" style="font-size:3rem;"></i>¡Excelente! No tiene solicitudes pendientes.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Radicado</th><th>Ciudadano</th><th>Tipo</th><th>Asunto</th><th>Fecha límite</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                    <tbody>
                        <?php foreach ($pqrs as $p): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($p['radicado']) ?></span></td>
                                <td class="small"><?= e(trim(($p['ciudadano_nombres'] ?? '') . ' ' . ($p['ciudadano_apellidos'] ?? ''))) ?></td>
                                <td class="small"><?= e($p['tipo_nombre']) ?></td>
                                <td class="small"><?= e(truncate($p['asunto'], 36)) ?></td>
                                <td class="small text-muted"><?= format_fecha($p['fecha_vencimiento']) ?></td>
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
