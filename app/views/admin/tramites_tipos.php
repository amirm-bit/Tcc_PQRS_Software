<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Tipos de trámites y certificados</h1>
        <p>Catálogo de servicios de ventanilla disponibles para los ciudadanos.</p>
    </div>
    <a href="<?= url('admin/tramites/crear') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Nuevo tipo de trámite</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($tipos)): ?>
            <div class="empty-state"><i class="bi bi-list-check"></i>No hay tipos de trámites registrados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Código</th><th>Trámite</th><th>Dependencia</th><th>Término</th><th>Campos</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($tipos as $t): ?>
                            <?php $campos = json_decode($t['detalle_campos'] ?? '[]', true) ?: []; ?>
                            <tr>
                                <td><span class="badge bg-primary"><?= e($t['codigo']) ?></span></td>
                                <td>
                                    <div class="fw-semibold small"><?= e($t['nombre']) ?></div>
                                    <div class="text-muted small"><?= e(truncate($t['descripcion'], 70)) ?></div>
                                </td>
                                <td class="small"><?= e($t['dependencia_nombre'] ?? '—') ?></td>
                                <td class="small"><?= (int)$t['dias_termino'] ?> días</td>
                                <td class="small"><?= count($campos) ?></td>
                                <td><?= (int)$t['activo'] === 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= url('admin/tramites/editar/' . (int)$t['id']) ?>" class="btn btn-sm btn-soft" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <form action="<?= url('admin/tramites/estado/' . (int)$t['id']) ?>" method="post" class="m-0" data-confirm="¿Desea cambiar la disponibilidad de este trámite?">
                                            <?= Csrf::field() ?>
                                            <button class="btn btn-sm <?= (int)$t['activo'] === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?>" title="<?= (int)$t['activo'] === 1 ? 'Desactivar' : 'Activar' ?>">
                                                <i class="bi <?= (int)$t['activo'] === 1 ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                            </button>
                                        </form>
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
