<div class="page-heading">
    <h1>Dependencias municipales</h1>
    <p>Estructura administrativa de la Alcaldía de Lorica.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-diagram-3 me-1"></i> Listado de dependencias</div>
            <div class="card-body p-0">
                <?php if (empty($dependencias)): ?>
                    <div class="empty-state"><i class="bi bi-diagram-3"></i>No hay dependencias registradas.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Código</th><th>Nombre</th><th>Contacto</th><th>Funcionarios</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                            <tbody>
                                <?php foreach ($dependencias as $d): ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?= e($d['codigo']) ?></span></td>
                                        <td class="small fw-semibold"><?= e($d['nombre']) ?></td>
                                        <td class="small">
                                            <?= e($d['email'] ?? '—') ?>
                                            <?php if (!empty($d['telefono'])): ?><div class="text-muted"><?= e($d['telefono']) ?></div><?php endif; ?>
                                        </td>
                                        <td class="small"><?= (int)$d['total_funcionarios'] ?></td>
                                        <td><?= (int)$d['activo'] === 1 ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>' ?></td>
                                        <td class="text-end">
                                            <form action="<?= url('admin/dependencias/estado/' . (int)$d['id']) ?>" method="post" class="m-0" data-confirm="¿Desea cambiar el estado de esta dependencia?">
                                                <?= Csrf::field() ?>
                                                <button class="btn btn-sm <?= (int)$d['activo'] === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                                    <i class="bi <?= (int)$d['activo'] === 1 ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-1"></i> Nueva dependencia</div>
            <div class="card-body">
                <form action="<?= url('admin/dependencias/crear') ?>" method="post">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="codigo" value="<?= old('codigo') ?>" maxlength="20" required placeholder="Ej: SECGOB">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" value="<?= old('nombre') ?>" maxlength="150" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="telefono" value="<?= old('telefono') ?>">
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Crear dependencia</button>
                </form>
            </div>
        </div>
    </div>
</div>
