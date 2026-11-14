<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Notificaciones</h1>
        <p>Avisos generados por la gestión de sus solicitudes.</p>
    </div>
    <form action="<?= url('ciudadano/notificaciones/leer-todas') ?>" method="post" data-confirm="¿Marcar todas las notificaciones como leídas?">
        <?= Csrf::field() ?>
        <button class="btn btn-soft btn-sm"><i class="bi bi-check2-all me-1"></i> Marcar todas como leídas</button>
    </form>
</div>

<div class="card">
    <div class="list-group list-group-flush">
        <?php if (empty($notificaciones)): ?>
            <div class="empty-state"><i class="bi bi-bell-slash"></i>No tiene notificaciones registradas.</div>
        <?php else: foreach ($notificaciones as $n): ?>
            <div class="list-group-item d-flex flex-wrap align-items-start gap-3 <?= $n['leida'] ? '' : 'bg-light' ?>">
                <div class="stat-icon mb-0 <?= $n['leida'] ? 'slate' : 'green' ?>" style="width:40px;height:40px;font-size:1rem;flex:0 0 auto;">
                    <i class="bi <?= $n['leida'] ? 'bi-bell' : 'bi-bell-fill' ?>"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <span class="fw-bold small"><?= e($n['titulo']) ?></span>
                        <span class="small text-muted"><?= time_ago($n['created_at']) ?></span>
                    </div>
                    <div class="small text-muted mt-1"><?= e($n['mensaje']) ?></div>
                </div>
                <div class="d-flex gap-1">
                    <?php if (!$n['leida']): ?>
                        <form action="<?= url('ciudadano/notificaciones/leer') ?>" method="post" class="m-0">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                            <button class="btn btn-sm btn-outline-primary" title="Marcar leída y abrir"><i class="bi bi-check2"></i></button>
                        </form>
                    <?php elseif (!empty($n['enlace'])): ?>
                        <a href="<?= url($n['enlace']) ?>" class="btn btn-sm btn-soft"><i class="bi bi-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>
