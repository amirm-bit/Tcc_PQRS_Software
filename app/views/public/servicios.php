<?php $app = config(); ?>
<section class="hero" style="padding:3rem 0;">
    <div class="container">
        <span class="hero-chip"><i class="bi bi-folder2-open"></i> Servicios y trámites</span>
        <h1 class="h2">Catálogo de servicios de demostración</h1>
        <p class="lead mt-2 mb-0">Consulte requisitos, instrucciones y disponibilidad de los trámites y PQRS del sistema.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h4 section-title mb-0">Trámites y certificados</h2>
                    <span class="badge bg-primary"><?= count($tiposTramites) ?> disponibles</span>
                </div>

                <div class="accordion" id="acordeonTramites">
                    <?php foreach ($tiposTramites as $i => $t): ?>
                        <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                            <h2 class="accordion-header" id="heading<?= (int)$t['id'] ?>">
                                <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#tramite<?= (int)$t['id'] ?>" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                                    <span class="feature-icon green me-3 mb-0" style="width:40px;height:40px;font-size:1.1rem;"><i class="bi bi-file-earmark-text"></i></span>
                                    <span class="flex-grow-1">
                                        <span class="fw-bold d-block"><?= e($t['nombre']) ?></span>
                                        <span class="small text-muted"><?= e($t['dependencia_nombre'] ?? 'Ventanilla única') ?> · <?= (int)$t['dias_termino'] ?> días hábiles</span>
                                    </span>
                                </button>
                            </h2>
                            <div id="tramite<?= (int)$t['id'] ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" data-bs-parent="#acordeonTramites">
                                <div class="accordion-body">
                                    <p class="text-muted"><?= e($t['descripcion']) ?></p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold small text-uppercase text-muted">Requisitos</h6>
                                            <div class="small" style="white-space:pre-line;"><?= e($t['requisitos']) ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold small text-uppercase text-muted">Instrucciones</h6>
                                            <div class="small" style="white-space:pre-line;"><?= e($t['instrucciones']) ?></div>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex gap-2">
                                        <?php if (Auth::check() && Auth::is('ciudadano')): ?>
                                            <a href="<?= url('ciudadano/tramites/solicitar/' . (int)$t['id']) ?>" class="btn btn-success btn-sm"><i class="bi bi-send me-1"></i> Solicitar este trámite</a>
                                        <?php else: ?>
                                            <a href="<?= url('login') ?>" class="btn btn-success btn-sm"><i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión para solicitar</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($tiposTramites)): ?>
                        <div class="empty-state"><i class="bi bi-folder-x"></i>No hay trámites activos en este momento.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><i class="bi bi-chat-square-text me-1"></i> Tipos de PQRS</div>
                    <div class="card-body">
                        <?php foreach ($tiposPqrs as $tp): ?>
                            <div class="d-flex gap-2 mb-3">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <div>
                                    <div class="fw-bold small"><?= e($tp['nombre']) ?></div>
                                    <div class="small text-muted"><?= e($tp['descripcion']) ?></div>
                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i> <?= (int)$tp['dias_termino'] ?> días</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><i class="bi bi-lightbulb me-1"></i> Nota importante</div>
                    <div class="card-body small text-muted">
                        Los servicios aquí descritos son de <strong>demostración académica</strong>. No representan trámites oficiales
                        de la Alcaldía Municipal de Lorica y no tienen validez legal.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
