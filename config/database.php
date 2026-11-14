<?php
// config/database.php - Configuración de conexión a MySQL vía PDO

return [
    'host'      => '127.0.0.1',
    'port'      => 3306,
    'database'  => 'alcaldia_lorica',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
