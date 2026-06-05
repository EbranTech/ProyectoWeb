CREATE DATABASE IF NOT EXISTS biblioteca_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE biblioteca_db;

CREATE TABLE roles (
    id_rol BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios (
    id_usuario BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_rol BIGINT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

CREATE TABLE autores (
    id_autor BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(120) NOT NULL,
    apellidos VARCHAR(120) NOT NULL,
    nacionalidad VARCHAR(100),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE estudiantes (
    id_estudiante BIGINT AUTO_INCREMENT PRIMARY KEY,
    carnet VARCHAR(30) NOT NULL UNIQUE,
    nombres VARCHAR(150) NOT NULL,
    apellidos VARCHAR(150) NOT NULL,
    carrera VARCHAR(150) NOT NULL,
    correo VARCHAR(150),
    telefono VARCHAR(50),
    estado ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE libros (
    id_libro BIGINT AUTO_INCREMENT PRIMARY KEY,
    codigo_libro VARCHAR(30) NOT NULL UNIQUE,
    isbn VARCHAR(30) NOT NULL UNIQUE,
    titulo VARCHAR(250) NOT NULL,
    id_autor BIGINT NOT NULL,
    categoria VARCHAR(120),
    editorial VARCHAR(150),
    anio_publicacion INT,
    cantidad_total INT NOT NULL DEFAULT 1,
    cantidad_disponible INT NOT NULL DEFAULT 1,
    ubicacion VARCHAR(150) NOT NULL,
    estado ENUM('DISPONIBLE','PRESTADO','MANTENIMIENTO') NOT NULL DEFAULT 'DISPONIBLE',
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_libro_autor
        FOREIGN KEY (id_autor) REFERENCES autores(id_autor),
    CONSTRAINT chk_libro_cantidades
        CHECK (cantidad_total >= 0 AND cantidad_disponible >= 0 AND cantidad_disponible <= cantidad_total)
);

CREATE TABLE prestamos (
    id_prestamo BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_estudiante BIGINT NOT NULL,
    id_libro BIGINT NOT NULL,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion_esperada DATE NOT NULL,
    fecha_devolucion_real DATE NULL,
    estado ENUM('ACTIVO','DEVUELTO') NOT NULL DEFAULT 'ACTIVO',
    observaciones TEXT,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_prestamo_estudiante
        FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id_estudiante),
    CONSTRAINT fk_prestamo_libro
        FOREIGN KEY (id_libro) REFERENCES libros(id_libro),
    CONSTRAINT chk_prestamo_fechas
        CHECK (fecha_devolucion_esperada >= fecha_prestamo)
);

CREATE INDEX idx_estudiantes_carnet ON estudiantes(carnet);
CREATE INDEX idx_libros_isbn ON libros(isbn);
CREATE INDEX idx_libros_estado ON libros(estado);
CREATE INDEX idx_prestamos_estado ON prestamos(estado);
CREATE INDEX idx_prestamos_estudiante ON prestamos(id_estudiante);
CREATE INDEX idx_prestamos_libro ON prestamos(id_libro);

CREATE VIEW vw_usuarios_acceso AS
SELECT
    u.id_usuario,
    u.nombre,
    u.username,
    r.nombre AS rol,
    CASE WHEN u.activo = TRUE THEN 'ACTIVO' ELSE 'INACTIVO' END AS estado,
    CASE
        WHEN u.activo = TRUE THEN 'PUEDE_INGRESAR'
        ELSE 'SIN_ACCESO'
    END AS acceso
FROM usuarios u
INNER JOIN roles r ON r.id_rol = u.id_rol;

CREATE VIEW vw_libros_disponibles AS
SELECT
    l.id_libro,
    l.codigo_libro,
    l.isbn,
    l.titulo AS libro,
    CONCAT(a.nombres, ' ', a.apellidos) AS autor,
    l.categoria,
    l.cantidad_disponible,
    l.ubicacion,
    l.estado
FROM libros l
INNER JOIN autores a ON a.id_autor = l.id_autor
WHERE l.activo = TRUE
  AND l.estado = 'DISPONIBLE'
  AND l.cantidad_disponible > 0;

CREATE VIEW vw_prestamos_detalle AS
SELECT
    p.id_prestamo AS id,
    e.carnet,
    CONCAT(e.nombres, ' ', e.apellidos) AS estudiante,
    e.carrera,
    l.codigo_libro,
    l.isbn,
    l.titulo AS libro,
    CONCAT(a.nombres, ' ', a.apellidos) AS autor,
    l.ubicacion,
    p.fecha_prestamo AS prestamo,
    p.fecha_devolucion_esperada,
    p.fecha_devolucion_real AS fecha_devolucion,
    p.estado,
    p.observaciones
FROM prestamos p
INNER JOIN estudiantes e ON e.id_estudiante = p.id_estudiante
INNER JOIN libros l ON l.id_libro = p.id_libro
INNER JOIN autores a ON a.id_autor = l.id_autor;

CREATE VIEW vw_historial_prestamos AS
SELECT
    id,
    carnet,
    estudiante,
    carrera,
    codigo_libro,
    isbn,
    libro,
    prestamo,
    fecha_devolucion_esperada,
    fecha_devolucion,
    estado
FROM vw_prestamos_detalle;

INSERT INTO roles (nombre, descripcion) VALUES
('ADMIN','Administrador del sistema'),
('BIBLIOTECARIO','Gestion de libros, prestamos y devoluciones');

INSERT INTO usuarios (id_rol, nombre, username, password_hash) VALUES
(1,'Karla Motta','karla.motta','karla123*'),
(2,'Yesenia Garcia','yesenia.garcia','yesenia123');

INSERT INTO autores (nombres, apellidos, nacionalidad) VALUES
('Robert','Martin','Estados Unidos'),
('Martin','Fowler','Estados Unidos'),
('Abraham','Silberschatz','Estados Unidos'),
('Gabriel','Garcia Marquez','Colombia'),
('Thomas','Cormen','Estados Unidos');

INSERT INTO estudiantes (carnet, nombres, apellidos, carrera, correo, telefono) VALUES
('20250001','Carlos','Ramirez','Ingenieria en Sistemas','carlos@uvg.edu.gt','55510001'),
('20250002','Ana','Lopez','Ingenieria Industrial','ana@uvg.edu.gt','55510002'),
('20250003','Luis','Morales','Ingenieria Civil','luis@uvg.edu.gt','55510003'),
('20250004','Maria','Castillo','Arquitectura','maria@uvg.edu.gt','55510004');

INSERT INTO libros (
    codigo_libro,
    isbn,
    titulo,
    id_autor,
    categoria,
    editorial,
    anio_publicacion,
    cantidad_total,
    cantidad_disponible,
    ubicacion,
    estado
) VALUES
('LIB001','9780132350884','Clean Code',1,'Programacion','Prentice Hall',2008,5,4,'EST-001 Programacion','DISPONIBLE'),
('LIB002','9780137081073','Clean Coder',1,'Programacion','Prentice Hall',2011,4,4,'EST-001 Programacion','DISPONIBLE'),
('LIB003','9780201485677','Refactoring',2,'Ingenieria de Software','Addison Wesley',1999,3,3,'EST-004 Software','DISPONIBLE'),
('LIB004','9780073523323','Database System Concepts',3,'Bases de Datos','McGraw Hill',2019,6,6,'EST-002 Bases de Datos','DISPONIBLE'),
('LIB005','9780262033848','Introduction to Algorithms',5,'Algoritmos','MIT Press',2009,5,4,'EST-005 Matematica','DISPONIBLE'),
('LIB006','9780307474728','Cien Anos de Soledad',4,'Literatura','Sudamericana',1967,2,0,'EST-009 Literatura','PRESTADO');

INSERT INTO prestamos (
    id_estudiante,
    id_libro,
    fecha_prestamo,
    fecha_devolucion_esperada,
    fecha_devolucion_real,
    estado,
    observaciones
) VALUES
(1,1,'2026-06-01','2026-06-10',NULL,'ACTIVO','Prestamo vigente'),
(2,3,'2026-05-20','2026-05-30','2026-05-29','DEVUELTO','Devuelto en buen estado'),
(3,5,'2026-06-02','2026-06-12',NULL,'ACTIVO','Prestamo vigente'),
(4,6,'2026-05-25','2026-06-04',NULL,'ACTIVO','Ejemplar unico prestado');
