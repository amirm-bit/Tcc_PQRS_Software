<?php $nombre = trim(($ciudadano['nombres'] ?? '') . ' ' . ($ciudadano['apellidos'] ?? '')); ?>
<div class="page-heading">
    <h1>Radicar nueva PQRS</h1>
    <p>Petición, queja, reclamo o sugerencia dirigida a la Alcaldía Municipal de Lorica.</p>
</div>

<div class="alert alert-info d-flex gap-2">
    <i class="bi bi-info-circle-fill mt-1"></i>
    <div class="small">
        Los campos marcados con <span class="text-danger">*</span> son obligatorios. Su identidad se toma automáticamente de su cuenta y no puede ser modificada desde este formulario.
    </div>
</div>

<form action="<?= url('ciudadano/pqrs/nueva') ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= Csrf::field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-person-badge me-1"></i> Datos del solicitante</div>
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
                    <div class="form-text mt-2">Si requiere actualizar sus datos, diríjase a <a href="<?= url('ciudadano/perfil') ?>">Mi perfil</a>.</div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-pencil-square me-1"></i> Detalle de la solicitud</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tipo de solicitud <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <?php foreach ($tipos as $i => $t): ?>
                                <div class="col-sm-6">
                                    <input type="radio" class="btn-check" name="tipo_pqrs_id" id="tipo<?= (int)$t['id'] ?>" value="<?= (int)$t['id'] ?>" <?= old('tipo_pqrs_id') == $t['id'] ? 'checked' : ($i === 0 && !old('tipo_pqrs_id') ? 'checked' : '') ?> required>
                                    <label class="btn btn-outline-primary w-100 text-start p-3" for="tipo<?= (int)$t['id'] ?>">
                                        <span class="fw-bold d-block"><?= e($t['nombre']) ?></span>
                                        <span class="small text-muted" style="white-space:normal;"><?= e(truncate($t['descripcion'], 70)) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="asunto">Asunto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="asunto" name="asunto" value="<?= old('asunto') ?>" maxlength="255" placeholder="Resumen breve de su solicitud" required>
                        <div class="form-text">Entre 5 y 255 caracteres.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="descripcion">Descripción detallada <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="6" minlength="20" placeholder="Describa con claridad los hechos, el lugar y su petición..." required><?= old('descripcion') ?></textarea>
                        <div class="form-text">Mínimo 20 caracteres.</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label" for="dependencia_id">Dependencia relacionada</label>
                        <select class="form-select" id="dependencia_id" name="dependencia_id">
                            <option value="">No estoy seguro / Ventanilla única</option>
                            <?php foreach ($dependencias as $d): ?>
                                <option value="<?= (int)$d['id'] ?>" <?= old('dependencia_id') == $d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="bi bi-paperclip me-1"></i> Documentos adjuntos (opcional)</div>
                <div class="card-body">
                    <input type="file" class="form-control" name="adjuntos[]" multiple data-file-label="archivosSeleccionados" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    <div class="form-text mt-1">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX. Tamaño máximo: 10 MB por archivo.</div>
                    <div class="small text-muted mt-1" id="archivosSeleccionados">Ningún archivo seleccionado</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-telephone me-1"></i> Datos de contacto</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Medio de contacto preferido <span class="text-danger">*</span></label>
                        <select class="form-select" name="medio_contacto" required>
                            <option value="correo" <?= old('medio_contacto') === 'correo' ? 'selected' : '' ?>>Correo electrónico</option>
                            <option value="telefono" <?= old('medio_contacto') === 'telefono' ? 'selected' : '' ?>>Teléfono</option>
                            <option value="direccion" <?= old('medio_contacto') === 'direccion' ? 'selected' : '' ?>>Dirección física</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email_contacto" value="<?= old('email_contacto', $usuario['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="telefono_contacto" value="<?= old('telefono_contacto', $ciudadano['telefono'] ?? '') ?>" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="bi bi-shield-check text-success me-1"></i> Antes de radicar</h6>
                    <ul class="small text-muted ps-3 mb-3">
                        <li>Verifique que la información sea veraz y completa.</li>
                        <li>Recibirá un número único de radicado.</li>
                        <li>Podrá seguir el estado desde su panel.</li>
                    </ul>
                    <button class="btn btn-primary w-100 btn-lg" data-confirm="¿Confirma la radicación de esta PQRS?">
                        <i class="bi bi-send me-1"></i> Radicar solicitud
                    </button>
                    <a href="<?= url('ciudadano/pqrs') ?>" class="btn btn-soft w-100 mt-2">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
