<?php
$app = config();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Acceso') ?> · <?= e($app['name']) ?></title>
    <link rel="stylesheet" href="<?= asset('bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<div class="auth-wrap">
    <aside class="auth-aside">
        <div>
            <a href="<?= url('') ?>" class="d-flex align-items-center gap-2 text-decoration-none mb-4">
                <div class="brand-shield"><i class="bi bi-bank2"></i></div>
                <div>
                    <div class="text-white fw-bold"><?= e($app['name']) ?></div>
                    <div class="small text-white-50"><?= e($app['municipio']) ?>, <?= e($app['departamento']) ?></div>
                </div>
            </a>
            <h2>Gestión digital de trámites y PQRS</h2>
            <p class="text-white-50 mb-0" style="max-width: 460px;">
                Plataforma institucional para radicar peticiones, quejas, reclamos y sugerencias,
                y hacer seguimiento a sus solicitudes de certificados y trámites municipales.
            </p>
            <ul>
                <li><i class="bi bi-check-circle-fill"></i><span>Radicación en línea con número único de radicado.</span></li>
                <li><i class="bi bi-check-circle-fill"></i><span>Seguimiento en tiempo real de cada solicitud.</span></li>
                <li><i class="bi bi-check-circle-fill"></i><span>Respuestas formales emitidas por funcionarios.</span></li>
                <li><i class="bi bi-check-circle-fill"></i><span>Ambiente académico de demostración local.</span></li>
            </ul>
        </div>
        <div class="small text-white-50">
            <i class="bi bi-shield-lock me-1"></i> Conexión segura · Datos ficticios de demostración
        </div>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <a href="<?= url('') ?>" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-3">
                <i class="bi bi-arrow-left"></i> Volver al portal
            </a>
            <?php require __DIR__ . '/_flash.php'; ?>
            <?= $content ?>
        </div>
    </main>
</div>
<script src="<?= asset('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
