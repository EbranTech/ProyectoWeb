# BiblioSys

Prototipo web para la gestion de prestamos y devoluciones de una biblioteca universitaria.

## Estructura actual

- `index.html`: ingreso al sistema.
- `dashboard.html`: aplicacion completa.
- `assets/js/database.js`: base de datos simulada en `localStorage`.
- `assets/js/app.js`: flujo de pantallas, formularios y consultas.
- `assets/css/app.css`: estilos compartidos.
- `sql/query.sql`: modelo relacional de referencia.

Las paginas sueltas anteriores fueron retiradas para evitar tener dos versiones del mismo modulo con logicas diferentes.

## Flujo principal

1. El administrador entra a `Usuarios` y registra usuarios con rol y estado.
2. Solo usuarios activos pueden iniciar sesion.
3. El bibliotecario no ve ni puede usar la pestaña `Usuarios`.
4. Registrar autores.
5. Registrar libros asociados a un autor.
6. Registrar estudiantes.
7. Crear prestamos ingresando solo carnet e ISBN.
8. El sistema consulta estudiante y libro desde la base simulada.
9. El prestamo guarda las relaciones con estudiante y libro, descuenta disponibilidad y muestra fecha de entrega y fecha esperada de devolucion.
10. Devoluciones consulta los prestamos, muestra todos los datos unidos y permite registrar la fecha real de devolucion por ID.
11. Consulta de libros muestra disponibilidad, ubicacion y estado.
12. Historial muestra la trazabilidad de prestamos activos y devueltos.

## Usuarios de prueba

- Administrador: `karla.motta` / `karla123*`
- Bibliotecario: `yesenia.garcia` / `yesenia123`

## Nota de prototipo

El proyecto no usa backend real. La comunicacion entre tablas se simula con `localStorage` usando IDs y busquedas equivalentes a consultas por usuario, carnet, ISBN y prestamos.
