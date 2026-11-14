<?php $p = $pqrs; ?>
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <div class="breadcrumb"><a href="<?= url('ciudadano/pqrs') ?>">Mis PQRS</a> <span class="mx-1">›</span> Detalle</div>
        <h1><?= e($p['radicado']) ?></h1>
        <p><?= e($p['tipo_nombre']) ?> · Radicada el <?= format_fecha($p['fecha_radicacion'], true) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-<?= badge_estado($p['color_badge']) ?> fs-6 px-3 py-2"><?= e($p['estado_nombre']) ?></span>
        <button class="btn btn-soft btn-sm" data-copy="<?= e($p['radicado']) ?>"><i class="bi bi-clipboard"></i> Copiar</button>
    </div>
</div>

<div class="alert alert-success d-flex gap-2 align-items-start">
    <i class="bi bi-check-circle-fill mt-1"></i>
    <div>
        <strong>Solicitud radicada correctamente.</strong> Su número de radicado es <span class="radicado-pill"><?= e($p['radicado']) ?></span>.
        Conserve este número para futuras consultas.
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-file-text me-1"></i> Información de la solicitud</div>
            <div class="card-body">
                <h6 class="fw-bold mb-2"><?= e($p['asunto']) ?></h6>
                <p class="text-muted" style="white-space:pre-line;"><?= e($p['descripcion']) ?></p>
                <hr>
                <ul class="detail-list">
                    <li><span class="dl-label">Tipo de solicitud</span><span class="dl-value"><?= e($p['tipo_nombre']) ?></span></li>
                    <li><span class="dl-label">Dependencia</span><span class="dl-value"><?= e($p['dependencia_nombre'] ?? 'Por asignar') ?></span></li>
                    <li><span class="dl-label">Funcionario asignado</span><span class="dl-value"><?= e(trim(($p['funcionario_nombres'] ?? '') . ' ' . ($p['funcionario_apellidos'] ?? '')) ?: 'Por asignar') ?></span></li>
                    <li><span class="dl-label">Medio de contacto</span><span class="dl-value"><?= e(ucfirst($p['medio_contacto'])) ?></span></li>
                    <li><span class="dl-label">Correo</span><span class="dl-value"><?= e($p['email_contacto']) ?></span></li>
                    <li><span class="dl-label">Teléfono</span><span class="dl-value"><?= e($p['telefono_contacto'] ?? '—') ?></span></li>
                    <li><span class="dl-label">Fecha límite</span><span class="dl-value"><?= format_fecha($p['fecha_vencimiento']) ?></span></li>
                    <?php if (!empty($p['fecha_cierre'])): ?>
                        <li><span class="dl-label">Fecha de cierre</span><span class="dl-value"><?= format_fecha($p['fecha_cierre'], true) ?></span></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <?php if (!empty($archivos)): ?>
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-paperclip me-1"></i> Documentos adjuntos</div>
                <div class="list-group list-group-flush">
                    <?php foreach ($archivos as $a): ?>
                        <a href="<?= url('archivos/descargar/' . (int)$a['id']) ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-arrow-down text-primary"></i>
                            <span class="flex-grow-1 small"><?= e($a['nombre_original']) ?></span>
                            <span class="small text-muted"><?= UploadService::formatSize((int)$a['tamano_bytes']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><i class="bi bi-chat-left-text me-1"></i> Respuestas oficiales</div>
            <div class="card-body">
                <?php if (empty($respuestas)): ?>
                    <div class="empty-state py-3"><i class="bi bi-hourglass-split"></i>Aún no se ha emitido respuesta oficial para esta solicitud.</div>
                <?php else: foreach ($respuestas as $r): ?>
                    <div class="answer-box mb-3">
                        <div class="answer-meta mb-2 d-flex flex-wrap justify-content-between gap-2">
                            <span><i class="bi bi-person-badge me-1"></i><?= e(trim($r['funcionario_nombres'] . ' ' . $r['funcionario_apellidos'])) ?> · <?= e($r['cargo']) ?></span>
                            <span><?= format_fecha($r['fecha_respuesta'], true) ?></span>
                        </div>
                        <div style="white-space:pre-line;"><?= e($r['contenido']) ?></div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-1"></i> Línea de tiempo</div>
            <div class="card-body">
                <ul class="timeline">
                    <?php foreach ($seguimiento as $s): ?>
                        <li class="timeline-item">
                            <span class="timeline-dot <?= badge_estado($s['color_badge']) === 'success' ? 'green' : (badge_estado($s['color_badge']) === 'dark' ? 'slate' : (badge_estado($s['color_badge']) === 'warning' ? 'amber' : '')) ?>">
                                <i class="bi <?= estado_icon(strtolower($s['estado_nuevo'])) ?>"></i>
                            </span>
                            <div class="timeline-title"><?= e($s['estado_nuevo']) ?></div>
                            <div class="timeline-meta"><?= format_fecha($s['created_at'], true) ?></div>
                            <div class="timeline-text"><?= e($s['observacion']) ?></div>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($seguimiento)): ?>
                        <li class="text-muted small">Sin registros de seguimiento.</li>
                    <?php endif; ?>
                </ul>
                <div class="alert alert-light border small mb-0">
                    <i class="bi bi-eye-slash me-1"></i>
                    Las observaciones internas de los funcionarios no se muestran en esta vista.
                </div>
            </div>
        </div>
    </div>
</div>
