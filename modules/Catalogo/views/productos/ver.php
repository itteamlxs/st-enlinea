<?php
// modules/Catalogo/views/productos/ver.php

// Verificación explícita de la variable
if (!isset($producto) || !is_array($producto)) {
    header("Location: productos.php?action=listar");
    exit;
}

// Función helper para escape seguro
function e($value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Valores por defecto para evitar warnings
$producto['nombre'] = $producto['nombre'] ?? 'Producto sin nombre';
$producto['precio'] = $producto['precio'] ?? 0;
$producto['descripcion'] = $producto['descripcion'] ?? '';
$producto['imagenes'] = $producto['imagenes'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($producto['nombre']) ?> | Catálogo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($producto['imagenes'][0])): ?>
                <img src="<?= e($producto['imagenes'][0]) ?>" 
                     class="img-fluid rounded" 
                     alt="<?= e($producto['nombre']) ?>"
                     style="max-height: 400px; object-fit: contain;">
                <?php else: ?>
                <div class="bg-secondary text-white text-center p-5 rounded">
                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                    <p class="mt-2">Imagen no disponible</p>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h1><?= e($producto['nombre']) ?></h1>
                <p class="text-success fs-3 fw-bold">
                    $<?= number_format((float)$producto['precio'], 2) ?>
                </p>
                
                <?php if (!empty($producto['descripcion'])): ?>
                <div class="mt-4">
                    <h4>Descripción</h4>
                    <p class="text-muted"><?= nl2br(e($producto['descripcion'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="mt-4 d-flex gap-2">
                    <a href="productos.php?action=listar" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Añadir al carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>