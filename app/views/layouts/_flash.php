<?php
// app/views/layouts/_flash.php - Render de mensajes flash
$__flash = Flash::all();
if (!empty($__flash)):
    foreach ($__flash as $__f): ?>
        <div class="alert alert-<?= e($__f['type']) ?> alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
            <i class="bi <?= $__f['type'] === 'success' ? 'bi-check-circle-fill' : ($__f['type'] === 'danger' ? 'bi-exclamation-triangle-fill' : ($__f['type'] === 'warning' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill')) ?> mt-1"></i>
            <div><?= e($__f['message']) ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endforeach;
endif;
