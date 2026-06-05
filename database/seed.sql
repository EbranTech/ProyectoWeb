-- BiblioSys Seed Data
USE bibliosys;

INSERT INTO roles (nombre, descripcion) VALUES
('ADMIN', 'Administrador del sistema'),
('BIBLIOTECARIO', 'Gestion de libros, prestamos y devoluciones')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

DELETE FROM usuarios;

INSERT INTO usuarios (id_rol, nombre, username, password_hash, activo) VALUES
(1, 'Administrator', 'admin', '$2y$10$H1f5iFWtEhJ9rsQNDX.6q.u3thwdi3wwQRz1PvOza0HZsnMxJEA/u', 1),
(2, 'Librarian', 'biblio', '$2y$10$PI9R42WpAfecMlSIwdEF..ct0A9RFT0IGN/dHI50Sg4/8jLeZMwdm', 1);
