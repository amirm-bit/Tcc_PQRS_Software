<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Gestión de usuarios</h1>
        <p>Administradores, funcionarios y ciudadanos registrados en el sistema.</p>
    </div>
    <a href="<?= url('admin/usuarios/crear') ?>" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Crear usuario</a>
</div>

<form class="filter-bar" method="get" action="<?= url('admin/usuarios') ?>">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label mb-1">Buscar</label>
            <input type="text" class="form-control" name="q" value="<?= e($filtros['q']) ?>" placeholder="Correo, nombre o documento">
        </div>
        <div class="col-md-4">
            <label class="form-label mb-1">Rol</label>
            <select class="form-select" name="rol">
                <option value="">Todos los roles</option>
                <option value="admin" <?= $filtros['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="funcionario" <?= $filtros['rol'] === 'funcionario' ? 'selected' : '' ?>>Funcionario</option>
                <option value="ciudadano" <?= $filtros['rol'] === 'ciudadano' ? 'selected' : '' ?>>Ciudadano</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
            <a href="<?= url('admin/usuarios') ?>" class="btn btn-soft"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($usuarios)): ?>
            <div class="empty-state"><i class="bi bi-people"></i>No se encontraron usuarios.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Usuario</th><th>Rol</th><th>Documento</th><th>Dependencia / Cargo</th><th>Estado</th><th>Registro</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <?php $nombre = trim(($u['c_nombres'] ?? $u['f_nombres'] ?? '') . ' ' . ($u['c_apellidos'] ?? $u['f_apellidos'] ?? '')); ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= e($nombre ?: 'Sin perfil') ?></div>
                                    <div class="text-muted small"><?= e($u['email']) ?></div>
                                </td>
                                <td><span class="badge bg-<?= $u['rol'] === 'admin' ? 'dark' : ($u['rol'] === 'funcionario' ? 'primary' : 'success') ?>"><?= e(role_label($u['rol'])) ?></span></td>
                                <td class="small"><?= e($u['numero_documento'] ?? '—') ?></td>
                                <td class="small">
                                    <?= e($u['dependencia_nombre'] ?? '—') ?>
                                    <?php if (!empty($u['cargo'])): ?><div class="text-muted"><?= e($u['cargo']) ?></div><?php endif; ?>
                                </td>
                                <td><?= (int)$u['activo'] === 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                                <td class="small text-muted"><?= format_fecha($u['created_at']) ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= url('admin/usuarios/editar/' . (int)$u['id']) ?>" class="btn btn-sm btn-soft" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <?php if ((int)$u['id'] !== (int)Auth::id()): ?>
                                            <form action="<?= url('admin/usuarios/estado/' . (int)$u['id']) ?>" method="post" class="m-0" data-confirm="¿Desea <?= (int)$u['activo'] === 1 ? 'desactivar' : 'activar' ?> esta cuenta?">
                                                <?= Csrf::field() ?>
                                                <button class="btn btn-sm <?= (int)$u['activo'] === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?>" title="<?= (int)$u['activo'] === 1 ? 'Desactivar' : 'Activar' ?>">
                                                    <i class="bi <?= (int)$u['activo'] === 1 ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
