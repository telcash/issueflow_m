-- Datos de prueba.
USE issueflow_prueba;

INSERT INTO users (name, email) VALUES
    ('Ana Torres', 'ana@example.test'),
    ('Luis Pérez', 'luis@example.test');

INSERT INTO categories (name) VALUES
    ('Acceso'),
    ('Facturación'),
    ('Datos de cliente');

INSERT INTO tickets (code, title, priority, status, user_id, category_id) VALUES
    ('INC-1001', 'No puedo iniciar sesión', 'urgent', 'open', 1, 1),
    ('INC-1002', 'Error en la factura', 'high', 'in_progress', 2, 2),
    ('INC-1003', 'Actualizar datos de contacto', 'normal', 'closed', NULL, 3);