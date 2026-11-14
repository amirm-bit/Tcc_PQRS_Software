<div class="auth-logo"><i class="bi bi-person-lock"></i></div>
<h1 class="h4 fw-bold mb-1">Iniciar sesión</h1>
<p class="text-muted small mb-4">Acceda con su cuenta de ciudadano, funcionario o administrador.</p>

<form action="<?= url('login') ?>" method="post" novalidate>
    <?= Csrf::field() ?>
    <div class="mb-3">
        <label class="form-label" for="email">Correo electrónico</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder="usuario@lorica.test" required autofocus>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            <button class="btn btn-soft" type="button" onclick="const p=document.getElementById('password');p.type=p.type==='password'?'text':'password';"><i class="bi bi-eye"></i></button>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="recordar" disabled>
            <label class="form-check-label small text-muted" for="recordar">Recordarme (demo)</label>
        </div>
        <a href="<?= url('recuperar') ?>" class="small">¿Olvidó su contraseña?</a>
    </div>
    <button class="btn btn-primary w-100 btn-lg mb-3"><i class="bi bi-box-arrow-in-right me-1"></i> Ingresar al sistema</button>
</form>

<div class="auth-demo">
    <div class="fw-bold mb-1"><i class="bi bi-key me-1"></i> Cuentas de demostración</div>
    <div>Admin: <code>admin@lorica.test</code> / <code>Admin123!</code></div>
    <div>Funcionario: <code>funcionario@lorica.test</code> / <code>Funcionario123!</code></div>
    <div>Ciudadano: <code>ciudadano@lorica.test</code> / <code>Ciudadano123!</code></div>
</div>

<p class="text-center small text-muted mt-4 mb-0">
    ¿No tiene cuenta? <a href="<?= url('registro') ?>" class="fw-semibold">Regístrese como ciudadano</a>
</p>
