<?php
$esEdicion = $tipo !== null;
$action = $esEdicion ? url('admin/tramites/editar/' . (int)$tipo['id']) : url('admin/tramites/crear');
$val = function (string $key, $default = '') {
    if (isset($_SESSION['_old'][$key])) {
        return e($_SESSION['_old'][$key]);
    }
    return e((string)($default ?? ''));
};
?>
<div class="page-heading">
    <div class="breadcrumb"><a href="<?= url('admin/tramites') ?>">Tipos de trámites</a> <span class="mx-1">›</span> <?= $esEdicion ? 'Editar' : 'Crear' ?></div>
    <h1><?= $esEdicion ? 'Editar tipo de trámite' : 'Crear tipo de trámite' ?></h1>
    <p>Defina la información pública, los requisitos y los campos que diligenciará el ciudadano.</p>
</div>

<form method="post" action="<?= $action ?>" class="row g-4">
    <?= Csrf::field() ?>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-file-earmark-text me-1"></i> Información general</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="codigo" value="<?= $val('codigo', $tipo['codigo'] ?? '') ?>" maxlength="30" required placeholder="Ej: CERT_RESIDENCIA">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" value="<?= $val('nombre', $tipo['nombre'] ?? '') ?>" maxlength="150" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dependencia responsable</label>
                        <select class="form-select" name="dependencia_id">
                            <option value="">Sin dependencia</option>
                            <?php foreach ($dependencias as $d): ?>
                                <option value="<?= (int)$d['id'] ?>" <?= (string)$val('dependencia_id', $tipo['dependencia_id'] ?? '') === (string)$d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Días de término <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="dias_termino" min="1" max="90" value="<?= $val('dias_termino', $tipo['dias_termino'] ?? 10) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Disponibilidad</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="activo" id="activo" <?= !$esEdicion || (int)($tipo['activo'] ?? 1) === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label small" for="activo">Activo para el público</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descripcion" rows="3" required><?= $val('descripcion', $tipo['descripcion'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Requisitos <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="requisitos" rows="5" required placeholder="Un requisito por línea"><?= $val('requisitos', $tipo['requisitos'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instrucciones <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="instrucciones" rows="5" required placeholder="Paso a paso para el ciudadano"><?= $val('instrucciones', $tipo['instrucciones'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-input-cursor-text me-1"></i> Campos del formulario</div>
            <div class="card-body">
                <label class="form-label">Definición de campos</label>
                <textarea class="form-control font-monospace" name="campos_definicion" rows="6" placeholder="nombre_completo|Nombre completo|text|1&#10;fecha_nacimiento|Fecha de nacimiento|date|0"><?= $val('campos_definicion', $camposTexto) ?></textarea>
                <div class="form-text">
                    Una línea por campo con el formato <code>nombre|Etiqueta|tipo|obligatorio</code>.<br>
                    Tipos permitidos: <code>text</code>, <code>textarea</code>, <code>number</code>, <code>date</code>, <code>email</code>. Use <code>1</code> o <code>0</code> para obligatorio.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-1"></i> Ayuda</div>
            <div class="card-body">
                <p class="small text-muted">Los campos definidos se mostrarán automáticamente en el formulario de solicitud del ciudadano y se almacenarán como datos adicionales del trámite.</p>
                <div class="alert alert-light border small mb-3">
                    <div class="fw-semibold mb-1">Ejemplo</div>
                    <code>direccion|Dirección|text|1</code><br>
                    <code>estrato|Estrato|number|0</code>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> <?= $esEdicion ? 'Guardar cambios' : 'Crear tipo de trámite' ?></button>
                    <a href="<?= url('admin/tramites') ?>" class="btn btn-soft">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
