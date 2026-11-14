<?php
$rol = $usuario['rol'];
$val = function (string $key, $default = '') {
    if (isset($_SESSION['_old'][$key])) {
        return e($_SESSION['_old'][$key]);
    }
    return e((string)($default ?? ''));
};
?>
<div class="page-heading">
    <div class="breadcrumb"><a href="<?= url('admin/usuarios') ?>">Usuarios</a> <span class="mx-1">›</span> Editar</div>
    <h1>Editar usuario</h1>
    <p><?= e($usuario['email']) ?> · <span class="badge bg-<?= $rol === 'admin' ? 'dark' : ($rol === 'funcionario' ? 'primary' : 'success') ?>"><?= e(role_label($rol)) ?></span></p>
</div>

<form method="post" action="<?= url('admin/usuarios/editar/' . (int)$usuario['id']) ?>" class="row g-4">
    <?= Csrf::field() ?>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-envelope me-1"></i> Datos de acceso</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= $val('email', $usuario['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="<?= $val('telefono', $perfil['telefono'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" name="password" minlength="8" placeholder="Dejar en blanco para conservar">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" class="form-control" name="password_confirm" minlength="8">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-person-vcard me-1"></i> Datos del perfil</div>
            <div class="card-body">
                <?php if ($rol === 'admin'): ?>
                    <p class="text-muted small mb-0">La cuenta de administrador no requiere datos de perfil adicionales.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombres" value="<?= $val('nombres', $perfil['nombres'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellidos" value="<?= $val('apellidos', $perfil['apellidos'] ?? '') ?>" required>
                        </div>

                        <?php if ($rol === 'funcionario'): ?>
                            <div class="col-md-6">
                                <label class="form-label">Dependencia <span class="text-danger">*</span></label>
                                <select class="form-select" name="dependencia_id" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($dependencias as $d): ?>
                                        <option value="<?= (int)$d['id'] ?>" <?= (string)$val('dependencia_id', $perfil['dependencia_id'] ?? '') === (string)$d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cargo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="cargo" value="<?= $val('cargo', $perfil['cargo'] ?? '') ?>" required>
                            </div>
                        <?php endif; ?>

                        <?php if ($rol === 'ciudadano'): ?>
                            <div class="col-md-4">
                                <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                                <select class="form-select" name="tipo_documento" required>
                                    <?php foreach (['CC','TI','CE','PASAPORTE','NIT'] as $td): ?>
                                        <option value="<?= $td ?>" <?= $val('tipo_documento', $perfil['tipo_documento'] ?? 'CC') === $td ? 'selected' : '' ?>><?= $td ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Número de documento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="numero_documento" value="<?= $val('numero_documento', $perfil['numero_documento'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dirección <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="direccion" value="<?= $val('direccion', $perfil['direccion'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Barrio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="barrio" value="<?= $val('barrio', $perfil['barrio'] ?? '') ?>" required>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-lock me-1"></i> Información</div>
            <div class="card-body">
                <ul class="detail-list small">
                    <li><span class="dl-label">Rol</span><span class="dl-value"><?= e(role_label($rol)) ?></span></li>
                    <li><span class="dl-label">Estado</span><span class="dl-value"><?= (int)$usuario['activo'] === 1 ? 'Activo' : 'Inactivo' ?></span></li>
                    <li><span class="dl-label">Registro</span><span class="dl-value"><?= format_fecha($usuario['created_at']) ?></span></li>
                    <li><span class="dl-label">Último acceso</span><span class="dl-value"><?= $usuario['ultimo_acceso'] ? format_fecha($usuario['ultimo_acceso'], true) : 'Nunca' ?></span></li>
                </ul>
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Guardar cambios</button>
                    <a href="<?= url('admin/usuarios') ?>" class="btn btn-soft">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
