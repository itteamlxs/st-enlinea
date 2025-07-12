<?php
// modules/Catalogo/public/productos.php
declare(strict_types=1);

// Cargar autoload global desde la raíz del proyecto
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Modules\Catalogo\Repositories\ProductoRepository;

try {
    $db = getDatabaseConnection();
    $repo = new ProductoRepository($db);

    $action = $_GET['action'] ?? 'listar';

    switch ($action) {
        case 'listar':
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $productos = $repo->findAll($limit, $offset);
            $total = $repo->count();
            $pages = max(1, ceil($total / $limit));

            require __DIR__ . '/../views/productos/listar.php';
            exit;

        case 'ver':
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
            exit;

        default:
            throw new InvalidArgumentException("Acción no válida", 400);
    }

} catch (Throwable $e) {
    error_log("Error: " . $e->getMessage());

    $code = $e->getCode();
    if (!is_int($code) || $code < 400 || $code > 599) {
        $code = 500;
    }

    http_response_code($code);
    require __DIR__ . '/../views/errors/500.php';
    exit;
}
