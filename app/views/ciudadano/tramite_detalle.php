<?php $s = $solicitud; ?>
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <div class="breadcrumb"><a href="<?= url('ciudadano/tramites/mis-solicitudes') ?>">Mis trámites</a> <span class="mx-1">›</span> Detalle</div>
        <h1><?= e($s['radicado']) ?></h1>
        <p><?= e($s['tipo_nombre']) ?> · Radicado el <?= format_fecha($s['fecha_radicacion'], true) ?></p>
    </div>
    <span class="badge bg-<?= badge_estado($s['color_badge']) ?> fs-6 px-3 py-2"><?= e($s['estado_nombre']) ?></span>
</div>

<?php if (!empty($s['motivo_rechazo'])): ?>
    <div class="alert alert-danger d-flex gap-2">
        <i class="bi bi-x-circle-fill mt-1"></i>
        <div><strong>Motivo de rechazo / observación:</strong> <?= e($s['motivo_rechazo']) ?></div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-file-earmark-text me-1"></i> Datos de la solicitud</div>
            <div class="card-body">
                <ul class="detail-list mb-3">
                    <li><span class="dl-label">Radicado</span><span class="dl-value"><span class="radicado-pill"><?= e($s['radicado']) ?></span></span></li>
                    <li><span class="dl-label">Trámite</span><span class="dl-value"><?= e($s['tipo_nombre']) ?></span></li>
                    <li><span class="dl-label">Dependencia</span><span class="dl-value"><?= e($s['dependencia_nombre'] ?? 'Ventanilla única') ?></span></li>
                    <li><span class="dl-label">Funcionario</span><span class="dl-value"><?= e(trim(($s['funcionario_nombres'] ?? '') . ' ' . ($s['funcionario_apellidos'] ?? '')) ?: 'Por asignar') ?></span></li>
                    <li><span class="dl-label">Fecha de radicación</span><span class="dl-value"><?= format_fecha($s['fecha_radicacion'], true) ?></span></li>
                    <?php if (!empty($s['fecha_cierre'])): ?>
                        <li><span class="dl-label">Fecha de cierre</span><span class="dl-value"><?= format_fecha($s['fecha_cierre'], true) ?></span></li>
                    <?php endif; ?>
                </ul>
                <?php if (!empty($datos)): ?>
                    <h6 class="fw-bold small text-uppercase text-muted mt-3">Información suministrada</h6>
                    <ul class="detail-list">
                        <?php foreach ($datos as $k => $v): ?>
                            <li><span class="dl-label"><?= e(ucfirst(str_replace('_', ' ', (string)$k))) ?></span><span class="dl-value"><?= e((string)$v) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($archivos)): ?>
            <div class="card">
                <div class="card-header"><i class="bi bi-paperclip me-1"></i> Documentos adjuntos</div>
                <div class="list-group list-group-flush">
                    <?php foreach ($archivos as $a): ?>
                        <a href="<?= url('archivos/descargar/' . (int)$a['id']) ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-arrow-down text-success"></i>
                            <span class="flex-grow-1 small"><?= e($a['nombre_original']) ?></span>
                            <span class="small text-muted"><?= UploadService::formatSize((int)$a['tamano_bytes']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-1"></i> Línea de tiempo</div>
            <div class="card-body">
                <ul class="timeline">
                    <?php foreach ($seguimiento as $seg): ?>
                        <li class="timeline-item">
                            <span class="timeline-dot <?= badge_estado($seg['color_badge']) === 'success' ? 'green' : (badge_estado($seg['color_badge']) === 'dark' ? 'slate' : (badge_estado($seg['color_badge']) === 'danger' ? 'red' : (badge_estado($seg['color_badge']) === 'warning' ? 'amber' : ''))) ?>">
                                <i class="bi <?= estado_icon('en_revision') ?>"></i>
                            </span>
                            <div class="timeline-title"><?= e($seg['estado_nuevo']) ?></div>
                            <div class="timeline-meta"><?= format_fecha($seg['created_at'], true) ?></div>
                            <div class="timeline-text"><?= e($seg['observacion']) ?></div>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($seguimiento)): ?>
                        <li class="text-muted small">Sin registros de seguimiento.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
