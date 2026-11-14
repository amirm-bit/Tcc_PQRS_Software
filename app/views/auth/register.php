<div class="auth-logo"><i class="bi bi-person-plus"></i></div>
<h1 class="h4 fw-bold mb-1">Registro de ciudadano</h1>
<p class="text-muted small mb-4">Cree su cuenta para radicar PQRS y solicitar trámites.</p>

<form action="<?= url('registro') ?>" method="post" novalidate>
    <?= Csrf::field() ?>
    <div class="row g-3">
        <div class="col-sm-6">
            <label class="form-label" for="nombres">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= old('nombres') ?>" required>
        </div>
        <div class="col-sm-6">
            <label class="form-label" for="apellidos">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= old('apellidos') ?>" required>
        </div>
        <div class="col-sm-5">
            <label class="form-label" for="tipo_documento">Tipo de documento</label>
            <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                <option value="CC" <?= old('tipo_documento') === 'CC' ? 'selected' : '' ?>>Cédula de ciudadanía</option>
                <option value="TI" <?= old('tipo_documento') === 'TI' ? 'selected' : '' ?>>Tarjeta de identidad</option>
                <option value="CE" <?= old('tipo_documento') === 'CE' ? 'selected' : '' ?>>Cédula de extranjería</option>
                <option value="PASAPORTE" <?= old('tipo_documento') === 'PASAPORTE' ? 'selected' : '' ?>>Pasaporte</option>
            </select>
        </div>
        <div class="col-sm-7">
            <label class="form-label" for="numero_documento">Número de documento</label>
            <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="<?= old('numero_documento') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="email">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
        </div>
        <div class="col-sm-6">
            <label class="form-label" for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= old('telefono') ?>" required>
        </div>
        <div class="col-sm-6">
            <label class="form-label" for="barrio">Barrio o vereda</label>
            <input type="text" class="form-control" id="barrio" name="barrio" value="<?= old('barrio') ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="direccion">Dirección de residencia</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= old('direccion') ?>" required>
        </div>
        <div class="col-sm-6">
            <label class="form-label" for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="form-text">Mínimo 8 caracteres, con letras y números.</div>
        </div>
        <div class="col-sm-6">
            <label class="form-label" for="password_confirm">Confirmar contraseña</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>
    </div>
    <button class="btn btn-primary w-100 btn-lg mt-4"><i class="bi bi-person-check me-1"></i> Crear mi cuenta</button>
</form>

<p class="text-center small text-muted mt-3 mb-0">
    ¿Ya tiene cuenta? <a href="<?= url('login') ?>" class="fw-semibold">Iniciar sesión</a>
</p>
