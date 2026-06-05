# BiblioSys - Sistema de Gestión Bibliotecaria

BiblioSys es un sistema de gestión de bibliotecas universitarias diseñado bajo una arquitectura de tres capas (Frontend MVC, Backend API REST, y Base de Datos MySQL) desplegado en servidores independientes.

## 📋 Módulos y Casos de Uso

El sistema implementa la lógica de negocio completa de BiblioSys:

- **Gestión de Usuarios:** Control de acceso para Administradores y Bibliotecarios.
- **Catálogo de Autores:** Registro y mantenimiento de autores.
- **Registro de Estudiantes:** Control de datos de estudiantes y validación de carnet.
- **Inventario de Libros:** Gestión de ISBN, códigos internos, existencias y ubicaciones.
- **Flujo de Préstamos:** Registro de salidas de libros validando disponibilidad y estado del estudiante.
- **Gestión de Devoluciones:** Procesamiento de devoluciones y actualización automática del inventario.
- **Consulta y Historial:** Vistas rápidas de libros disponibles y trazabilidad completa de movimientos.

## 🛠️ Requisitos Técnicos

- **Servidores:** 3 VMs Ubuntu Server (Frontend, Backend, DB).
- **Servidor Web:** Apache2 con `mod_rewrite` habilitado.
- **Lenguaje:** PHP 8.x (con extensiones `pdo_mysql`, `curl`, `json`).
- **Base de Datos:** MySQL o MariaDB.
- **Red:** IPs estáticas o Tailscale para interconexión.

## 🔑 Credenciales de Prueba

| Rol | Usuario | Contraseña (Texto Plano) |
|------|---------|--------------------------|
| Administrador | `admin` | `admin123` |
| Bibliotecario | `biblio` | `biblio123` |

## 🚀 Guía Rápida de Despliegue

1. **Base de Datos:** Instalar MySQL $\rightarrow$ Crear BD `bibliosys` $\rightarrow$ Ejecutar `database/schema.sql` y `database/seed.sql` $\rightarrow$ Abrir puerto 3306.
2. **Backend:** Instalar Apache y PHP $\rightarrow$ Subir carpeta `backend/` $\rightarrow$ Configurar `.env` (IP de la BD) $\rightarrow$ Apuntar `DocumentRoot` a `public/`.
3. **Frontend:** Instalar Apache y PHP $\rightarrow$ Subir carpeta `frontend/` $\rightarrow$ Configurar `.env` (IP del Backend) $\rightarrow$ Apuntar `DocumentRoot` a `public/`.

## 🌐 Cambio de Red (Física $\leftrightarrow$ Tailscale)

Para cambiar la red de comunicación, **no toque el código PHP**. Edite los archivos `.env` en cada servidor:

- **Para Red Física:** Use las IPs asignadas por el router/DHCP en cada VM.
- **Para Tailscale:** Use las IPs o nombres de dominio obtenidos mediante `tailscale ip -4`.

Consulte `docs/NETWORK_CONFIG.md` para el detalle de cada variable.

## 📂 Estructura del Proyecto

- `/backend`: API REST nativa en PHP con arquitectura Repository-Service-Controller.
- `/frontend`: Interfaz MVC que consume la API vía HTTP.
- `/database`: Definiciones de tablas y datos iniciales.
- `/docs`: Documentación técnica de despliegue y red.

## 🔍 Pruebas de Endpoints (curl)

Para probar el backend (requiere token):
```bash
curl -H "Authorization: Bearer 123456" http://BACKEND_IP/api/libros
```
