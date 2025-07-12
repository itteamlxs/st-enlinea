<?php
// modules/Catalogo/views/productos/listar.php

// Verificación y asignación segura de variables
$productos = $productos ?? [];
$total = $total ?? 0;
$page = $page ?? 1;
$pages = $pages ?? 1;

// Función helper para escape seguro
function e($value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo | <?= e($_ENV['APP_NAME'] ?? 'Tienda') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-5 fw-bold text-primary">
                    Catálogo de Productos
                </h1>
                <p class="text-muted">Mostrando <?= count($productos) ?> de <?= $total ?> productos</p>
            </div>
        </div>

        <?php if (!empty($productos)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($productos as $prod): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($prod['imagenes'][0])): ?>
                    <img src="<?= e($prod['imagenes'][0]) ?>" class="card-img-top p-3" 
                         alt="<?= e($prod['nombre']) ?>" style="height: 200px; object-fit: contain;">
                    <?php else: ?>
                    <div class="text-center py-5 bg-light">
                        <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?= e($prod['nombre']) ?></h5>
                        <p class="text-success fw-bold">$<?= number_format($prod['precio'], 2) ?></p>
                        <a href="productos.php?action=ver&id=<?= $prod['producto_id'] ?>" 
                           class="btn btn-sm btn-outline-primary">
                            Ver Detalle
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-warning">
            No se encontraron productos.
        </div>
        <?php endif; ?>
    </div>
</body>
</html>