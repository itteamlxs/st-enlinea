-- CREACIÓN DE LA BASE DE DATOS
CREATE DATABASE IF NOT EXISTS tienda_online 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tienda_online;

-- TABLA USUARIOS (con trigger para validación de email)
CREATE TABLE usuarios (
    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

DELIMITER //
CREATE TRIGGER tr_valida_email BEFORE INSERT ON usuarios
FOR EACH ROW
BEGIN
    IF NEW.email NOT LIKE '%@%.%' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Formato de email inválido: debe contener @ y al menos un punto después';
    END IF;
END//
DELIMITER ;

-- TABLA CATEGORÍAS (con restricción autorelacionada)
CREATE TABLE categorias (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    padre_id INT NULL
) ENGINE=InnoDB;

ALTER TABLE categorias 
ADD CONSTRAINT fk_categoria_padre 
FOREIGN KEY (padre_id) REFERENCES categorias(categoria_id) 
ON DELETE SET NULL;

-- TABLA PRODUCTOS (con validación vía trigger)
CREATE TABLE productos (
    producto_id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    peso DECIMAL(10,2),
    dimensiones VARCHAR(50),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

DELIMITER //
CREATE TRIGGER tr_valida_producto BEFORE INSERT ON productos
FOR EACH ROW
BEGIN
    IF NEW.precio <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El precio debe ser mayor que cero';
    END IF;
    
    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El stock no puede ser negativo';
    END IF;
END//
DELIMITER ;

ALTER TABLE productos
ADD CONSTRAINT fk_producto_categoria
FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id);

-- TABLA IMÁGENES DE PRODUCTOS
CREATE TABLE imagenes_productos (
    imagen_id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    url_imagen VARCHAR(255) NOT NULL,
    orden INT DEFAULT 0,
    es_principal BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB;

ALTER TABLE imagenes_productos
ADD CONSTRAINT fk_imagen_producto
FOREIGN KEY (producto_id) REFERENCES productos(producto_id)
ON DELETE CASCADE;

-- TABLA CARRITOS (soporta compras sin registro)
CREATE TABLE carritos (
    carrito_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    sesion_id VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE carritos
ADD CONSTRAINT fk_carrito_usuario
FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
ON DELETE CASCADE;

-- TABLA ITEMS DEL CARRITO
CREATE TABLE items_carrito (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    carrito_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB;

DELIMITER //
CREATE TRIGGER tr_valida_item_carrito BEFORE INSERT ON items_carrito
FOR EACH ROW
BEGIN
    IF NEW.cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor que cero';
    END IF;
    
    IF NEW.precio_unitario <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'El precio unitario debe ser mayor que cero';
    END IF;
END//
DELIMITER ;

ALTER TABLE items_carrito
ADD CONSTRAINT fk_item_carrito
FOREIGN KEY (carrito_id) REFERENCES carritos(carrito_id)
ON DELETE CASCADE;

ALTER TABLE items_carrito
ADD CONSTRAINT fk_item_producto
FOREIGN KEY (producto_id) REFERENCES productos(producto_id);

-- TABLA PEDIDOS
CREATE TABLE pedidos (
    pedido_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'procesando', 'enviado', 'completado', 'cancelado') DEFAULT 'pendiente',
    total DECIMAL(12,2) NOT NULL,
    direccion_envio TEXT NOT NULL,
    metodo_pago VARCHAR(50)
) ENGINE=InnoDB;

ALTER TABLE pedidos
ADD CONSTRAINT fk_pedido_usuario
FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id);

-- TABLA DETALLES DE PEDIDO
CREATE TABLE detalles_pedido (
    detalle_id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB;

ALTER TABLE detalles_pedido
ADD CONSTRAINT fk_detalle_pedido
FOREIGN KEY (pedido_id) REFERENCES pedidos(pedido_id)
ON DELETE CASCADE;

ALTER TABLE detalles_pedido
ADD CONSTRAINT fk_detalle_producto
FOREIGN KEY (producto_id) REFERENCES productos(producto_id);

-- TABLA LOGS (para auditoría)
CREATE TABLE logs_eventos (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_evento VARCHAR(50) NOT NULL,
    usuario_id INT NULL,
    ip_origen VARCHAR(45),
    descripcion TEXT,
    fecha_evento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE logs_eventos
ADD CONSTRAINT fk_log_usuario
FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
ON DELETE SET NULL;

-- TABLA CONFIGURACIONES
CREATE TABLE configuraciones (
    config_id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo VARCHAR(20) DEFAULT 'string'
) ENGINE=InnoDB;

-- INSERTS INICIALES (datos básicos)
INSERT INTO categorias (nombre, descripcion) VALUES
('Electrónicos', 'Dispositivos electrónicos y gadgets'),
('Ropa', 'Prendas de vestir para todas las edades'),
('Hogar', 'Artículos para el hogar y decoración');

INSERT INTO configuraciones (clave, valor, tipo) VALUES
('tienda_nombre', 'Mi Tienda Online', 'string'),
('tienda_moneda', 'MXN', 'string'),
('items_por_pagina', '12', 'number'),
('mantenimiento_mode', 'false', 'boolean');