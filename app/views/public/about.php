<?php $app = config(); ?>
<section class="hero" style="padding:3rem 0;">
    <div class="container">
        <span class="hero-chip"><i class="bi bi-info-circle-fill"></i> Acerca del sistema</span>
        <h1 class="h2">Un portal para acercar la administración municipal a la ciudadanía</h1>
        <p class="lead mt-2 mb-0">Conozca el propósito, el alcance y las condiciones de esta plataforma de demostración académica.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 section-title mb-3">Objetivo del sistema</h2>
                        <p class="text-muted mb-3">
                            El Sistema Web de Trámites y PQRS de la Alcaldía Municipal de Lorica tiene como propósito
                            digitalizar la radicación, gestión y seguimiento de Peticiones, Quejas, Reclamos y Sugerencias (PQRS),
                            así como las solicitudes de certificados y trámites administrativos frecuentes.
                        </p>
                        <p class="text-muted mb-0">
                            La plataforma busca mejorar la transparencia, la trazabilidad y la oportunidad en la atención al ciudadano,
                            ofreciendo canales digitales organizados con control de acceso por roles y registro de cada actuación.
                        </p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 section-title mb-3">Alcance académico</h2>
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-mortarboard me-1"></i>
                            Este sistema corresponde a un Trabajo Colaborativo Contextualizado de la asignatura
                            <strong>Software Project Management</strong> y se ejecuta exclusivamente de forma local.
                        </div>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>Toda la información es ficticia y de uso exclusivamente demostrativo.</span></li>
                            <li class="d-flex gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>No está publicado en internet ni se integra con plataformas gubernamentales reales.</span></li>
                            <li class="d-flex gap-2 mb-2"><i class="bi bi-check2-circle text-success"></i><span>No procesa pagos en línea ni trámites oficiales.</span></li>
                            <li class="d-flex gap-2 mb-0"><i class="bi bi-check2-circle text-success"></i><span>Los certificados y respuestas emitidos no tienen validez legal.</span></li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <h2 class="h4 section-title mb-3">Tecnologías utilizadas</h2>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3" style="background:var(--gris-50);">
                                    <i class="bi bi-filetype-php text-primary fs-4"></i>
                                    <div class="fw-bold mt-1">PHP 8.2 + PDO</div>
                                    <div class="small text-muted">Arquitectura MVC, consultas preparadas y sesiones seguras.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3" style="background:var(--gris-50);">
                                    <i class="bi bi-database text-primary fs-4"></i>
                                    <div class="fw-bold mt-1">MySQL / MariaDB</div>
                                    <div class="small text-muted">Base de datos relacional normalizada con integridad referencial.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3" style="background:var(--gris-50);">
                                    <i class="bi bi-bootstrap text-primary fs-4"></i>
                                    <div class="fw-bold mt-1">Bootstrap 5</div>
                                    <div class="small text-muted">Interfaz responsive con recursos locales.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3" style="background:var(--gris-50);">
                                    <i class="bi bi-bar-chart text-primary fs-4"></i>
                                    <div class="fw-bold mt-1">Chart.js</div>
                                    <div class="small text-muted">Visualización de estadísticas calculadas en tiempo real.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><i class="bi bi-building me-1"></i> Información institucional</div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item"><strong><?= e($app['name']) ?></strong><br><?= e($app['municipio']) ?>, <?= e($app['departamento']) ?></li>
                        <li class="list-group-item"><i class="bi bi-geo-alt me-1 text-primary"></i><?= e($app['direccion']) ?></li>
                        <li class="list-group-item"><i class="bi bi-telephone me-1 text-primary"></i><?= e($app['telefono']) ?></li>
                        <li class="list-group-item"><i class="bi bi-envelope me-1 text-primary"></i><?= e($app['email']) ?></li>
                        <li class="list-group-item"><i class="bi bi-clock me-1 text-primary"></i><?= e($app['horario']) ?></li>
                    </ul>
                </div>
                <div class="card">
                    <div class="card-header"><i class="bi bi-shield-lock me-1"></i> Roles del sistema</div>
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <span class="user-avatar">C</span>
                            <div><div class="fw-bold small">Ciudadano</div><div class="small text-muted">Radica y consulta sus solicitudes.</div></div>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <span class="user-avatar" style="background:linear-gradient(145deg,var(--verde),#0a5c2e);">F</span>
                            <div><div class="fw-bold small">Funcionario</div><div class="small text-muted">Atiende y responde las solicitudes asignadas.</div></div>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="user-avatar" style="background:linear-gradient(145deg,#475569,#1e293b);">A</span>
                            <div><div class="fw-bold small">Administrador</div><div class="small text-muted">Administra usuarios, trámites y reportes.</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
