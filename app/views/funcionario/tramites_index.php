<div class="page-heading">
    <h1>Solicitudes de trámites asignadas</h1>
    <p>Certificados y trámites bajo su responsabilidad.</p>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($solicitudes)): ?>
            <div class="empty-state"><i class="bi bi-file-earmark-x"></i>No tiene solicitudes de trámites asignadas.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Radicado</th><th>Ciudadano</th><th>Trámite</th><th>Fecha</th><th>Estado</th><th class="text-end">Acción</th></tr></thead>
                    <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td><span class="radicado-pill"><?= e($s['radicado']) ?></span></td>
                                <td class="small"><?= e(trim(($s['ciudadano_nombres'] ?? '') . ' ' . ($s['ciudadano_apellidos'] ?? ''))) ?></td>
                                <td class="small"><?= e($s['tipo_nombre']) ?></td>
                                <td class="small text-muted"><?= format_fecha($s['fecha_radicacion']) ?></td>
                                <td><span class="badge bg-<?= badge_estado($s['color_badge']) ?>"><?= e($s['estado_nombre']) ?></span></td>
                                <td class="text-end"><a href="<?= url('funcionario/tramites/gestionar/' . (int)$s['id']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-tools me-1"></i> Gestionar</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
