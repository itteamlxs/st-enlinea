<?php
// modules/Catalogo/views/errors/404.php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5 text-center">
        <h1 class="display-1 text-danger">404</h1>
        <p class="lead">Producto no encontrado</p>
        <a href="productos.php" class="btn btn-primary">Volver al catálogo</a>
    </div>
</body>
</html>