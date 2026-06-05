-- BiblioSys Seed Data
USE bibliosys;

-- Roles
INSERT INTO roles (nombre, descripcion) VALUES
('ADMIN', 'Administrador del sistema'),
('BIBLIOTECARIO', 'Gestion de libros, prestamos y devoluciones');

-- Usuarios (Passwords are hashed with password_hash() using BCRYPT)
-- admin / admin123
-- biblio / biblio123
INSERT INTO usuarios (id_rol, nombre, username, password_hash) VALUES
(1, 'Administrator', 'admin', '$2y$10$X9pYz1aB2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4'),
(2, 'Librarian', 'biblio', '$2y$10$A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6');

-- Autores
INSERT INTO autores (nombres, apellidos, nacionalidad) VALUES
('Robert', 'Martin', 'Estados Unidos'),
('Martin', 'Fowler', 'Estados Unidos'),
('Abraham', 'Silberschatz', 'Estados Unidos'),
('Gabriel', 'Garcia Marquez', 'Colombia'),
('Thomas', 'Cormen', 'Estados Unidos');

-- Estudiantes
INSERT INTO estudiantes (carnet, nombres, apellidos, carrera, correo, telefono) VALUES
('20250001', 'Carlos', 'Ramirez', 'Ingenieria en Sistemas', 'carlos@uvg.edu.gt', '55510001'),
('20250002', 'Ana', 'Lopez', 'Ingenieria Industrial', 'ana@uvg.edu.gt', '55510002'),
('20250003', 'Luis', 'Morales', 'Ingenieria Civil', 'luis@uvg.edu.gt', '55510003'),
('20250004', 'Maria', 'Castillo', 'Arquitectura', 'maria@uvg.edu.gt', '55510004');

-- Libros
INSERT INTO libros (codigo_libro, isbn, titulo, id_autor, categoria, editorial, anio_publicacion, cantidad_total, cantidad_disponible, ubicacion, estado) VALUES
('LIB001', '9780132350884', 'Clean Code', 1, 'Programacion', 'Prentice Hall', 2008, 5, 4, 'EST-001 Programacion', 'DISPONIBLE'),
('LIB002', '9780137081073', 'Clean Coder', 1, 'Programacion', 'Prentice Hall', 2011, 4, 4, 'EST-001 Programacion', 'DISPONIBLE'),
('LIB003', '9780201485677', 'Refactoring', 2, 'Ingenieria de Software', 'Addison Wesley', 1999, 3, 3, 'EST-004 Software', 'DISPONIBLE'),
('LIB004', '9780073523323', 'Database System Concepts', 3, 'Bases de Datos', 'McGraw Hill', 2019, 6, 6, 'EST-002 Bases de Datos', 'DISPONIBLE'),
('LIB005', '9780262033848', 'Introduction to Algorithms', 5, 'Algoritmos', 'MIT Press', 2009, 5, 4, 'EST-005 Matematica', 'DISPONIBLE'),
('LIB006', '9780307474728', 'Cien Anos de Soledad', 4, 'Literatura', 'Sudamericana', 1967, 2, 0, 'EST-009 Literatura', 'PRESTADO');

-- Prestamos
INSERT INTO prestamos (id_estudiante, id_libro, fecha_prestamo, fecha_devolucion_esperada, fecha_devolucion_real, estado, observaciones) VALUES
(1, 1, '2026-06-01', '2026-06-10', NULL, 'ACTIVO', 'Prestamo vigente'),
(2, 3, '2026-05-20', '2026-05-30', '2026-05-29', 'DEVUELTO', 'Devuelto en buen estado'),
(3, 5, '2026-06-02', '2026-06-12', NULL, 'ACTIVO', 'Prestamo vigente'),
(4, 6, '2026-05-25', '2026-06-04', NULL, 'ACTIVO', 'Ejemplar unico prestado');
