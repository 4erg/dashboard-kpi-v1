CREATE DATABASE IF NOT EXISTS dashboard_kpi;
USE dashboard_kpi;

DROP TABLE IF EXISTS ventas;

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(100),
    producto VARCHAR(100),
    categoria VARCHAR(50),
    cantidad INT DEFAULT 1,
    precio_unitario DECIMAL(10,2),
    total DECIMAL(10,2),
    fecha DATE,
    vendedor VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO ventas (cliente, producto, categoria, cantidad, precio_unitario, total, fecha, vendedor) VALUES
('Juan Pérez', 'Laptop Gamer', 'Electrónica', 2, 850.00, 1700.00, '2026-06-15', 'Carlos'),
('María López', 'Mouse Inalámbrico', 'Accesorios', 3, 25.00, 75.00, '2026-06-14', 'Ana'),
('Pedro Gómez', 'Teclado Mecánico', 'Accesorios', 1, 89.00, 89.00, '2026-06-14', 'Carlos'),
('Laura Díaz', 'Monitor 24"', 'Electrónica', 1, 199.00, 199.00, '2026-06-13', 'Ana'),
('Carlos Ruiz', 'Silla Gamer', 'Muebles', 1, 299.00, 299.00, '2026-06-12', 'Luis'),
('Sofía Castro', 'Webcam HD', 'Electrónica', 2, 45.00, 90.00, '2026-06-11', 'Carlos'),
('Javier Mora', 'SSD 1TB', 'Componentes', 2, 120.00, 240.00, '2026-06-10', 'Ana'),
('Valeria Soto', 'Headset RGB', 'Audio', 1, 65.00, 65.00, '2026-06-09', 'Luis'),
('Andrés Vega', 'Router WiFi', 'Redes', 1, 89.00, 89.00, '2026-06-08', 'Carlos'),
('Camila Rojas', 'Tablet', 'Electrónica', 1, 149.00, 149.00, '2026-06-07', 'Ana'),
('Juan Pérez', 'Laptop Gamer', 'Electrónica', 1, 850.00, 850.00, '2026-05-20', 'Carlos'),
('María López', 'Monitor 24"', 'Electrónica', 2, 199.00, 398.00, '2026-05-15', 'Ana'),
('Pedro Gómez', 'Mouse Gamer', 'Accesorios', 5, 35.00, 175.00, '2026-05-10', 'Luis'),
('Laura Díaz', 'Teclado Mecánico', 'Accesorios', 2, 89.00, 178.00, '2026-04-25', 'Carlos'),
('Carlos Ruiz', 'Laptop Gamer', 'Electrónica', 1, 850.00, 850.00, '2026-04-18', 'Ana'),
('Sofía Castro', 'Silla Gamer', 'Muebles', 1, 299.00, 299.00, '2026-04-12', 'Luis'),
('Javier Mora', 'Webcam HD', 'Electrónica', 3, 45.00, 135.00, '2026-03-28', 'Carlos'),
('Valeria Soto', 'SSD 1TB', 'Componentes', 1, 120.00, 120.00, '2026-03-20', 'Ana'),
('Andrés Vega', 'Headset RGB', 'Audio', 2, 65.00, 130.00, '2026-03-15', 'Luis'),
('Camila Rojas', 'Router WiFi', 'Redes', 1, 89.00, 89.00, '2026-02-10', 'Carlos'),
('Rosa Flores', 'Impresora Epson', 'Oficina', 1, 320.00, 320.00, CURDATE(), 'Ana'),
('Miguel Torres', 'Disco Externo 2TB', 'Componentes', 1, 250.00, 250.00, CURDATE(), 'Carlos');
