# Pruebas Postman - empleados_api

Base URL:

```text
http://localhost/empleados_api/public
```

Headers para rutas protegidas:

```text
Authorization: Bearer 123456
Content-Type: application/json
```

## CRUD valido

Listar empleados:

```text
GET http://localhost/empleados_api/public/api/empleados
```

Obtener empleado por id:

```text
GET http://localhost/empleados_api/public/api/empleados/1
```

Crear empleado:

```text
POST http://localhost/empleados_api/public/api/empleados
```

Body:

```json
{
  "nombre": "Pedro",
  "apellido": "Lopez",
  "correo": "pedro.lopez@example.com",
  "puesto": "Desarrollador",
  "salario": 4500.50,
  "fecha_contratacion": "2026-05-23"
}
```

Actualizar empleado:

```text
PUT http://localhost/empleados_api/public/api/empleados/1
```

Body:

```json
{
  "nombre": "Pedro",
  "apellido": "Lopez",
  "correo": "pedro.lopez.actualizado@example.com",
  "puesto": "Analista",
  "salario": 5000,
  "fecha_contratacion": "2026-05-23"
}
```

Eliminar empleado:

```text
DELETE http://localhost/empleados_api/public/api/empleados/1
```

Preflight CORS:

```text
OPTIONS http://localhost/empleados_api/public/api/empleados
```

## Casos invalidos / validaciones

Sin token:

```text
GET http://localhost/empleados_api/public/api/empleados
```

Resultado esperado: `401 No autorizado`.

Token incorrecto:

```text
GET http://localhost/empleados_api/public/api/empleados
Authorization: Bearer token-malo
```

Resultado esperado: `401 No autorizado`.

Ruta inexistente:

```text
GET http://localhost/empleados_api/public/api/no-existe
```

Resultado esperado: `404 Ruta no encontrada`.

Id invalido:

```text
GET http://localhost/empleados_api/public/api/empleados/abc
Authorization: Bearer 123456
```

Resultado esperado: `400 El id debe ser un numero entero valido`.

Empleado inexistente:

```text
GET http://localhost/empleados_api/public/api/empleados/999999
Authorization: Bearer 123456
```

Resultado esperado: `404 Empleado no encontrado`.

Crear sin campos:

```text
POST http://localhost/empleados_api/public/api/empleados
Authorization: Bearer 123456
Content-Type: application/json
```

Body:

```json
{}
```

Resultado esperado: `400 Errores de validacion`.

Crear con datos invalidos:

```text
POST http://localhost/empleados_api/public/api/empleados
Authorization: Bearer 123456
Content-Type: application/json
```

Body:

```json
{
  "nombre": "Pedro",
  "apellido": "Lopez",
  "correo": "correo-invalido",
  "puesto": "Analista",
  "salario": -10,
  "fecha_contratacion": "23-05-2026"
}
```

Resultado esperado: `400 Errores de validacion`.

Crear con correo duplicado:

```text
POST http://localhost/empleados_api/public/api/empleados
Authorization: Bearer 123456
Content-Type: application/json
```

Body:

```json
{
  "nombre": "Juan",
  "apellido": "Garcia",
  "correo": "juan.garcia@email.com",
  "puesto": "Gerente",
  "salario": 4500,
  "fecha_contratacion": "2023-01-15"
}
```

Resultado esperado: `400 El correo ya existe para otro empleado`.

Actualizar empleado inexistente:

```text
PUT http://localhost/empleados_api/public/api/empleados/999999
Authorization: Bearer 123456
Content-Type: application/json
```

Body:

```json
{
  "nombre": "Pedro",
  "apellido": "Lopez",
  "correo": "pedro.inexistente@example.com",
  "puesto": "Analista",
  "salario": 5000,
  "fecha_contratacion": "2026-05-23"
}
```

Resultado esperado: `404 Empleado no encontrado`.

Eliminar empleado inexistente:

```text
DELETE http://localhost/empleados_api/public/api/empleados/999999
Authorization: Bearer 123456
```

Resultado esperado: `404 Empleado no encontrado`.
