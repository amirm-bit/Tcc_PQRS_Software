// public/assets/js/app.js - Comportamientos generales de la interfaz

document.addEventListener('DOMContentLoaded', function () {
    // Sidebar móvil
    const sidebar = document.getElementById('panelSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
    }
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        });
    }
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    // Confirmación para acciones destructivas/importantes
    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const mensaje = form.getAttribute('data-confirm') || '¿Confirma esta acción?';
            if (!window.confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });

    // Auto-ocultar alertas después de 7 segundos
    document.querySelectorAll('.alert.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 7000);
    });

    // Copiar radicado al portapapeles
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const text = btn.getAttribute('data-copy');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function () {
                    const original = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check2"></i> Copiado';
                    setTimeout(function () { btn.innerHTML = original; }, 1800);
                });
            }
        });
    });

    // Mostrar nombre de archivo seleccionado
    document.querySelectorAll('input[type="file"][data-file-label]').forEach(function (input) {
        input.addEventListener('change', function () {
            const target = document.getElementById(input.getAttribute('data-file-label'));
            if (!target) return;
            if (input.files && input.files.length > 0) {
                const names = Array.from(input.files).map(f => f.name).join(', ');
                target.textContent = names;
            } else {
                target.textContent = 'Ningún archivo seleccionado';
            }
        });
    });

    // Efecto de filtros persistentes en selects
    document.querySelectorAll('form[data-auto-submit] select').forEach(function (select) {
        select.addEventListener('change', function () {
            select.closest('form').submit();
        });
    });

    // Gráficas declarativas con Chart.js
    // Uso: <div class="chart-box"><canvas></canvas><script type="application/json" class="chart-data">{...}</script></div>
    if (window.Chart) {
        const paleta = ['#0b3d91', '#0f7b3f', '#1e56b3', '#c8a24a', '#64748b', '#b45309', '#b91c1c', '#0891b2', '#7c3aed', '#475569'];
        document.querySelectorAll('.chart-box').forEach(function (box) {
            const canvas = box.querySelector('canvas');
            const dataEl = box.querySelector('.chart-data');
            if (!canvas || !dataEl) return;

            let cfg;
            try { cfg = JSON.parse(dataEl.textContent); } catch (e) { return; }
            const type = cfg.type || 'bar';
            const labels = cfg.labels || [];
            const values = cfg.values || [];
            const colors = cfg.colors || labels.map((_, i) => paleta[i % paleta.length]);

            const isCircular = type === 'doughnut' || type === 'pie' || type === 'polarArea';
            const dataset = {
                label: cfg.label || 'Total',
                data: values,
                backgroundColor: isCircular ? colors : colors.map(c => c + 'CC'),
                borderColor: isCircular ? '#fff' : colors,
                borderWidth: isCircular ? 2 : 1,
                borderRadius: isCircular ? 0 : 6,
                maxBarThickness: 34,
                tension: 0.35,
                fill: cfg.fill === true
            };

            new Chart(canvas.getContext('2d'), {
                type: type,
                data: { labels: labels, datasets: [dataset] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: cfg.legend !== false,
                            position: isCircular ? 'bottom' : 'top',
                            labels: { boxWidth: 12, boxHeight: 12, usePointStyle: true, font: { size: 11 } }
                        },
                        tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 }
                    },
                    scales: isCircular ? {} : {
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#5b6b82' } },
                        y: { beginAtZero: true, grid: { color: '#eef1f6' }, ticks: { precision: 0, font: { size: 11 }, color: '#5b6b82' } }
                    }
                }
            });
        });
    }
});
