<?php
// modules/Catalogo/src/Entities/Producto.php
declare(strict_types=1);

namespace Modules\Catalogo\Entities;

class Producto
{
    private int $producto_id;
    private int $categoria_id;
    private string $sku;
    private string $nombre;
    private ?string $descripcion;
    private float $precio;
    private int $stock;
    private ?float $peso;
    private ?string $dimensiones;
    private string $fecha_creacion;
    private string $fecha_actualizacion;
    private ?string $categoria_nombre;
    private array $imagenes;

    public function __construct(array $data)
    {
        $this->producto_id = (int)$data['producto_id'];
        $this->categoria_id = (int)$data['categoria_id'];
        $this->sku = $data['sku'];
        $this->nombre = $data['nombre'];
        $this->descripcion = $data['descripcion'] ?? null;
        $this->precio = (float)$data['precio'];
        $this->stock = (int)$data['stock'];
        $this->peso = isset($data['peso']) ? (float)$data['peso'] : null;
        $this->dimensiones = $data['dimensiones'] ?? null;
        $this->fecha_creacion = $data['fecha_creacion'];
        $this->fecha_actualizacion = $data['fecha_actualizacion'];
        $this->categoria_nombre = $data['categoria_nombre'] ?? null;
        $this->imagenes = $data['imagenes'] ?? [];
    }

    // Getters
    public function getProductoId(): int
    {
        return $this->producto_id;
    }

    public function getCategoriaId(): int
    {
        return $this->categoria_id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function getDimensiones(): ?string
    {
        return $this->dimensiones;
    }

    public function getFechaCreacion(): string
    {
        return $this->fecha_creacion;
    }

    public function getFechaActualizacion(): string
    {
        return $this->fecha_actualizacion;
    }

    public function getCategoriaNombre(): ?string
    {
        return $this->categoria_nombre;
    }

    public function getImagenes(): array
    {
        return $this->imagenes;
    }

    public function getPrimerImagen(): ?string
    {
        return !empty($this->imagenes) ? $this->imagenes[0] : null;
    }

    public function toArray(): array
    {
        return [
            'producto_id' => $this->producto_id,
            'categoria_id' => $this->categoria_id,
            'sku' => $this->sku,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'peso' => $this->peso,
            'dimensiones' => $this->dimensiones,
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_actualizacion' => $this->fecha_actualizacion,
            'categoria_nombre' => $this->categoria_nombre,
            'imagenes' => $this->imagenes
        ];
    }

    public function estaDisponible(): bool
    {
        return $this->stock > 0;
    }

    public function getPrecioFormateado(): string
    {
        return '$' . number_format($this->precio, 2);
    }
}