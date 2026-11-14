<?php $app = config(); ?>
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="hero-chip"><i class="bi bi-patch-check-fill"></i> Portal institucional · Demostración académica</span>
                <h1>Trámites y PQRS en línea para la comunidad de Lorica</h1>
                <p class="lead mt-3">
                    Radique peticiones, quejas, reclamos y sugerencias, solicite certificados y
                    haga seguimiento en tiempo real a cada una de sus solicitudes desde cualquier dispositivo.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <?php if (Auth::check()): ?>
                        <a href="<?= url(dashboard_for_role(Auth::role())) ?>" class="btn btn-light btn-lg text-primary fw-bold"><i class="bi bi-grid-1x2 me-1"></i> Ir a mi panel</a>
                    <?php else: ?>
                        <a href="<?= url('registro') ?>" class="btn btn-light btn-lg text-primary fw-bold"><i class="bi bi-person-plus me-1"></i> Registrarme</a>
                        <a href="<?= url('login') ?>" class="btn btn-outline-light btn-lg"><i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión</a>
                    <?php endif; ?>
                    <a href="<?= url('consultar-radicado') ?>" class="btn btn-link btn-lg text-white text-decoration-underline"><i class="bi bi-search me-1"></i> Consultar radicado</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card">
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-2" style="letter-spacing:.1em;">Atención al ciudadano</h6>
                    <div class="hc-item">
                        <i class="bi bi-file-earmark-plus"></i>
                        <div>
                            <div class="fw-bold">Radicación en línea</div>
                            <div class="small text-white-50">PQRS con número único de radicado</div>
                        </div>
                    </div>
                    <div class="hc-item">
                        <i class="bi bi-folder-check"></i>
                        <div>
                            <div class="fw-bold">Certificados y trámites</div>
                            <div class="small text-white-50">Solicitudes de demostración disponibles</div>
                        </div>
                    </div>
                    <div class="hc-item">
                        <i class="bi bi-graph-up-arrow"></i>
                        <div>
                            <div class="fw-bold">Seguimiento transparente</div>
                            <div class="small text-white-50">Consulte el estado en tiempo real</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100">
                    <div class="stat-icon mx-auto"><i class="bi bi-inbox"></i></div>
                    <div class="stat-value"><?= number_format($totalPqrs) ?></div>
                    <div class="stat-label">PQRS registradas</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100">
                    <div class="stat-icon green mx-auto"><i class="bi bi-file-earmark-check"></i></div>
                    <div class="stat-value"><?= number_format($totalTramites) ?></div>
                    <div class="stat-label">Trámites solicitados</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100">
                    <div class="stat-icon amber mx-auto"><i class="bi bi-people"></i></div>
                    <div class="stat-value"><?= number_format($totalCiudadanos) ?></div>
                    <div class="stat-label">Ciudadanos registrados</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100">
                    <div class="stat-icon slate mx-auto"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Trazabilidad de gestiones</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background:#fff;">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-eyebrow">Accesos rápidos</span>
            <h2 class="section-title mt-1">¿Qué desea realizar hoy?</h2>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <a href="<?= url('ciudadano/pqrs/nueva') ?>" class="quick-link">
                    <span class="ql-icon"><i class="bi bi-pencil-square"></i></span>
                    <span>
                        <span class="fw-bold d-block">Radicar PQRS</span>
                        <span class="small text-muted">Petición, queja, reclamo o sugerencia</span>
                    </span>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="<?= url('ciudadano/tramites') ?>" class="quick-link green">
                    <span class="ql-icon"><i class="bi bi-file-earmark-text"></i></span>
                    <span>
                        <span class="fw-bold d-block">Solicitar certificado</span>
                        <span class="small text-muted">Residencia, vecindad y más</span>
                    </span>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="<?= url('consultar-radicado') ?>" class="quick-link">
                    <span class="ql-icon"><i class="bi bi-search"></i></span>
                    <span>
                        <span class="fw-bold d-block">Consultar radicado</span>
                        <span class="small text-muted">Estado y seguimiento público</span>
                    </span>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="<?= url('servicios') ?>" class="quick-link green">
                    <span class="ql-icon"><i class="bi bi-folder2-open"></i></span>
                    <span>
                        <span class="fw-bold d-block">Ver servicios</span>
                        <span class="small text-muted">Catálogo de trámites disponibles</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 align-items-center mb-4">
            <div class="col-lg-8">
                <span class="section-eyebrow">Servicios institucionales</span>
                <h2 class="section-title mt-1 mb-1">Trámites y certificados disponibles</h2>
                <p class="text-muted mb-0">Solicitudes de demostración académica. No sustituyen los trámites presenciales oficiales.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= url('servicios') ?>" class="btn btn-outline-primary">Ver todos <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach (array_slice($tiposTramites, 0, 3) as $t): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon green"><i class="bi bi-file-earmark-text"></i></div>
                        <h5 class="h6 fw-bold"><?= e($t['nombre']) ?></h5>
                        <p class="text-muted small mb-2"><?= e(truncate($t['descripcion'], 130)) ?></p>
                        <div class="small text-muted mb-3"><i class="bi bi-clock me-1"></i> Término: <?= (int)$t['dias_termino'] ?> días hábiles</div>
                        <a href="<?= Auth::check() && Auth::is('ciudadano') ? url('ciudadano/tramites/solicitar/' . (int)$t['id']) : url('login') ?>" class="btn btn-sm btn-success">
                            Solicitar <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" style="background:#fff;">
    <div class="container">
        <div class="text-center mb-4">
            <span class="section-eyebrow">Proceso</span>
            <h2 class="section-title mt-1">¿Cómo funciona?</h2>
        </div>
        <div class="row g-4">
            <?php
            $pasos = [
                ['1', 'Regístrese o inicie sesión', 'Cree su cuenta de ciudadano con sus datos personales.', 'bi-person-plus'],
                ['2', 'Radique su solicitud', 'Complete el formulario y adjunte documentos de soporte.', 'bi-pencil-square'],
                ['3', 'Reciba su radicado', 'Obtenga un número único para consultar su trámite.', 'bi-hash'],
                ['4', 'Haga seguimiento', 'Consulte el estado, observaciones y respuestas oficiales.', 'bi-graph-up-arrow'],
            ];
            foreach ($pasos as $p): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary rounded-circle" style="width:30px;height:30px;display:grid;place-items:center;"><?= $p[0] ?></span>
                            <i class="bi <?= $p[3] ?> fs-5 text-primary"></i>
                        </div>
                        <h6 class="fw-bold"><?= e($p[1]) ?></h6>
                        <p class="small text-muted mb-0"><?= e($p[2]) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="card border-0 shadow-sm overflow-hidden" style="background:linear-gradient(120deg,var(--azul-oscuro),var(--azul));">
            <div class="card-body p-4 p-lg-5 text-white d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h3 class="text-white fw-bold mb-1">¿Listo para radicar su solicitud?</h3>
                    <p class="mb-0 text-white-50">Regístrese en minutos y gestione sus trámites desde cualquier lugar.</p>
                </div>
                <a href="<?= Auth::check() ? url(dashboard_for_role(Auth::role())) : url('registro') ?>" class="btn btn-light btn-lg text-primary fw-bold">
                    <i class="bi bi-arrow-right-circle me-1"></i> <?= Auth::check() ? 'Ir a mi panel' : 'Crear mi cuenta' ?>
                </a>
            </div>
        </div>
    </div>
</section>
