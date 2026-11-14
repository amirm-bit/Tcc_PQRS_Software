<?php
$app = config();
$user = Auth::user();
$rol = Auth::role();
$activeMenu = $activeMenu ?? '';
$perfil = Auth::perfil();
$nombreCompleto = Auth::name();
$iniciales = '';
$partes = preg_split('/\s+/', trim($nombreCompleto));
foreach (array_slice($partes, 0, 2) as $p) {
    if ($p !== '') $iniciales .= mb_strtoupper(mb_substr($p, 0, 1));
}
$iniciales = $iniciales ?: 'AL';
$noLeidas = ($rol === 'ciudadano') ? NotificationService::noLeidas((int)$user['id']) : 0;

$menus = [
    'ciudadano' => [
        ['label' => 'Principal', 'items' => [
            ['key' => 'dashboard', 'icon' => 'bi-grid-1x2', 'text' => 'Dashboard', 'url' => 'ciudadano/dashboard'],
            ['key' => 'nueva-pqrs', 'icon' => 'bi-plus-circle', 'text' => 'Nueva PQRS', 'url' => 'ciudadano/pqrs/nueva'],
            ['key' => 'mis-pqrs', 'icon' => 'bi-inbox', 'text' => 'Mis PQRS', 'url' => 'ciudadano/pqrs'],
        ]],
        ['label' => 'Trámites y certificados', 'items' => [
            ['key' => 'tramites', 'icon' => 'bi-folder2-open', 'text' => 'Trámites disponibles', 'url' => 'ciudadano/tramites'],
            ['key' => 'mis-tramites', 'icon' => 'bi-file-earmark-text', 'text' => 'Mis trámites', 'url' => 'ciudadano/tramites/mis-solicitudes'],
        ]],
        ['label' => 'Mi cuenta', 'items' => [
            ['key' => 'notificaciones', 'icon' => 'bi-bell', 'text' => 'Notificaciones', 'url' => 'ciudadano/notificaciones'],
            ['key' => 'perfil', 'icon' => 'bi-person-gear', 'text' => 'Mi perfil', 'url' => 'ciudadano/perfil'],
        ]],
    ],
    'funcionario' => [
        ['label' => 'Gestión', 'items' => [
            ['key' => 'dashboard', 'icon' => 'bi-grid-1x2', 'text' => 'Dashboard', 'url' => 'funcionario/dashboard'],
            ['key' => 'pqrs', 'icon' => 'bi-inbox', 'text' => 'PQRS asignadas', 'url' => 'funcionario/pqrs'],
            ['key' => 'pendientes', 'icon' => 'bi-hourglass-split', 'text' => 'Pendientes', 'url' => 'funcionario/pqrs/pendientes'],
            ['key' => 'tramites', 'icon' => 'bi-folder2-open', 'text' => 'Gestión de trámites', 'url' => 'funcionario/tramites'],
            ['key' => 'historial', 'icon' => 'bi-clock-history', 'text' => 'Historial de actividades', 'url' => 'funcionario/historial'],
        ]],
    ],
    'admin' => [
        ['label' => 'Panel general', 'items' => [
            ['key' => 'dashboard', 'icon' => 'bi-grid-1x2', 'text' => 'Dashboard general', 'url' => 'admin/dashboard'],
            ['key' => 'pqrs', 'icon' => 'bi-inbox', 'text' => 'Gestión de PQRS', 'url' => 'admin/pqrs'],
        ]],
        ['label' => 'Administración', 'items' => [
            ['key' => 'usuarios', 'icon' => 'bi-people', 'text' => 'Usuarios', 'url' => 'admin/usuarios'],
            ['key' => 'ciudadanos', 'icon' => 'bi-person-vcard', 'text' => 'Ciudadanos', 'url' => 'admin/ciudadanos'],
            ['key' => 'funcionarios', 'icon' => 'bi-person-badge', 'text' => 'Funcionarios', 'url' => 'admin/funcionarios'],
            ['key' => 'dependencias', 'icon' => 'bi-diagram-3', 'text' => 'Dependencias', 'url' => 'admin/dependencias'],
        ]],
        ['label' => 'Trámites', 'items' => [
            ['key' => 'tipos-tramites', 'icon' => 'bi-list-check', 'text' => 'Tipos de trámites', 'url' => 'admin/tramites'],
            ['key' => 'solicitudes-tramites', 'icon' => 'bi-file-earmark-check', 'text' => 'Solicitudes', 'url' => 'admin/tramites/solicitudes'],
        ]],
        ['label' => 'Control', 'items' => [
            ['key' => 'reportes', 'icon' => 'bi-bar-chart-line', 'text' => 'Reportes', 'url' => 'admin/reportes'],
            ['key' => 'auditoria', 'icon' => 'bi-shield-check', 'text' => 'Auditoría', 'url' => 'admin/auditoria'],
            ['key' => 'configuracion', 'icon' => 'bi-gear', 'text' => 'Configuración', 'url' => 'admin/configuracion'],
        ]],
    ],
];
$menuRol = $menus[$rol] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Panel') ?> · <?= e($app['name']) ?></title>
    <link rel="stylesheet" href="<?= asset('bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="panel-body">

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="panel-sidebar" id="panelSidebar">
    <div class="sidebar-brand">
        <div class="brand-shield"><i class="bi bi-bank2"></i></div>
        <div>
            <div class="sb-title">Alcaldía de Lorica</div>
            <div class="sb-sub"><?= e(role_label($rol)) ?></div>
        </div>
    </div>

    <div class="sidebar-scroll">
        <?php foreach ($menuRol as $grupo): ?>
            <div class="sidebar-label"><?= e($grupo['label']) ?></div>
            <ul class="sidebar-nav">
                <?php foreach ($grupo['items'] as $item): ?>
                    <li>
                        <a class="nav-link <?= $activeMenu === $item['key'] ? 'active' : '' ?>" href="<?= url($item['url']) ?>">
                            <i class="bi <?= e($item['icon']) ?>"></i>
                            <span><?= e($item['text']) ?></span>
                            <?php if ($item['key'] === 'notificaciones' && $noLeidas > 0): ?>
                                <span class="badge bg-danger"><?= $noLeidas ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>

    <div class="sidebar-footer">
        <i class="bi bi-mortarboard me-1"></i> Sistema de demostración académica
    </div>
