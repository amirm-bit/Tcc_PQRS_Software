<div class="page-heading">
    <h1>Configuración del sistema</h1>
    <p>Parámetros generales, información institucional y seguridad de la cuenta.</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-building me-1"></i> Información institucional</div>
            <div class="card-body">
                <ul class="detail-list">
                    <li><span class="dl-label">Entidad</span><span class="dl-value"><?= e($app['name']) ?></span></li>
                    <li><span class="dl-label">Municipio</span><span class="dl-value"><?= e($app['municipio']) ?>, <?= e($app['departamento']) ?></span></li>
                    <li><span class="dl-label">NIT</span><span class="dl-value"><?= e($app['nit']) ?></span></li>
                    <li><span class="dl-label">Dirección</span><span class="dl-value"><?= e($app['direccion']) ?></span></li>
                    <li><span class="dl-label">Teléfono</span><span class="dl-value"><?= e($app['telefono']) ?></span></li>
                    <li><span class="dl-label">Correo</span><span class="dl-value"><?= e($app['email']) ?></span></li>
                    <li><span class="dl-label">Horario</span><span class="dl-value"><?= e($app['horario']) ?></span></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-sliders me-1"></i> Parámetros técnicos</div>
            <div class="card-body">
                <ul class="detail-list">
                    <li><span class="dl-label">Entorno</span><span class="dl-value"><span class="badge bg-<?= $app['env'] === 'local' ? 'warning text-dark' : 'success' ?>"><?= e($app['env']) ?></span></span></li>
                    <li><span class="dl-label">URL base</span><span class="dl-value"><code><?= e($app['base_url']) ?></code></span></li>
                    <li><span class="dl-label">Zona horaria</span><span class="dl-value"><?= e($app['timezone']) ?></span></li>
                    <li><span class="dl-label">Sesión</span><span class="dl-value"><?= e($app['session_name']) ?> · <?= (int)($app['session_lifetime'] / 60) ?> min</span></li>
                    <li><span class="dl-label">Adjuntos</span><span class="dl-value">Máx. <?= UploadService::formatSize((int)$app['uploads']['max_size']) ?> · <?= e(implode(', ', $app['uploads']['allowed_types'])) ?></span></li>
                    <li><span class="dl-label">Versión PHP</span><span class="dl-value"><?= e(PHP_VERSION) ?></span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-gear me-1"></i> Mi cuenta</div>
            <div class="card-body">
                <ul class="detail-list">
                    <li><span class="dl-label">Usuario</span><span class="dl-value"><?= e($usuario['email']) ?></span></li>
                    <li><span class="dl-label">Rol</span><span class="dl-value"><?= e(role_label($usuario['rol'] ?? 'admin')) ?></span></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-shield-lock me-1"></i> Cambiar contraseña</div>
            <div class="card-body">
                <form action="<?= url('admin/configuracion/password') ?>" method="post">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Contraseña actual <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_actual" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" minlength="8" required>
                        <div class="form-text">Mínimo 8 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar nueva contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password_confirm" minlength="8" required>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Actualizar contraseña</button>
                </form>
            </div>
        </div>
    </div>
</div>
