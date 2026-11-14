<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Funcionarios de la Alcaldía</h1>
        <p>Equipo de atención institucional y sus dependencias.</p>
    </div>
    <a href="<?= url('admin/usuarios/crear') ?>" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Registrar funcionario</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($funcionarios)): ?>
            <div class="empty-state"><i class="bi bi-person-badge"></i>No hay funcionarios registrados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Funcionario</th><th>Cargo</th><th>Dependencia</th><th>Contacto</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($funcionarios as $f): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= e(trim(($f['f_nombres'] ?? '') . ' ' . ($f['f_apellidos'] ?? ''))) ?></div>
                                    <div class="text-muted small"><?= e($f['email']) ?></div>
                                </td>
                                <td class="small"><?= e($f['cargo'] ?? '—') ?></td>
                                <td class="small"><?= e($f['dependencia_nombre'] ?? '—') ?></td>
                                <td class="small"><?= e($f['telefono'] ?? '—') ?></td>
                                <td><?= (int)$f['activo'] === 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                                <td class="text-end"><a href="<?= url('admin/usuarios/editar/' . (int)$f['id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-pencil"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
