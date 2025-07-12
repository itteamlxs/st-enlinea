<?php
// modules/Catalogo/public/productos.php
declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Modules\Catalogo\Repositories\ProductoRepository;

try {
    $db = getDatabaseConnection();
    $repo = new ProductoRepository($db);

    $action = $_GET['action'] ?? 'listar';
    
    if ($action === 'listar') {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $productos = $repo->findAll($limit, $offset);
        $total = $repo->count();
        $pages = max(1, ceil($total / $limit));
        
        require __DIR__ . '/../views/productos/listar.php';
    } elseif ($action === 'ver') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);
        
        if (!$id) {
            throw new InvalidArgumentException("ID de producto inválido", 400);
        }
        
        $producto = $repo->findById($id);
        if (!$producto) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            exit;
        }
        
        require __DIR__ . '/../views/productos/ver.php';
    } else {
        throw new InvalidArgumentException("Acción no válida", 400);
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    require __DIR__ . '/../views/errors/500.php';
    exit;
}