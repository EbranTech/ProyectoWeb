# Guía de Despliegue en Ubuntu Server - BiblioSys

Este sistema está diseñado para ser desplegado en tres máquinas virtuales (VM) Ubuntu independientes para garantizar la separación de capas (Frontend, Backend, Base de Datos).

## 1. Servidor de Base de Datos (DB Server)

### Instalación y Configuración
1. **Instalar MySQL/MariaDB:**
   ```bash
   sudo apt update
   sudo apt install mariadb-server -y
   ```
2. **Configurar acceso remoto:**
   Editar `/etc/mysql/mariadb.conf.d/50-server.cnf` y cambiar `bind-address = 127.0.0.1` por `bind-address = 0.0.0.0`.
3. **Reiniciar servicio:**
   ```bash
   sudo systemctl restart mysql
   ```
4. **Configurar Base de Datos y Usuario:**
   ```sql
   CREATE DATABASE bibliosys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'usuario_prod'@'%' IDENTIFIED BY 'pass_seguro';
   GRANT ALL PRIVILEGES ON bibliosys.* TO 'usuario_prod'@'%';
   FLUSH PRIVILEGES;
   ```
5. **Cargar Esquema y Datos:**
   Importar los archivos `database/schema.sql` y `database/seed.sql`.
6. **Abrir Firewall:**
   ```bash
   sudo ufw allow 3306/tcp
   ```

---

## 2. Servidor Backend (API Server)

### Instalación y Configuración
1. **Instalar Apache y PHP:**
   ```bash
   sudo apt update
   sudo apt install apache2 php php-mysql php-curl php-json -y
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```
2. **Despliegue de archivos:**
   - Subir la carpeta `backend/` al servidor.
   - Configurar el `DocumentRoot` de Apache para que apunte a `/var/www/html/backend/public`.
3. **Configurar VirtualHost:**
   Asegurar que `AllowOverride All` esté habilitado en la configuración del sitio.
4. **Configurar `.env`:**
   Crear archivo `.env` basado en `.env.example`.
   - **Red Física:** Usar IP de la VM DB.
   - **Tailscale:** Usar IP/DNS de Tailscale de la VM DB.
5. **Abrir Firewall:**
   ```bash
   sudo ufw allow 80/tcp
   ```

---

## 3. Servidor Frontend (Web Server)

### Instalación y Configuración
1. **Instalar Apache y PHP:**
   ```bash
   sudo apt update
   sudo apt install apache2 php php-curl -y
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```
2. **Despliegue de archivos:**
   - Subir la carpeta `frontend/` al servidor.
   - Configurar el `DocumentRoot` de Apache para que apunte a `/var/www/html/frontend/public`.
3. **Configurar `.env`:**
   Crear archivo `.env` basado en `.env.example`.
   - **Red Física:** Usar IP de la VM Backend.
   - **Tailscale:** Usar IP/DNS de Tailscale de la VM Backend.
4. **Abrir Firewall:**
   ```bash
   sudo ufw allow 80/tcp
   ```

---

## 4. Configuración de Tailscale (Opcional para Red Remota)

Si se desea usar Tailscale en lugar de IPs físicas:
1. **Instalar Tailscale en las 3 VMs:**
   ```bash
   curl -fsSL https://tailscale.com/install.sh | sh
   sudo tailscale up
   ```
2. **Obtener IPs:** Ejecutar `tailscale ip -4` en cada máquina.
3. **Actualizar `.env`:** Sustituir todas las IPs de red física por las IPs de Tailscale en los archivos `.env` del Frontend y Backend.

## Resumen de Cambios de IP

| Servidor | Archivo | Variable | Valor Red Física | Valor Tailscale |
|----------|---------|----------|------------------|------------------|
| Frontend | `.env` | `API_BASE_URL` | `http://IP_BACKEND/api` | `http://DNS_BACKEND/api` |
| Backend | `.env` | `DB_HOST` | `IP_DATABASE` | `DNS_DATABASE` |
| Backend | `.env` | `CORS_ALLOWED_ORIGINS` | `http://IP_FRONTEND` | `http://DNS_FRONTEND` |
