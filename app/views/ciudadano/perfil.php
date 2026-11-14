<div class="page-heading">
    <h1>Mi perfil</h1>
    <p>Actualice sus datos personales y de contacto. El correo es su usuario de acceso.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-gear me-1"></i> Datos personales</div>
            <div class="card-body">
                <form action="<?= url('ciudadano/perfil') ?>" method="post" novalidate>
                    <?= Csrf::field() ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nombres</label>
                            <input type="text" class="form-control" name="nombres" value="<?= old('nombres', $ciudadano['nombres'] ?? '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-control" name="apellidos" value="<?= old('apellidos', $ciudadano['apellidos'] ?? '') ?>" required>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label">Tipo de documento</label>
                            <select class="form-select" name="tipo_documento">
                                <?php foreach (['CC'=>'Cédula de ciudadanía','TI'=>'Tarjeta de identidad','CE'=>'Cédula de extranjería','PASAPORTE'=>'Pasaporte'] as $val=>$lab): ?>
                                    <option value="<?= $val ?>" <?= ($ciudadano['tipo_documento'] ?? 'CC') === $val ? 'selected' : '' ?>><?= $lab ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-7">
                            <label class="form-label">Número de documento</label>
                            <input type="text" class="form-control" name="numero_documento" value="<?= old('numero_documento', $ciudadano['numero_documento'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="email" value="<?= old('email', $usuario['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" value="<?= old('telefono', $ciudadano['telefono'] ?? '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Barrio o vereda</label>
                            <input type="text" class="form-control" name="barrio" value="<?= old('barrio', $ciudadano['barrio'] ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion" value="<?= old('direccion', $ciudadano['direccion'] ?? '') ?>" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-key me-1"></i> Cambio de contraseña (opcional)</h6>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" name="password" autocomplete="new-password">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" name="password_confirm" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar cambios</button>
                        <a href="<?= url('ciudadano/dashboard') ?>" class="btn btn-soft">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-check me-1"></i> Seguridad de la cuenta</div>
            <div class="card-body small text-muted">
                <p><i class="bi bi-check2-circle text-success me-1"></i> Su contraseña se almacena cifrada con BCRYPT.</p>
                <p><i class="bi bi-check2-circle text-success me-1"></i> Solo usted y los administradores pueden consultar sus datos.</p>
                <p class="mb-0"><i class="bi bi-check2-circle text-success me-1"></i> Nunca comparta su contraseña con terceros.</p>
            </div>
        </div>
    </div>
</div>
