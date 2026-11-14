<?php $app = config(); ?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 text-center py-5">
            <div class="display-1 fw-bold text-warning">403</div>
            <h1 class="h3 mt-2">Acceso denegado</h1>
            <p class="text-muted">No tiene permisos para acceder a este recurso. Si cree que es un error, contacte al administrador del sistema.</p>
            <div class="d-flex gap-2 justify-content-center mt-3">
                <?php if (Auth::check()): ?>
                    <a href="<?= url(dashboard_for_role(Auth::role())) ?>" class="btn btn-primary"><i class="bi bi-grid-1x2 me-1"></i> Ir a mi panel</a>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión</a>
                <?php endif; ?>
                <a href="<?= url('') ?>" class="btn btn-soft">Volver al inicio</a>
            </div>
        </div>
    </div>
</section>
