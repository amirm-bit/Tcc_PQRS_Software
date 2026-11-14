<?php
// app/models/Database.php - Conexión Singleton PDO segura para MySQL

class Database {
    private static ?PDO $instance = null;
    private static ?string $connectionError = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                self::$connectionError = $e->getMessage();
                // Si la BD aún no existe, intentar conexión al servidor para informar o diagnosticar
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new Exception("Error al conectar con la base de datos MySQL (alcaldia_lorica). Verifique que MySQL esté iniciado en XAMPP. Detalle técnico: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function getRawServerConnection(): PDO {
        $config = require __DIR__ . '/../../config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
        return new PDO($dsn, $config['username'], $config['password'], $config['options']);
    }

    public static function getLastError(): ?string {
        return self::$connectionError;
    }
}