</aside>

<div class="panel-main">
    <header class="panel-topbar">
        <button class="icon-btn topbar-toggle" type="button" id="sidebarToggle" aria-label="Menú">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title">
            <?= e($pageTitle ?? ($title ?? 'Panel')) ?>
            <?php if (!empty($pageSubtitle)): ?><small><?= e($pageSubtitle) ?></small><?php endif; ?>
        </div>
        <div class="topbar-actions">
            <?php if ($rol === 'ciudadano'): ?>
                <a href="<?= url('ciudadano/notificaciones') ?>" class="icon-btn" title="Notificaciones">
                    <i class="bi bi-bell"></i>
                    <?php if ($noLeidas > 0): ?><span class="dot"></span><?php endif; ?>
                </a>
            <?php endif; ?>
            <a href="<?= url('') ?>" class="icon-btn d-none d-sm-grid" title="Ir al portal público"><i class="bi bi-house-door"></i></a>
            <div class="dropdown">
                <button class="user-chip border-0" data-bs-toggle="dropdown" aria-expanded="false" type="button">
                    <span class="user-avatar"><?= e($iniciales) ?></span>
                    <span class="text-start">
                        <span class="u-name d-block"><?= e($nombreCompleto) ?></span>
                        <span class="u-role d-block"><?= e(role_label($rol)) ?></span>
                    </span>
                    <i class="bi bi-chevron-down small text-muted ms-1"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li><h6 class="dropdown-header text-truncate" style="max-width: 240px;"><?= e($user['email']) ?></h6></li>
                    <?php if ($rol === 'ciudadano'): ?>
                        <li><a class="dropdown-item" href="<?= url('ciudadano/perfil') ?>"><i class="bi bi-person-gear me-2"></i>Mi perfil</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="<?= url('logout') ?>" method="post" class="m-0">
                            <?= Csrf::field() ?>
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="panel-content">
        <?php require __DIR__ . '/_flash.php'; ?>
        <?= $content ?>
    </div>
</div>

<script src="<?= asset('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/chart.umd.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<?= $scripts ?? '' ?>
</body>
</html>
