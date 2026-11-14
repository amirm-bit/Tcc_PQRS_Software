<?php $app = config(); ?>
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 py-5">
            <div class="card border-danger">
                <div class="card-body text-center p-5">
                    <i class="bi bi-exclamation-octagon text-danger" style="font-size:3rem;"></i>
                    <h1 class="h4 mt-3">Ha ocurrido un error</h1>
                    <p class="text-muted">No fue posible procesar su solicitud en este momento.</p>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger small text-start"><?= e($message) ?></div>
                    <?php endif; ?>
                    <a href="<?= url('') ?>" class="btn btn-primary"><i class="bi bi-house-door me-1"></i> Volver al inicio</a>
                </div>
            </div>
        </div>
    </div>
</section>
