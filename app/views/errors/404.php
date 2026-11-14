<?php $app = config(); ?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 text-center py-5">
            <div class="display-1 fw-bold text-primary">404</div>
            <h1 class="h3 mt-2">Página no encontrada</h1>
            <p class="text-muted">La dirección que intenta consultar no existe o fue movida. Verifique el enlace e intente nuevamente.</p>
            <?php if (!empty($path)): ?><p class="small text-muted">Ruta: <code><?= e($path) ?></code></p><?php endif; ?>
            <a href="<?= url('') ?>" class="btn btn-primary mt-2"><i class="bi bi-house-door me-1"></i> Volver al inicio</a>
        </div>
    </div>
</section>
