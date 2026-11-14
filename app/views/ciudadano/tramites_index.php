<div class="page-heading d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1>Trámites y certificados</h1>
        <p>Seleccione el trámite que desea solicitar. Servicios de demostración académica.</p>
    </div>
    <a href="<?= url('ciudadano/tramites/mis-solicitudes') ?>" class="btn btn-soft"><i class="bi bi-list-check me-1"></i> Mis solicitudes</a>
</div>

<div class="alert alert-warning small d-flex gap-2">
    <i class="bi bi-exclamation-circle-fill mt-1"></i>
    <div>Los certificados y trámites ofrecidos son de <strong>demostración académica</strong> y no corresponden a documentos oficiales con validez legal.</div>
</div>

<div class="row g-4">
    <?php if (empty($tipos)): ?>
        <div class="col-12"><div class="card"><div class="empty-state"><i class="bi bi-folder-x"></i>No hay trámites disponibles en este momento.</div></div></div>
    <?php else: foreach ($tipos as $t): ?>
        <div class="col-md-6 col-lg-4">
            <div class="tramite-card d-flex flex-column">
                <div class="tc-head">
                    <div class="feature-icon green"><i class="bi bi-file-earmark-text"></i></div>
                    <h5 class="h6 fw-bold mb-1"><?= e($t['nombre']) ?></h5>
                    <div class="small text-muted mb-2"><i class="bi bi-building me-1"></i><?= e($t['dependencia_nombre'] ?? 'Ventanilla única') ?></div>
                    <p class="small text-muted"><?= e(truncate($t['descripcion'], 120)) ?></p>
                </div>
                <div class="tc-body mt-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark"><i class="bi bi-clock me-1"></i><?= (int)$t['dias_termino'] ?> días</span>
                        <a href="<?= url('ciudadano/tramites/solicitar/' . (int)$t['id']) ?>" class="btn btn-sm btn-success">Solicitar <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>
