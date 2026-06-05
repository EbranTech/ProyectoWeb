# Colección de Endpoints API - BiblioSys

Toda solicitud a la API debe incluir el siguiente header de autorización:
`Authorization: Bearer 123456` (El token se configura en el `.env` del backend).

## 1. Usuarios
**Base URL:** `/api/usuarios`

| Método | Endpoint | Descripción | Body JSON | Respuesta Esperada |
|---------|----------|-------------|-----------|-------------------|
| GET | `/` | Listar todos | N/A | `200 OK` + Lista de usuarios |
| GET | `/{id}` | Ver detalle | N/A | `200 OK` + Objeto usuario |
| POST | `/` | Crear usuario | `{ "nombre": "...", "username": "...", "password": "...", "id_rol": 1, "activo": 1 }` | `201 Created` |
| PUT | `/{id}` | Actualizar usuario | `{ "nombre": "...", "username": "...", "password": "...", "id_rol": 1, "activo": 1 }` | `200 OK` |
| DELETE | `/{id}` | Eliminar usuario | N/A | `200 OK` |

## 2. Autores
**Base URL:** `/api/autores`

| Método | Endpoint | Descripción | Body JSON | Respuesta Esperada |
|---------|----------|-------------|-----------|-------------------|
| GET | `/` | Listar todos | N/A | `200 OK` |
| GET | `/{id}` | Ver detalle | N/A | `200 OK` |
| POST | `/` | Crear autor | `{ "nombres": "...", "apellidos": "...", "nacionalidad": "..." }` | `201 Created` |
| PUT | `/{id}` | Actualizar autor | `{ "nombres": "...", "apellidos": "...", "nacionalidad": "..." }` | `200 OK` |
| DELETE | `/{id}` | Eliminar autor | N/A | `200 OK` |

## 3. Estudiantes
**Base URL:** `/api/estudiantes`

| Método | Endpoint | Descripción | Body JSON | Respuesta Esperada |
|---------|----------|-------------|-----------|-------------------|
| GET | `/` | Listar todos | N/A | `200 OK` |
| GET | `/{id}` | Ver detalle | N/A | `200 OK` |
| GET | `/lookup?carnet=C123` | Buscar por carnet | N/A | `200 OK` |
| POST | `/` | Crear estudiante | `{ "carnet": "...", "nombres": "...", "apellidos": "...", "carrera": "...", "correo": "...", "telefono": "...", "estado": "ACTIVO" }` | `201 Created` |
| PUT | `/{id}` | Actualizar estudiante | `{ "carnet": "...", "nombres": "...", "apellidos": "...", "carrera": "...", "correo": "...", "telefono": "...", "estado": "ACTIVO" }` | `200 OK` |
| DELETE | `/{id}` | Eliminar estudiante | N/A | `200 OK` |

## 4. Libros
**Base URL:** `/api/libros`

| Método | Endpoint | Descripción | Body JSON | Respuesta Esperada |
|---------|----------|-------------|-----------|-------------------|
| GET | `/` | Listar todos | N/A | `200 OK` |
| GET | `/{id}` | Ver detalle | N/A | `200 OK` |
| GET | `/lookup?isbn=123` | Buscar por ISBN | N/A | `200 OK` |
| POST | `/` | Crear libro | `{ "codigo": "...", "isbn": "...", "titulo": "...", "id_autor": 1, "categoria": "...", "editorial": "...", "anio": 2020, "total": 5, "ubicacion": "...", "estado": "DISPONIBLE" }` | `201 Created` |
| PUT | `/{id}` | Actualizar libro | `{ "codigo": "...", "isbn": "...", "titulo": "...", "id_autor": 1, "categoria": "...", "editorial": "...", "anio": 2020, "total": 5, "ubicacion": "...", "estado": "DISPONIBLE" }` | `200 OK` |
| DELETE | `/{id}` | Eliminar libro | N/A | `200 OK` |

## 5. Prestamos
**Base URL:** `/api/prestamos`

| Método | Endpoint | Descripción | Body JSON | Respuesta Esperada |
|---------|----------|-------------|-----------|-------------------|
| GET | `/` | Listar todos | N/A | `200 OK` |
| POST | `/` | Registrar prestamo | `{ "carnet": "...", "isbn": "...", "fecha_prestamo": "2026-06-01", "fecha_esperada": "2026-06-08", "observaciones": "..." }` | `201 Created` |
| POST | `/return` | Registrar devolucion | `{ "id_prestamo": 1, "fecha_devolucion": "2026-06-05" }` | `200 OK` |

## Escenarios de Error
- **401 Unauthorized:** Enviar solicitud sin header `Authorization` o con token incorrecto.
- **404 Not Found:** Acceder a un ID que no existe (ej. `/api/usuarios/999`).
- **400 Bad Request:** Enviar datos incompletos o romper reglas de negocio (ej. eliminar autor con libros asociados).
