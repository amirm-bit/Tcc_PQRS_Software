<?php
$nombre = trim(($ciudadano['nombres'] ?? '') . ' ' . ($ciudadano['apellidos'] ?? ''));
?>
<div class="page-heading d-flex flex-wrap align-items-center justify-content-between gap-2">
    <div>
        <h1>Bienvenido(a), <?= e($ciudadano['nombres'] ?? 'ciudadano') ?></h1>
        <p>Este es el resumen de su actividad en el sistema de trámites y PQRS.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('ciudadano/pqrs/nueva') ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Nueva PQRS</a>
        <a href="<?= url('ciudadano/tramites') ?>" class="btn btn-outline-success"><i class="bi bi-file-earmark-text me-1"></i> Solicitar trámite</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-inbox"></i></div>
            <div class="stat-value"><?= (int)$stats['total'] ?></div>
            <div class="stat-label">PQRS radicadas</div>
            <i class="bi bi-inbox stat-accent"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value"><?= (int)$stats['abiertas'] ?></div>
            <div class="stat-label">En trámite</div>
            <i class="bi bi-hourglass-split stat-accent"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-chat-left-text"></i></div>
            <div class="stat-value"><?= (int)$stats['respondidas'] ?></div>
            <div class="stat-label">Respondidas</div>
            <i class="bi bi-chat-left-text stat-accent"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon slate"><i class="bi bi-file-earmark-check"></i></div>
            <div class="stat-value"><?= (int)$stats['tramites'] ?></div>
            <div class="stat-label">Trámites solicitados</div>
            <i class="bi bi-file-earmark-check stat-accent"></i>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-inbox me-1"></i> Últimas PQRS</span>
                <a href="<?= url('ciudadano/pqrs') ?>" class="small">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pqrs)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        Aún no ha radicado solicitudes.
                        <div class="mt-2"><a href="<?= url('ciudadano/pqrs/nueva') ?>" class="btn btn-sm btn-primary">Radicar mi primera PQRS</a></div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr><th>Radicado</th><th>Tipo</th><th>Asunto</th><th>Fecha</th><th>Estado</th><th></th></tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($pqrs, 0, 6) as $p): ?>
                                    <tr>
                                        <td><span class="radicado-pill"><?= e($p['radicado']) ?></span></td>
                                        <td class="small"><?= e($p['tipo_nombre']) ?></td>
                                        <td class="small"><?= e(truncate($p['asunto'], 45)) ?></td>
                                        <td class="small text-muted"><?= format_fecha($p['fecha_radicacion']) ?></td>
                                        <td><span class="badge bg-<?= badge_estado($p['color_badge']) ?>"><?= e($p['estado_nombre']) ?></span></td>
                                        <td class="text-end"><a href="<?= url('ciudadano/pqrs/detalle/' . (int)$p['id']) ?>" class="btn btn-sm btn-soft" title="Ver detalle"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-text me-1"></i> Últimos trámites</span>
                <a href="<?= url('ciudadano/tramites/mis-solicitudes') ?>" class="small">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($tramites)): ?>
                    <div class="empty-state py-4">
                        <i class="bi bi-file-earmark"></i>
                        No ha solicitado trámites todavía.
                        <div class="mt-2"><a href="<?= url('ciudadano/tramites') ?>" class="btn btn-sm btn-outline-success">Ver trámites disponibles</a></div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Radicado</th><th>Trámite</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach (array_slice($tramites, 0, 5) as $t): ?>
                                    <tr>
                                        <td><span class="radicado-pill"><?= e($t['radicado']) ?></span></td>
                                        <td class="small"><?= e($t['tipo_nombre']) ?></td>
                                        <td class="small text-muted"><?= format_fecha($t['fecha_radicacion']) ?></td>
                                        <td><span class="badge bg-<?= badge_estado($t['color_badge']) ?>"><?= e($t['estado_nombre']) ?></span></td>
                                        <td class="text-end"><a href="<?= url('ciudadano/tramites/detalle/' . (int)$t['id']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-eye"></i></a></td>
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
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bell me-1"></i> Notificaciones</span>
                <a href="<?= url('ciudadano/notificaciones') ?>" class="small">Ver todas</a>
            </div>
            <div class="list-group list-group-flush">
                <?php if (empty($notificaciones)): ?>
                    <div class="empty-state py-4"><i class="bi bi-bell-slash"></i>Sin notificaciones</div>
                <?php else: foreach ($notificaciones as $n): ?>
                    <div class="list-group-item">
                        <div class="d-flex gap-2">
                            <i class="bi bi-dot <?= $n['leida'] ? 'text-muted' : 'text-success' ?>" style="font-size:1.4rem;line-height:1;"></i>
                            <div>
                                <div class="fw-bold small"><?= e($n['titulo']) ?></div>
                                <div class="small text-muted"><?= e(truncate($n['mensaje'], 80)) ?></div>
                                <div class="small text-muted mt-1"><?= time_ago($n['created_at']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-person-vcard me-1"></i> Mis datos</div>
            <div class="card-body">
                <ul class="detail-list">
                    <li><span class="dl-label">Nombre</span><span class="dl-value"><?= e($nombre) ?></span></li>
                    <li><span class="dl-label">Documento</span><span class="dl-value"><?= e($ciudadano['tipo_documento'] ?? '') ?> <?= e($ciudadano['numero_documento'] ?? '') ?></span></li>
                    <li><span class="dl-label">Teléfono</span><span class="dl-value"><?= e($ciudadano['telefono'] ?? '—') ?></span></li>
                    <li><span class="dl-label">Barrio</span><span class="dl-value"><?= e($ciudadano['barrio'] ?? '—') ?></span></li>
                </ul>
                <a href="<?= url('ciudadano/perfil') ?>" class="btn btn-soft btn-sm w-100 mt-2"><i class="bi bi-pencil me-1"></i> Editar mis datos</a>
            </div>
        </div>
    </div>
</div>
