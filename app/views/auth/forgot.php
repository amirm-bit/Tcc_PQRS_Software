<div class="auth-logo"><i class="bi bi-shield-lock"></i></div>
<h1 class="h4 fw-bold mb-1">Recuperar acceso</h1>
<p class="text-muted small mb-4">Ingrese su correo registrado. Se generará una contraseña temporal de demostración.</p>

<form action="<?= url('recuperar') ?>" method="post" novalidate>
    <?= Csrf::field() ?>
    <div class="mb-3">
        <label class="form-label" for="email">Correo electrónico</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder="usuario@lorica.test" required autofocus>
        </div>
    </div>
    <button class="btn btn-primary w-100 btn-lg"><i class="bi bi-arrow-repeat me-1"></i> Generar contraseña temporal</button>
</form>

<div class="alert alert-warning small mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i>
    Procedimiento local de demostración. En un entorno productivo la contraseña se enviaría por correo electrónico.
</div>

<p class="text-center small text-muted mt-3 mb-0">
    <a href="<?= url('login') ?>">Volver a iniciar sesión</a>
</p>
