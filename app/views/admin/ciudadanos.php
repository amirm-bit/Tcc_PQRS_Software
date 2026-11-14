<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Ciudadanos registrados</h1>
        <p>Base de datos de la comunidad con acceso al portal.</p>
    </div>
    <a href="<?= url('admin/usuarios/crear') ?>" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Registrar ciudadano</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($ciudadanos)): ?>
            <div class="empty-state"><i class="bi bi-person-vcard"></i>No hay ciudadanos registrados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Ciudadano</th><th>Documento</th><th>Contacto</th><th>Ubicación</th><th>Estado</th><th>Registro</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($ciudadanos as $c): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold small"><?= e($c['nombres'] . ' ' . $c['apellidos']) ?></div>
                                    <div class="text-muted small"><?= e($c['email']) ?></div>
                                </td>
                                <td class="small"><?= e($c['tipo_documento']) ?> <?= e($c['numero_documento']) ?></td>
                                <td class="small">
                                    <?= e($c['telefono'] ?? '—') ?><br>
                                    <span class="text-muted"><?= e($c['direccion'] ?? '—') ?></span>
                                </td>
                                <td class="small"><?= e($c['barrio'] ?? '—') ?></td>
                                <td><?= (int)$c['activo'] === 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                                <td class="small text-muted"><?= format_fecha($c['created_at']) ?></td>
                                <td class="text-end"><a href="<?= url('admin/usuarios/editar/' . (int)$c['usuario_id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-pencil"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
