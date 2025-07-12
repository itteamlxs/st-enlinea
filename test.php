<?php
// debug_productos.php - Coloca este archivo en la raíz del proyecto
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/modules/Catalogo/config/database.php';

header('Content-Type: text/plain; charset=utf-8');

echo "🔍 DIAGNÓSTICO DE PRODUCTOS\n";
echo "=========================\n\n";

try {
    $db = getDatabaseConnection();
    echo "✅ Conexión a BD exitosa\n\n";
    
    // 1. Verificar productos en BD
    echo "📊 VERIFICANDO PRODUCTOS EN BD:\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM productos");
    $total = $stmt->fetch()['total'];
    echo "Total productos: $total\n";
    
    if ($total > 0) {
        echo "\n📋 PRIMEROS 5 PRODUCTOS:\n";
        $stmt = $db->query("SELECT producto_id, nombre, precio, categoria_id FROM productos LIMIT 5");
        while ($row = $stmt->fetch()) {
            echo "- ID: {$row['producto_id']}, Nombre: {$row['nombre']}, Precio: {$row['precio']}, Categoria: {$row['categoria_id']}\n";
        }
    }
    
    // 2. Verificar categorías
    echo "\n📊 VERIFICANDO CATEGORÍAS:\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM categorias");
    $totalCat = $stmt->fetch()['total'];
    echo "Total categorías: $totalCat\n";
    
    if ($totalCat > 0) {
        echo "\n📋 CATEGORÍAS EXISTENTES:\n";
        $stmt = $db->query("SELECT categoria_id, nombre FROM categorias");
        while ($row = $stmt->fetch()) {
            echo "- ID: {$row['categoria_id']}, Nombre: {$row['nombre']}\n";
        }
    }
    
    // 3. Verificar relación productos-categorías
    echo "\n🔗 VERIFICANDO RELACIÓN PRODUCTOS-CATEGORÍAS:\n";
    $stmt = $db->query("
        SELECT 
            p.producto_id, 
            p.nombre as producto_nombre, 
            p.categoria_id,
            c.nombre as categoria_nombre
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
        LIMIT 5
    ");
    
    $productos = $stmt->fetchAll();
    echo "Productos encontrados: " . count($productos) . "\n";
    
    foreach ($productos as $prod) {
        echo "- Producto: {$prod['producto_nombre']}, Categoría: " . ($prod['categoria_nombre'] ?? 'SIN CATEGORÍA') . "\n";
    }
    
    // 4. Probar la consulta exacta del repositorio
    echo "\n🧪 PROBANDO CONSULTA DEL REPOSITORIO:\n";
    $stmt = $db->prepare("
        SELECT 
            p.*,
            c.nombre AS categoria_nombre,
            (SELECT GROUP_CONCAT(ip.url_imagen SEPARATOR ',') 
             FROM imagenes_productos ip
             WHERE ip.producto_id = p.producto_id) AS imagenes
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
        WHERE p.categoria_id IS NOT NULL
        ORDER BY p.fecha_creacion DESC
        LIMIT 10 OFFSET 0
    ");
    $stmt->execute();
    
    $resultados = $stmt->fetchAll();
    echo "Resultados de la consulta del repositorio: " . count($resultados) . "\n";
    
    if (empty($resultados)) {
        echo "⚠️  LA CONSULTA NO DEVUELVE RESULTADOS\n";
        echo "Posibles causas:\n";
        echo "1. Los productos no tienen categoria_id válido\n";
        echo "2. Las categorías no existen\n";
        echo "3. Problema con la consulta JOIN\n";
        
        // Verificar productos sin categoría válida
        echo "\n🔍 VERIFICANDO PRODUCTOS SIN CATEGORÍA VÁLIDA:\n";
        $stmt = $db->query("
            SELECT p.producto_id, p.nombre, p.categoria_id
            FROM productos p
            WHERE p.categoria_id IS NULL 
               OR p.categoria_id NOT IN (SELECT categoria_id FROM categorias)
        ");
        $sinCategoria = $stmt->fetchAll();
        
        if (!empty($sinCategoria)) {
            echo "Productos sin categoría válida:\n";
            foreach ($sinCategoria as $prod) {
                echo "- ID: {$prod['producto_id']}, Nombre: {$prod['nombre']}, Categoría ID: {$prod['categoria_id']}\n";
            }
        } else {
            echo "Todos los productos tienen categoría válida.\n";
        }
    } else {
        echo "✅ La consulta funciona correctamente\n";
        foreach ($resultados as $prod) {
            echo "- {$prod['nombre']} - {$prod['categoria_nombre']}\n";
        }
    }
    
    // 5. Verificar autoload
    echo "\n📦 VERIFICANDO AUTOLOAD:\n";
    if (class_exists('Modules\Catalogo\Repositories\ProductoRepository')) {
        echo "✅ ProductoRepository se puede cargar\n";
    } else {
        echo "❌ ProductoRepository NO se puede cargar\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}

echo "\n🚀 Diagnóstico completado\n";