<?php $app = config(); ?>
<section class="py-5" style="background:linear-gradient(135deg,var(--azul-oscuro),var(--azul));">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 text-white">
                <span class="hero-chip"><i class="bi bi-search"></i> Consulta pública</span>
                <h1 class="h2 text-white">Consulte el estado de su radicado</h1>
                <p class="text-white-50 mb-0">Ingrese el número de radicado de su PQRS o trámite para conocer el estado y el seguimiento registrado.</p>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <form action="<?= url('consultar-radicado') ?>" method="post" novalidate>
                            <?= Csrf::field() ?>
                            <label class="form-label" for="radicado">Número de radicado</label>
                            <div class="input-group input-group-lg mb-3">
                                <span class="input-group-text bg-white"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control" id="radicado" name="radicado" placeholder="PQRS-2026-000001" required autofocus>
                            </div>
                            <div class="form-text mb-3">Formato PQRS-AAAA-000000 o TRAM-AAAA-000000</div>
                            <button class="btn btn-primary btn-lg w-100"><i class="bi bi-search me-1"></i> Consultar radicado</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card h-100 text-center">
                    <div class="feature-icon mx-auto"><i class="bi bi-1-circle"></i></div>
                    <h6 class="fw-bold">Ingrese su radicado</h6>
                    <p class="small text-muted mb-0">Use el número entregado al momento de radicar su solicitud.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100 text-center">
                    <div class="feature-icon green mx-auto"><i class="bi bi-2-circle"></i></div>
                    <h6 class="fw-bold">Verifique el estado</h6>
                    <p class="small text-muted mb-0">Consulte el avance, observaciones públicas y respuestas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100 text-center">
                    <div class="feature-icon mx-auto"><i class="bi bi-3-circle"></i></div>
                    <h6 class="fw-bold">Acceda a su cuenta</h6>
                    <p class="small text-muted mb-0">Para descargar documentos, inicie sesión como ciudadano.</p>
                </div>
            </div>
        </div>
    </div>
</section>
