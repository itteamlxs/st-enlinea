<?php
// modules/Catalogo/src/Repositories/ProductoRepository.php
declare(strict_types=1);

namespace Modules\Catalogo\Repositories;

use PDO;
use Modules\Catalogo\Exceptions\ProductoException;

class ProductoRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findAll(int $limit = 10, int $offset = 0): array {
        $stmt = $this->db->prepare("
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
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $this->procesarProductos($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function count(): int {
        $stmt = $this->db->query("
            SELECT COUNT(*) 
            FROM productos
            WHERE categoria_id IS NOT NULL
        ");
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                c.nombre AS categoria_nombre,
                (SELECT GROUP_CONCAT(ip.url_imagen SEPARATOR ',') 
                 FROM imagenes_productos ip
                 WHERE ip.producto_id = p.producto_id) AS imagenes
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
            WHERE p.producto_id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $this->procesarProducto($resultado) : null;
    }

    private function procesarProductos(array $productos): array {
        return array_map([$this, 'procesarProducto'], $productos);
    }

    private function procesarProducto(array $producto): array {
        return [
            'producto_id' => (int)$producto['producto_id'],
            'sku' => $producto['sku'],
            'nombre' => $producto['nombre'],
            'descripcion' => $producto['descripcion'],
            'precio' => (float)$producto['precio'],
            'stock' => (int)$producto['stock'],
            'peso' => $producto['peso'] ? (float)$producto['peso'] : null,
            'dimensiones' => $producto['dimensiones'],
            'fecha_creacion' => $producto['fecha_creacion'],
            'fecha_actualizacion' => $producto['fecha_actualizacion'],
            'categoria_nombre' => $producto['categoria_nombre'] ?? 'Sin categoría',
            'imagenes' => !empty($producto['imagenes']) ? explode(',', $producto['imagenes']) : []
        ];
    }
}