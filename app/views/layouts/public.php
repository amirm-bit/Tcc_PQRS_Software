<?php
$active = $active ?? '';
$app = config();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema web de trámites y PQRS de la Alcaldía Municipal de Lorica, Córdoba. Plataforma de demostración académica.">
    <title><?= e($title ?? 'Inicio') ?> · <?= e($app['name']) ?></title>
    <link rel="stylesheet" href="<?= asset('bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>

<div class="gov-strip">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-1">
        <span><i class="bi bi-bank2 me-1"></i> Gobierno Municipal · Colombia</span>
        <span class="d-none d-md-inline">
            <a href="<?= url('acerca') ?>">Transparencia</a><span class="divider">|</span>
            <a href="<?= url('servicios') ?>">Servicios</a><span class="divider">|</span>
            <a href="<?= url('consultar-radicado') ?>">Consulta de radicados</a>
        </span>
    </div>
</div>

<header class="public-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <a href="<?= url('') ?>" class="public-brand text-decoration-none">
                <div class="brand-shield"><i class="bi bi-bank2"></i></div>
                <div class="brand-text">
                    <div class="brand-title"><?= e($app['name']) ?></div>
                    <div class="brand-sub"><?= e($app['municipio']) ?>, <?= e($app['departamento']) ?> · <?= e($app['pais']) ?></div>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <?php if (Auth::check()): ?>
                    <a href="<?= url(dashboard_for_role(Auth::role())) ?>" class="btn btn-primary btn-sm"><i class="bi bi-grid-1x2 me-1"></i> Mi panel</a>
                    <form action="<?= url('logout') ?>" method="post" class="m-0">
                        <?= Csrf::field() ?>
                        <button class="btn btn-soft btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Salir</button>
                    </form>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión</a>
                    <a href="<?= url('registro') ?>" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i> Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
        <nav class="public-nav pb-1">
            <ul class="nav flex-wrap">
                <li class="nav-item"><a class="nav-link <?= $active === 'inicio' ? 'active' : '' ?>" href="<?= url('') ?>"><i class="bi bi-house-door me-1"></i> Inicio</a></li>
                <li class="nav-item"><a class="nav-link <?= $active === 'acerca' ? 'active' : '' ?>" href="<?= url('acerca') ?>"><i class="bi bi-info-circle me-1"></i> Acerca del sistema</a></li>
                <li class="nav-item"><a class="nav-link <?= $active === 'servicios' ? 'active' : '' ?>" href="<?= url('servicios') ?>"><i class="bi bi-folder2-open me-1"></i> Servicios y trámites</a></li>
                <li class="nav-item"><a class="nav-link <?= $active === 'consulta' ? 'active' : '' ?>" href="<?= url('consultar-radicado') ?>"><i class="bi bi-search me-1"></i> Consultar radicado</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container mt-3">
        <?php require __DIR__ . '/_flash.php'; ?>
    </div>
    <?= $content ?>
</main>

<footer class="public-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-3">
                    <div class="brand-shield"><i class="bi bi-bank2"></i></div>
                    <div>
                        <div class="text-white fw-bold"><?= e($app['name']) ?></div>
                        <div class="small text-white-50"><?= e($app['municipio']) ?>, <?= e($app['departamento']) ?></div>
                    </div>
                </div>
                <p class="small mb-2"><?= e($app['lema']) ?></p>
                <p class="small mb-0"><i class="bi bi-geo-alt me-1"></i> <?= e($app['direccion']) ?></p>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-2">Sistema</h6>
                <div class="d-flex flex-column gap-1">
                    <a href="<?= url('') ?>">Inicio</a>
                    <a href="<?= url('acerca') ?>">Acerca del sistema</a>
                    <a href="<?= url('servicios') ?>">Servicios y trámites</a>
                    <a href="<?= url('consultar-radicado') ?>">Consultar radicado</a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-2">Acceso</h6>
                <div class="d-flex flex-column gap-1">
                    <a href="<?= url('login') ?>">Iniciar sesión</a>
                    <a href="<?= url('registro') ?>">Registro de ciudadano</a>
                    <a href="<?= url('recuperar') ?>">Recuperar acceso</a>
                </div>
            </div>
            <div class="col-lg-4">
                <h6 class="mb-2">Contacto</h6>
                <p class="small mb-1"><i class="bi bi-telephone me-1"></i> <?= e($app['telefono']) ?></p>
                <p class="small mb-1"><i class="bi bi-envelope me-1"></i> <?= e($app['email']) ?></p>
                <p class="small mb-0"><i class="bi bi-clock me-1"></i> <?= e($app['horario']) ?></p>
            </div>
        </div>
        <div class="footer-line d-flex flex-wrap justify-content-between gap-2">
            <span>© <?= date('Y') ?> <?= e($app['name']) ?> · NIT <?= e($app['nit']) ?></span>
            <span>Proyecto académico de demostración · Software Project Management</span>
        </div>
    </div>
</footer>

<script src="<?= asset('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<?= $scripts ?? '' ?>
</body>
</html>
