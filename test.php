<?php
require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: text/plain; charset=utf-8');

function testDatabaseConnection() {
    try {
        // Configuración de conexión
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        
        // Crear conexión PDO
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        echo "✅ Conexión exitosa a la base de datos\n";
        echo "---\n";
        
        // Verificar tablas existentes
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "⚠️ No se encontraron tablas en la base de datos\n";
        } else {
            echo "📊 Tablas existentes (" . count($tables) . "):\n";
            foreach ($tables as $table) {
                echo "- $table\n";
            }
            
            echo "---\n";
            
            // Verificar datos de ejemplo
            $testTables = ['usuarios', 'productos', 'categorias'];
            foreach ($testTables as $table) {
                if (in_array($table, $tables)) {
                    $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                    echo "🔄 $table: $count registros\n";
                }
            }
        }
        
        // Prueba CRUD básica
        echo "---\n🧪 Prueba CRUD básica:\n";
        
        // 1. Crear registro de prueba
        $testName = 'Test_' . bin2hex(random_bytes(3));
        $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)")
            ->execute([$testName]);
        $id = $pdo->lastInsertId();
        echo "➕ Creada categoría prueba (ID: $id)\n";
        
        // 2. Leer registro
        $categoria = $pdo->prepare("SELECT * FROM categorias WHERE categoria_id = ?");
        $categoria->execute([$id]);
        $data = $categoria->fetch();
        echo "🔍 Categoría leída: " . ($data['nombre'] ?? 'No encontrada') . "\n";
        
        // 3. Eliminar registro
        $pdo->prepare("DELETE FROM categorias WHERE categoria_id = ?")
            ->execute([$id]);
        echo "🗑️ Categoría eliminada\n";
        
    } catch (PDOException $e) {
        echo "❌ Error de conexión: " . $e->getMessage() . "\n";
        echo "Detalles:\n";
        echo "- Host: " . $_ENV['DB_HOST'] . "\n";
        echo "- Usuario: " . $_ENV['DB_USER'] . "\n";
        echo "- Base de datos: " . $_ENV['DB_NAME'] . "\n";
        
        // Sugerencias para solucionar problemas
        echo "\n🔧 Solución de problemas:\n";
        echo "1. Verifica que MySQL/MariaDB esté corriendo\n";
        echo "2. Confirma las credenciales en .env\n";
        echo "3. Comprueba los permisos del usuario de la BD\n";
        echo "4. Intenta conectar manualmente: mysql -u {usuario} -p{contraseña} -h {host} {bd}\n";
    }
}

// Ejecutar prueba
echo "🔍 Iniciando prueba de conexión a BD...\n";
echo "📌 Usando archivo .env: " . realpath(__DIR__ . '/.env') . "\n\n";

testDatabaseConnection();

echo "\nPrueba completada 🚀";