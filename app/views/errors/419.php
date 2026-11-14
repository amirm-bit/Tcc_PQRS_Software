<?php $app = config(); ?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 text-center py-5">
            <div class="display-1 fw-bold text-warning">419</div>
            <h1 class="h3 mt-2">Sesión expirada</h1>
            <p class="text-muted">El formulario perdió su validez por seguridad (token CSRF). Recargue la página e intente nuevamente.</p>
            <div class="d-flex gap-2 justify-content-center mt-3">
                <a href="javascript:history.back()" class="btn btn-primary"><i class="bi bi-arrow-left me-1"></i> Regresar</a>
                <a href="<?= url('') ?>" class="btn btn-soft">Ir al inicio</a>
            </div>
        </div>
    </div>
</section>
