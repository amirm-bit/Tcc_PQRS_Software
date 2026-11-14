<?php $nombre = trim(($ciudadano['nombres'] ?? '') . ' ' . ($ciudadano['apellidos'] ?? '')); ?>
<div class="page-heading">
    <div class="breadcrumb"><a href="<?= url('ciudadano/tramites') ?>">Trámites disponibles</a> <span class="mx-1">›</span> Solicitud</div>
    <h1><?= e($tipo['nombre']) ?></h1>
    <p>Solicitud de demostración académica · Término estimado <?= (int)$tipo['dias_termino'] ?> días hábiles</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <form action="<?= url('ciudadano/tramites/solicitar/' . (int)$tipo['id']) ?>" method="post" enctype="multipart/form-data" novalidate>
            <?= Csrf::field() ?>
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-person-badge me-1"></i> Solicitante</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Ciudadano</label>
                            <input type="text" class="form-control" value="<?= e($nombre) ?>" disabled>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Documento</label>
                            <input type="text" class="form-control" value="<?= e(($ciudadano['tipo_documento'] ?? '') . ' ' . ($ciudadano['numero_documento'] ?? '')) ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-ui-checks me-1"></i> Formulario del trámite</div>
                <div class="card-body">
                    <?php if (empty($campos)): ?>
                        <p class="text-muted small mb-0">Este trámite no requiere datos adicionales.</p>
                    <?php else: foreach ($campos as $campo): ?>
                        <div class="mb-3">
                            <label class="form-label" for="campo_<?= e($campo['name']) ?>">
                                <?= e($campo['label']) ?> <?= !empty($campo['required']) ? '<span class="text-danger">*</span>' : '' ?>
                            </label>
                            <?php if (($campo['type'] ?? 'text') === 'textarea'): ?>
                                <textarea class="form-control" id="campo_<?= e($campo['name']) ?>" name="campos[<?= e($campo['name']) ?>]" rows="3" <?= !empty($campo['required']) ? 'required' : '' ?>><?= old('campos_' . $campo['name']) ?></textarea>
                            <?php else: ?>
                                <input type="<?= in_array($campo['type'] ?? 'text', ['number','date','email'], true) ? e($campo['type']) : 'text' ?>" class="form-control" id="campo_<?= e($campo['name']) ?>" name="campos[<?= e($campo['name']) ?>]" <?= !empty($campo['required']) ? 'required' : '' ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="bi bi-paperclip me-1"></i> Documentos de soporte (opcional)</div>
                <div class="card-body">
                    <input type="file" class="form-control" name="adjuntos[]" multiple data-file-label="archivosSeleccionados" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    <div class="form-text mt-1">PDF, JPG, PNG, DOC o DOCX. Máximo 10 MB por archivo.</div>
                    <div class="small text-muted mt-1" id="archivosSeleccionados">Ningún archivo seleccionado</div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-success btn-lg" data-confirm="¿Confirma el envío de esta solicitud de trámite?">
                    <i class="bi bi-send me-1"></i> Enviar solicitud
                </button>
                <a href="<?= url('ciudadano/tramites') ?>" class="btn btn-soft btn-lg">Cancelar</a>
            </div>
        </form>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-info-circle me-1"></i> Descripción</div>
            <div class="card-body small text-muted"><?= e($tipo['descripcion']) ?></div>
        </div>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-list-check me-1"></i> Requisitos</div>
            <div class="card-body small" style="white-space:pre-line;"><?= e($tipo['requisitos']) ?></div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-signpost-split me-1"></i> Instrucciones</div>
            <div class="card-body small" style="white-space:pre-line;"><?= e($tipo['instrucciones']) ?></div>
        </div>
    </div>
</div>
