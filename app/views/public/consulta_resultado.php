<?php
$app = config();
$esTramite = $tipo === 'tramite';
$r = $registro;
?>
<section class="py-4" style="background:linear-gradient(135deg,var(--azul-oscuro),var(--azul));">
    <div class="container py-3">
        <a href="<?= url('consultar-radicado') ?>" class="text-white-50 small d-inline-flex align-items-center gap-1 mb-2 text-decoration-none"><i class="bi bi-arrow-left"></i> Nueva consulta</a>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="text-white">
                <div class="small text-white-50 text-uppercase" style="letter-spacing:.1em;"><?= $esTramite ? 'Solicitud de trámite' : 'PQRS' ?></div>
                <h1 class="h3 text-white mb-1"><?= e($r['radicado']) ?></h1>
                <div class="text-white-50 small"><?= e($r['tipo_nombre']) ?> · Radicado el <?= format_fecha($r['fecha_radicacion'], true) ?></div>
            </div>
            <span class="badge bg-<?= badge_estado($r['color_badge']) ?> fs-6 px-3 py-2"><?= e($r['estado_nombre']) ?></span>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header"><i class="bi bi-clock-history me-1"></i> Línea de tiempo del seguimiento</div>
                    <div class="card-body">
                        <?php if (empty($seguimiento)): ?>
                            <div class="empty-state py-4"><i class="bi bi-clock"></i>No hay registros públicos de seguimiento todavía.</div>
                        <?php else: ?>
                            <ul class="timeline">
                                <?php foreach ($seguimiento as $s): ?>
                                    <li class="timeline-item">
                                        <span class="timeline-dot <?= badge_estado($s['color_badge']) === 'success' ? 'green' : (badge_estado($s['color_badge']) === 'dark' ? 'slate' : (badge_estado($s['color_badge']) === 'warning' ? 'amber' : '')) ?>">
                                            <i class="bi <?= estado_icon($esTramite ? 'en_revision' : 'radicada') ?>"></i>
                                        </span>
                                        <div class="timeline-title"><?= e($s['estado_nuevo']) ?></div>
                                        <div class="timeline-meta"><?= format_fecha($s['created_at'], true) ?></div>
                                        <div class="timeline-text"><?= e($s['observacion']) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header"><i class="bi bi-info-circle me-1"></i> Datos de la solicitud</div>
                    <div class="card-body">
                        <ul class="detail-list">
                            <li><span class="dl-label">Radicado</span><span class="dl-value"><span class="radicado-pill"><?= e($r['radicado']) ?></span></span></li>
                            <li><span class="dl-label">Tipo</span><span class="dl-value"><?= e($r['tipo_nombre']) ?></span></li>
                            <li><span class="dl-label">Estado</span><span class="dl-value"><span class="badge bg-<?= badge_estado($r['color_badge']) ?>"><?= e($r['estado_nombre']) ?></span></span></li>
                            <li><span class="dl-label">Fecha de radicación</span><span class="dl-value"><?= format_fecha($r['fecha_radicacion'], true) ?></span></li>
                            <?php if (!$esTramite): ?>
                                <li><span class="dl-label">Dependencia</span><span class="dl-value"><?= e($r['dependencia_nombre'] ?? 'Por asignar') ?></span></li>
                                <li><span class="dl-label">Funcionario</span><span class="dl-value"><?= e(trim(($r['funcionario_nombres'] ?? '') . ' ' . ($r['funcionario_apellidos'] ?? '')) ?: 'Por asignar') ?></span></li>
                            <?php else: ?>
                                <li><span class="dl-label">Tipo de trámite</span><span class="dl-value"><?= e($r['tipo_nombre']) ?></span></li>
                                <li><span class="dl-label">Funcionario</span><span class="dl-value"><?= e(trim(($r['funcionario_nombres'] ?? '') . ' ' . ($r['funcionario_apellidos'] ?? '')) ?: 'Por asignar') ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($r['fecha_cierre'])): ?>
                                <li><span class="dl-label">Fecha de cierre</span><span class="dl-value"><?= format_fecha($r['fecha_cierre'], true) ?></span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <?php if (!$esTramite): ?>
                    <div class="card">
                        <div class="card-header"><i class="bi bi-chat-left-text me-1"></i> Asunto</div>
                        <div class="card-body">
                            <div class="fw-bold mb-2"><?= e($r['asunto']) ?></div>
                            <p class="small text-muted mb-0"><?= nl2br(e($r['descripcion'])) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header"><i class="bi bi-file-text me-1"></i> Descripción del trámite</div>
                        <div class="card-body small text-muted"><?= e($r['tipo_descripcion']) ?></div>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info small mt-4 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Para consultar respuestas oficiales y descargar documentos, inicie sesión con la cuenta del ciudadano que radicó la solicitud.
                </div>
            </div>
        </div>
    </div>
</section>
