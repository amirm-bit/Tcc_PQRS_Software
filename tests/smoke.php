<?php
// tests/smoke.php - Prueba de humo por línea de comandos
// Uso: php tests/smoke.php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';

require BASE_PATH . '/app/core/bootstrap.php';

$fallos = 0;
$total = 0;

function probar(string $nombre, callable $fn): void {
    global $fallos, $total;
    $total++;
    try {
        $resultado = $fn();
        if ($resultado === true) {
            echo "[OK]   {$nombre}\n";
        } else {
            $fallos++;
            echo "[FALLA] {$nombre}" . (is_string($resultado) ? " -> {$resultado}" : '') . "\n";
        }
    } catch (Throwable $e) {
        $fallos++;
        echo "[ERROR] {$nombre} -> " . $e->getMessage() . "\n";
    }
}

echo "Prueba de humo — Sistema de Trámites y PQRS\n";
echo str_repeat('-', 52) . "\n";

probar('Conexión a la base de datos', function () {
    $db = Database::getConnection();
    return $db instanceof PDO;
});

probar('Tablas requeridas presentes', function () {
    $db = Database::getConnection();
    $requeridas = [
        'roles', 'usuarios', 'dependencias', 'ciudadanos', 'funcionarios',
        'tipos_pqrs', 'estados_pqrs', 'pqrs', 'seguimiento_pqrs', 'respuestas_pqrs',
        'tipos_tramites', 'estados_tramites', 'solicitudes_tramites',
        'seguimiento_tramites', 'archivos_adjuntos', 'notificaciones', 'auditoria',
    ];
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t");
    $faltantes = [];
    foreach ($requeridas as $tabla) {
        $stmt->execute(['t' => $tabla]);
        if ((int)$stmt->fetchColumn() === 0) {
            $faltantes[] = $tabla;
        }
    }
    return $faltantes ? 'Faltan: ' . implode(', ', $faltantes) : true;
});

probar('Columna tipos_tramites.detalle_campos', function () {
    $db = Database::getConnection();
    $stmt = $db->query("SHOW COLUMNS FROM tipos_tramites LIKE 'detalle_campos'");
    return $stmt->fetch() !== false;
});

probar('Datos de demostración cargados', function () {
    $db = Database::getConnection();
    $roles = (int)$db->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    $usuarios = (int)$db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $pqrs = (int)$db->query("SELECT COUNT(*) FROM pqrs")->fetchColumn();
    $tramites = (int)$db->query("SELECT COUNT(*) FROM solicitudes_tramites")->fetchColumn();
    if ($roles !== 3) return "roles esperados 3, encontrados {$roles}";
    if ($usuarios < 3) return "usuarios insuficientes ({$usuarios})";
    if ($pqrs < 1) return 'no hay PQRS de ejemplo';
    if ($tramites < 1) return 'no hay trámites de ejemplo';
    return true;
});

probar('Contraseñas BCRYPT de demostración', function () {
    $db = Database::getConnection();
    $esperados = [
        'admin@lorica.test'       => 'Admin123!',
        'funcionario@lorica.test' => 'Funcionario123!',
        'ciudadano@lorica.test'   => 'Ciudadano123!',
    ];
    $stmt = $db->prepare("SELECT password FROM usuarios WHERE email = :e LIMIT 1");
    foreach ($esperados as $email => $clave) {
        $stmt->execute(['e' => $email]);
        $hash = $stmt->fetchColumn();
        if (!$hash || !password_verify($clave, $hash)) {
            return "no coincide la contraseña de {$email}";
        }
    }
    return true;
});

probar('Vistas esenciales presentes', function () {
    $vistas = [
        'layouts/public', 'layouts/auth', 'layouts/panel',
        'public/home', 'public/servicios', 'public/consulta',
        'auth/login', 'auth/register', 'auth/forgot',
        'ciudadano/dashboard', 'ciudadano/pqrs_crear', 'ciudadano/pqrs_detalle',
        'ciudadano/tramites_index', 'ciudadano/tramite_solicitar', 'ciudadano/tramite_detalle',
        'funcionario/dashboard', 'funcionario/pqrs_gestionar', 'funcionario/tramite_gestionar',
        'admin/dashboard', 'admin/usuarios', 'admin/pqrs_detalle', 'admin/reportes',
    ];
    $faltantes = [];
    foreach ($vistas as $v) {
        if (!is_file(BASE_PATH . '/app/views/' . $v . '.php')) {
            $faltantes[] = $v;
        }
    }
    return $faltantes ? 'Faltan: ' . implode(', ', $faltantes) : true;
});

probar('Almacenamiento de adjuntos escribible', function () {
    foreach (['pqrs', 'tramites', 'respuestas'] as $dir) {
        $ruta = BASE_PATH . '/storage/uploads/' . $dir;
        if (!is_dir($ruta)) return "No existe {$dir}";
        if (!is_writable($ruta)) return "Sin permisos de escritura en {$dir}";
    }
    return true;
});

probar('Configuración de carga de archivos', function () {
    $cfg = config('uploads');
    return is_array($cfg) && !empty($cfg['allowed_types']) && ($cfg['max_size'] ?? 0) > 0;
});

$db = Database::getConnection();
$roles = $db->query("SELECT nombre FROM roles ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
probar('Roles del sistema', function () use ($roles) {
    return $roles === ['admin', 'funcionario', 'ciudadano'] ? true : 'Roles inesperados: ' . implode(', ', $roles);
});

echo str_repeat('-', 52) . "\n";
echo ($fallos === 0 ? "RESULTADO: {$total} pruebas superadas.\n" : "RESULTADO: {$fallos} de {$total} pruebas fallaron.\n");

exit($fallos === 0 ? 0 : 1);
