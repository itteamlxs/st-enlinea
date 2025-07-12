<?php
// modules/Catalogo/config/database.php
declare(strict_types=1);

// Ruta corregida para autoload.php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar .env desde la raíz del proyecto
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../..');
$dotenv->safeLoad();

function getDatabaseConnection(): PDO {
    try {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? 'tienda_online';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';

        return new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        throw new RuntimeException("Error al conectar con la base de datos");
    }
}