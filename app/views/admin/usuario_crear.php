<div class="page-heading">
    <div class="breadcrumb"><a href="<?= url('admin/usuarios') ?>">Usuarios</a> <span class="mx-1">›</span> Crear</div>
    <h1>Crear nuevo usuario</h1>
    <p>Registre una cuenta y complete los datos según el rol seleccionado.</p>
</div>

<form method="post" action="<?= url('admin/usuarios/crear') ?>" class="row g-4">
    <?= Csrf::field() ?>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-plus me-1"></i> Datos de la cuenta</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Rol <span class="text-danger">*</span></label>
                    <select class="form-select" name="rol" id="rolSelect" required>
                        <option value="">Seleccione...</option>
                        <option value="admin" <?= old('rol') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <option value="funcionario" <?= old('rol') === 'funcionario' ? 'selected' : '' ?>>Funcionario</option>
                        <option value="ciudadano" <?= old('rol') === 'ciudadano' ? 'selected' : '' ?>>Ciudadano</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="<?= old('telefono') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" minlength="8" required>
                        <div class="form-text">Mínimo 8 caracteres.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirm" minlength="8" required>
                    </div>
                </div>

                <div data-role="funcionario ciudadano" class="row g-3 mt-1 d-none">
                    <div class="col-md-6">
                        <label class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombres" value="<?= old('nombres') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="apellidos" value="<?= old('apellidos') ?>">
                    </div>
                </div>

                <div data-role="funcionario" class="row g-3 mt-1 d-none">
                    <div class="col-md-6">
                        <label class="form-label">Dependencia <span class="text-danger">*</span></label>
                        <select class="form-select" name="dependencia_id">
                            <option value="">Seleccione...</option>
                            <?php foreach ($dependencias as $d): ?>
                                <option value="<?= (int)$d['id'] ?>" <?= (string)old('dependencia_id') === (string)$d['id'] ? 'selected' : '' ?>><?= e($d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cargo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="cargo" value="<?= old('cargo') ?>" placeholder="Ej: Profesional de atención al ciudadano">
                    </div>
                </div>

                <div data-role="ciudadano" class="row g-3 mt-1 d-none">
                    <div class="col-md-4">
                        <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                        <select class="form-select" name="tipo_documento">
                            <?php foreach (['CC','TI','CE','PASAPORTE','NIT'] as $td): ?>
                                <option value="<?= $td ?>" <?= old('tipo_documento', 'CC') === $td ? 'selected' : '' ?>><?= $td ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Número de documento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="numero_documento" value="<?= old('numero_documento') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="direccion" value="<?= old('direccion') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Barrio <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="barrio" value="<?= old('barrio') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-1"></i> Resumen</div>
            <div class="card-body">
                <p class="small text-muted">El rol determina los permisos y el panel al que accederá el usuario.</p>
                <ul class="small text-muted ps-3">
                    <li><strong>Administrador:</strong> control total del sistema.</li>
                    <li><strong>Funcionario:</strong> gestiona PQRS y trámites asignados.</li>
                    <li><strong>Ciudadano:</strong> radica y consulta solicitudes.</li>
                </ul>
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Crear usuario</button>
                    <a href="<?= url('admin/usuarios') ?>" class="btn btn-soft">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function () {
    var select = document.getElementById('rolSelect');
    if (!select) return;
    function sync() {
        var rol = select.value;
        document.querySelectorAll('[data-role]').forEach(function (el) {
            var roles = el.getAttribute('data-role').split(' ');
            var visible = roles.indexOf(rol) !== -1;
            el.classList.toggle('d-none', !visible);
            el.querySelectorAll('input, select, textarea').forEach(function (input) {
                input.disabled = !visible;
            });
        });
    }
    select.addEventListener('change', sync);
    sync();
})();
</script>
