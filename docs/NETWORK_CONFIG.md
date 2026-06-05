# Configuración de Red - BiblioSys

Este documento detalla las variables de entorno necesarias para conectar los tres componentes del sistema en diferentes escenarios de red.

## Variables de Entorno

### Frontend (`frontend/.env`)
| Variable | Descripción | Valor Local | Valor Red Física | Valor Tailscale |
|----------|-------------|-------------|------------------|------------------|
| `APP_ENV` | Entorno de ejecución | `local` | `production` | `production` |
| `APP_URL` | URL base del frontend | `http://localhost:8000` | `http://IP_FRONTEND` | `http://DNS_TAILSCALE_FRONTEND` |
| `API_BASE_URL` | URL de la API Backend | `http://localhost:8001/api` | `http://IP_BACKEND/api` | `http://DNS_TAILSCALE_BACKEND/api` |
| `API_TOKEN` | Token de autenticación API | `123456` | `TOKEN_SEGURO` | `TOKEN_SEGURO` |

### Backend (`backend/.env`)
| Variable | Descripción | Valor Local | Valor Red Física | Valor Tailscale |
|----------|-------------|-------------|------------------|------------------|
| `APP_ENV` | Entorno de ejecución | `local` | `production` | `production` |
| `APP_URL` | URL base del backend | `http://localhost:8001` | `http://IP_BACKEND` | `http://DNS_TAILSCALE_BACKEND` |
| `API_TOKEN` | Token de autenticación API | `123456` | `TOKEN_SEGURO` | `TOKEN_SEGURO` |
| `CORS_ALLOWED_ORIGINS` | Orígenes permitidos | `http://localhost:8000` | `http://IP_FRONTEND` | `http://DNS_TAILSCALE_FRONTEND` |
| `DB_HOST` | Host de la Base de Datos | `localhost` | `IP_DATABASE` | `DNS_TAILSCALE_DB` |
| `DB_PORT` | Puerto MySQL | `3306` | `3306` | `3306` |
| `DB_DATABASE` | Nombre de la BD | `bibliosys` | `bibliosys` | `bibliosys` |
| `DB_USERNAME` | Usuario de la BD | `root` | `usuario_prod` | `usuario_prod` |
| `DB_PASSWORD` | Password de la BD | `password` | `pass_seguro` | `pass_seguro` |
| `DB_CHARSET` | Juego de caracteres | `utf8mb4` | `utf8mb4` | `utf8mb4` |

## Instrucciones de Cambio de Red

Para cambiar la conectividad del sistema, **no modifique el código PHP**. Únicamente edite los archivos `.env` en cada servidor siguiendo la tabla anterior.

1. **Red Física:** Use las direcciones IP estáticas asignadas a cada VM en la red local.
2. **Tailscale:** Use los nombres de dominio (MagicDNS) o las IPs proporcionadas por Tailscale (`tailscale ip -4`).
