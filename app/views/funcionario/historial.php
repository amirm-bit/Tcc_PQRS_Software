<div class="page-heading">
    <h1>Historial de actividades</h1>
    <p>Registro de sus acciones y seguimientos realizados en el sistema.</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-check me-1"></i> Auditoría de mis acciones</div>
            <div class="card-body p-0">
                <?php if (empty($auditoria)): ?>
                    <div class="empty-state"><i class="bi bi-clock-history"></i>Sin registros de auditoría.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Acción</th><th>Tabla</th><th>Detalle</th><th>Fecha</th></tr></thead>
                            <tbody>
                                <?php foreach ($auditoria as $a): ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?= e($a['accion']) ?></span></td>
                                        <td class="small text-muted"><?= e($a['tabla_afectada']) ?></td>
                                        <td class="small"><?= e(truncate((string)$a['detalles'], 60)) ?></td>
                                        <td class="small text-muted"><?= format_fecha($a['created_at'], true) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-journal-text me-1"></i> Seguimientos registrados</div>
            <div class="card-body p-0">
                <?php if (empty($seguimientos)): ?>
                    <div class="empty-state"><i class="bi bi-journal"></i>Sin seguimientos.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($seguimientos as $s): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between gap-2">
                                    <span class="radicado-pill"><?= e($s['radicado']) ?></span>
                                    <span class="small text-muted"><?= format_fecha($s['created_at'], true) ?></span>
                                </div>
                                <div class="small mt-1"><?= e(truncate($s['observacion'], 110)) ?></div>
                                <?php if (!$s['es_publico']): ?><span class="badge bg-warning text-dark mt-1">Nota interna</span><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
