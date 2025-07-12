<?php
// modules/Catalogo/views/errors/500.php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error del servidor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5 text-center">
        <h1 class="display-1 text-danger">500</h1>
        <p class="lead">Error interno del servidor</p>
        <a href="productos.php" class="btn btn-primary">Volver al catálogo</a>
    </div>
</body>
</html>