<?php $p = $pqrs; ?>
<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <div class="breadcrumb"><a href="<?= url('admin/pqrs') ?>">Gestión de PQRS</a> <span class="mx-1">›</span> Detalle</div>
        <h1><?= e($p['radicado']) ?></h1>
        <p><?= e($p['tipo_nombre']) ?> · Radicada el <?= format_fecha($p['fecha_radicacion'], true) ?></p>
    </div>
    <span class="badge bg-<?= badge_estado($p['color_badge']) ?> fs-6 px-3 py-2"><?= e($p['estado_nombre']) ?></span>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-vcard me-1"></i> Ciudadano solicitante</div>
            <div class="card-body">
                <ul class="detail-list">
                    <li><span class="dl-label">Nombre</span><span class="dl-value"><?= e(trim($p['ciudadano_nombres'] . ' ' . $p['ciudadano_apellidos'])) ?></span></li>
                    <li><span class="dl-label">Documento</span><span class="dl-value"><?= e($p['numero_documento']) ?></span></li>
                    <li><span class="dl-label">Correo</span><span class="dl-value"><?= e($p['ciudadano_email']) ?></span></li>
                    <li><span class="dl-label">Teléfono</span><span class="dl-value"><?= e($p['ciudadano_telefono'] ?? $p['telefono_contacto'] ?? '—') ?></span></li>
                    <li><span class="dl-label">Dirección</span><span class="dl-value"><?= e($p['ciudadano_direccion'] ?? '—') ?></span></li>
                    <li><span class="dl-label">Barrio</span><span class="dl-value"><?= e($p['ciudadano_barrio'] ?? '—') ?></span></li>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-file-text me-1"></i> Contenido de la solicitud</div>
            <div class="card-body">
                <h6 class="fw-bold mb-2"><?= e($p['asunto']) ?></h6>
                <p class="text-muted mb-3" style="white-space:pre-line;"><?= e($p['descripcion']) ?></p>
                <ul class="detail-list">
                    <li><span class="dl-label">Dependencia</span><span class="dl-value"><?= e($p['dependencia_nombre'] ?? 'Sin asignar') ?></span></li>
                    <li><span class="dl-label">Funcionario</span><span class="dl-value"><?= e(trim(($p['funcionario_nombres'] ?? '') . ' ' . ($p['funcionario_apellidos'] ?? '')) ?: 'Sin asignar') ?></span></li>
                    <li><span class="dl-label">Medio de contacto</span><span class="dl-value"><?= e(ucfirst($p['medio_contacto'])) ?></span></li>
                    <li><span class="dl-label">Correo de contacto</span><span class="dl-value"><?= e($p['email_contacto']) ?></span></li>
                    <li><span class="dl-label">Fecha límite</span><span class="dl-value"><?= format_fecha($p['fecha_vencimiento']) ?></span></li>
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

        <?php if (!empty($respuestas)): ?>
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-chat-left-text me-1"></i> Respuestas emitidas</div>
                <div class="card-body">
                    <?php foreach ($respuestas as $r): ?>
                        <div class="answer-box mb-3">
                            <div class="answer-meta mb-1"><?= format_fecha($r['fecha_respuesta'], true) ?> · <?= e(trim($r['funcionario_nombres'] . ' ' . $r['funcionario_apellidos'])) ?></div>
                            <div class="small" style="white-space:pre-line;"><?= e($r['contenido']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-1"></i> Historial completo</div>
            <div class="card-body">
                <ul class="timeline">
                    <?php foreach ($seguimiento as $s): ?>
                        <li class="timeline-item">
                            <span class="timeline-dot <?= !$s['es_publico'] ? 'amber' : (badge_estado($s['color_badge']) === 'success' ? 'green' : '') ?>">
                                <i class="bi <?= !$s['es_publico'] ? 'bi-lock' : 'bi-check2' ?>"></i>
                            </span>
                            <div class="timeline-title"><?= e($s['estado_nuevo']) ?> <?= !$s['es_publico'] ? '<span class="badge bg-warning text-dark ms-1">Interna</span>' : '' ?></div>
                            <div class="timeline-meta"><?= format_fecha($s['created_at'], true) ?> · <?= e($s['usuario_email'] ?? 'sistema') ?></div>
                            <div class="timeline-text <?= !$s['es_publico'] ? 'internal' : '' ?>"><?= e($s['observacion']) ?></div>
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
                <form action="<?= url('admin/pqrs/asignar/' . (int)$p['id']) ?>" method="post">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Funcionario <span class="text-danger">*</span></label>
                        <select class="form-select" name="funcionario_id" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($funcionarios as $f): ?>
                                <option value="<?= (int)$f['id'] ?>" <?= (int)($p['funcionario_id'] ?? 0) === (int)$f['id'] ? 'selected' : '' ?>>
                                    <?= e($f['nombres'] . ' ' . $f['apellidos']) ?> — <?= e($f['dependencia_nombre'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dependencia</label>
                        <select class="form-select" name="dependencia_id">
                            <option value="">Mantener / asignar según funcionario</option>
                            <?php foreach ($dependencias as $d): ?>
                                <option value="<?= (int)$d['id'] ?>" <?= (int)($p['dependencia_id'] ?? 0) === (int)$d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Asignar solicitud</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-arrow-repeat me-1"></i> Cambiar estado</div>
            <div class="card-body">
                <form action="<?= url('admin/pqrs/estado/' . (int)$p['id']) ?>" method="post" data-confirm="¿Confirma el cambio de estado de esta PQRS?">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nuevo estado</label>
                        <select class="form-select" name="estado_pqrs_id" required>
                            <?php foreach ($estados as $es): ?>
                                <option value="<?= (int)$es['id'] ?>" <?= (int)$p['estado_pqrs_id'] === (int)$es['id'] ? 'selected' : '' ?>><?= e($es['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observación <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="observacion" rows="3" required></textarea>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Actualizar estado</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-journal-plus me-1"></i> Agregar seguimiento</div>
            <div class="card-body">
                <form action="<?= url('admin/pqrs/seguimiento/' . (int)$p['id']) ?>" method="post">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <textarea class="form-control" name="observacion" rows="3" required placeholder="Nota de seguimiento..."></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="es_publico" id="esPublicoAdmin" checked>
                        <label class="form-check-label small" for="esPublicoAdmin">Visible para el ciudadano</label>
                    </div>
                    <button class="btn btn-soft w-100"><i class="bi bi-plus-lg me-1"></i> Agregar al historial</button>
                </form>
            </div>
        </div>
    </div>
</div>
