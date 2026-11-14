<?php $s = $solicitud; ?>
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <div class="breadcrumb"><a href="<?= url('admin/tramites/solicitudes') ?>">Solicitudes</a> <span class="mx-1">›</span> Detalle</div>
        <h1><?= e($s['radicado']) ?></h1>
        <p><?= e($s['tipo_nombre']) ?> · Radicada el <?= format_fecha($s['fecha_radicacion'], true) ?></p>
    </div>
    <span class="badge bg-<?= badge_estado($s['color_badge']) ?> fs-6 px-3 py-2"><?= e($s['estado_nombre']) ?></span>
</div>

<?php if (!empty($s['motivo_rechazo'])): ?>
    <div class="alert alert-danger"><i class="bi bi-x-circle me-1"></i><strong>Motivo / observación:</strong> <?= e($s['motivo_rechazo']) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-vcard me-1"></i> Ciudadano solicitante</div>
            <div class="card-body">
                <ul class="detail-list">
                    <li><span class="dl-label">Nombre</span><span class="dl-value"><?= e(trim($s['ciudadano_nombres'] . ' ' . $s['ciudadano_apellidos'])) ?></span></li>
                    <li><span class="dl-label">Documento</span><span class="dl-value"><?= e($s['numero_documento']) ?></span></li>
                    <li><span class="dl-label">Correo</span><span class="dl-value"><?= e($s['ciudadano_email']) ?></span></li>
                    <li><span class="dl-label">Teléfono</span><span class="dl-value"><?= e($s['ciudadano_telefono'] ?? '—') ?></span></li>
                    <li><span class="dl-label">Dirección</span><span class="dl-value"><?= e($s['ciudadano_direccion'] ?? '—') ?></span></li>
                    <li><span class="dl-label">Barrio</span><span class="dl-value"><?= e($s['ciudadano_barrio'] ?? '—') ?></span></li>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-ui-checks me-1"></i> Datos suministrados</div>
            <div class="card-body">
                <?php if (empty($datos)): ?>
                    <p class="text-muted small mb-0">El ciudadano no diligenció campos adicionales.</p>
                <?php else: ?>
                    <ul class="detail-list">
                        <?php foreach ($datos as $k => $v): ?>
                            <li><span class="dl-label"><?= e(ucfirst(str_replace('_', ' ', (string)$k))) ?></span><span class="dl-value"><?= e((string)$v) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($archivos)): ?>
            <div class="card mb-4">
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

        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-1"></i> Historial completo</div>
            <div class="card-body">
                <ul class="timeline">
                    <?php foreach ($seguimiento as $seg): ?>
                        <li class="timeline-item">
                            <span class="timeline-dot <?= !$seg['es_publico'] ? 'amber' : (badge_estado($seg['color_badge']) === 'success' ? 'green' : '') ?>">
                                <i class="bi <?= !$seg['es_publico'] ? 'bi-lock' : 'bi-check2' ?>"></i>
                            </span>
                            <div class="timeline-title"><?= e($seg['estado_nuevo']) ?> <?= !$seg['es_publico'] ? '<span class="badge bg-warning text-dark ms-1">Interna</span>' : '' ?></div>
                            <div class="timeline-meta"><?= format_fecha($seg['created_at'], true) ?> · <?= e($seg['usuario_email'] ?? 'sistema') ?></div>
                            <div class="timeline-text <?= !$seg['es_publico'] ? 'internal' : '' ?>"><?= e($seg['observacion']) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-check me-1"></i> Asignar responsable</div>
            <div class="card-body">
                <form action="<?= url('admin/tramites/solicitudes/asignar/' . (int)$s['id']) ?>" method="post">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Funcionario <span class="text-danger">*</span></label>
                        <select class="form-select" name="funcionario_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($funcionarios as $f): ?>
                                <option value="<?= (int)$f['id'] ?>" <?= (int)($s['funcionario_id'] ?? 0) === (int)$f['id'] ? 'selected' : '' ?>>
                                    <?= e($f['nombres'] . ' ' . $f['apellidos']) ?> — <?= e($f['dependencia_nombre'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Asignar solicitud</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-arrow-repeat me-1"></i> Cambiar estado</div>
            <div class="card-body">
                <form action="<?= url('admin/tramites/solicitudes/estado/' . (int)$s['id']) ?>" method="post" data-confirm="¿Confirma el cambio de estado de esta solicitud?">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nuevo estado</label>
                        <select class="form-select" name="estado_tramite_id" required>
                            <?php foreach ($estados as $es): ?>
                                <option value="<?= (int)$es['id'] ?>" <?= (int)$s['estado_tramite_id'] === (int)$es['id'] ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observación / motivo</label>
                        <textarea class="form-control" name="observacion" rows="3" placeholder="Obligatoria para rechazo o solicitud de información adicional."></textarea>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Actualizar estado</button>
                </form>
            </div>
        </div>
    </div>
</div>
