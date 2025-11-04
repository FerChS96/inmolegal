# 📦 Instalación del Sistema InmoLegal

## Pre-requisitos

- PHP 8.1 o superior
- Composer
- PostgreSQL 13 o superior
- Node.js 16+ y npm
- Git

## 🚀 Pasos de Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/FerChS96/inmolegal.git
cd inmolegal
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node.js

```bash
npm install
```

### 4. Crear archivo de configuración `.env`

```bash
cp .env.example .env
```

### 5. Configurar variables de entorno en `.env`

Edita el archivo `.env` con tus credenciales:

```env
# Aplicación
APP_NAME=InmoLegal
APP_URL=http://localhost:8000

# Base de datos PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=badilloDB
DB_USERNAME=postgres
DB_PASSWORD=tu_password_aqui

# Correo (Porkbun SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.porkbun.com
MAIL_PORT=587
MAIL_USERNAME=soporte@inmolegalmx.com
MAIL_PASSWORD=tu_password_smtp_aqui
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="soporte@inmolegalmx.com"
MAIL_FROM_NAME="InmoLegalMX"

# Clip Payment Gateway (opcional, si usarás pagos)
CLIP_API_KEY=tu_api_key_aqui
CLIP_API_SECRET=tu_api_secret_aqui
CLIP_WEBHOOK_SECRET=tu_webhook_secret_aqui
```

### 6. Generar la clave de aplicación

```bash
php artisan key:generate
```

### 7. Crear la base de datos

En PostgreSQL, crea la base de datos:

```sql
CREATE DATABASE badilloDB;
```

### 8. Ejecutar migraciones

```bash
php artisan migrate
```

### 9. Crear el enlace simbólico de storage

```bash
php artisan storage:link
```

### 10. Compilar assets de frontend

```bash
npm run build
```

## 🧪 Probar la instalación

### Iniciar el servidor de desarrollo

```bash
php artisan serve
```

Accede a: `http://localhost:8000`

### Probar envío de correos

```bash
php artisan email:test tu_email@ejemplo.com
```

### Simular un contrato completo

```bash
php artisan simular:contrato-completo
```

## 📁 Archivos/Carpetas que NO están en Git

Los siguientes archivos/carpetas se generan durante la instalación o deben crearse manualmente:

1. **`.env`** - Configuración del entorno (copiar de `.env.example`)
2. **`vendor/`** - Dependencias PHP (generado con `composer install`)
3. **`node_modules/`** - Dependencias Node.js (generado con `npm install`)
4. **`public/build/`** - Assets compilados (generado con `npm run build`)
5. **`public/storage`** - Enlace simbólico (creado con `php artisan storage:link`)
6. **`storage/logs/`** - Los archivos de log se generan automáticamente
7. **`bootstrap/cache/`** - Cache de Laravel (se genera automáticamente)

## 🔧 Comandos útiles

```bash
# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver rutas disponibles
php artisan route:list

# Crear usuario admin (si implementas seeders)
php artisan db:seed

# Ver logs en tiempo real
tail -f storage/logs/laravel.log
```

## 🌐 Endpoints públicos

- **Formulario de contratos**: `/contratos/crear`
- **Widget examples**: `/widget-examples.html`
- **API example**: `/ejemplo-api.html`
- **Admin panel**: `/admin` (requiere autenticación)

## 📧 Configuración de correo

Si los correos no llegan, verifica:

1. Credenciales SMTP correctas en `.env`
2. Puerto 587 abierto en firewall
3. Registros SPF del dominio configurados
4. Revisar carpeta de spam

## 🗄️ Esquema de base de datos

Consulta el archivo `esquemaSQL.txt` en la raíz del proyecto para ver el esquema completo de la base de datos.

## 🔒 Seguridad

Antes de producción:

- [ ] Cambiar `APP_ENV=production` en `.env`
- [ ] Establecer `APP_DEBUG=false`
- [ ] Configurar certificado SSL (HTTPS)
- [ ] Cambiar todas las contraseñas por defecto
- [ ] Configurar backups automáticos de base de datos
- [ ] Revisar permisos de carpetas (storage y bootstrap/cache: 775)

## 🆘 Soporte

Para reportar problemas o solicitar ayuda:
- GitHub Issues: https://github.com/FerChS96/inmolegal/issues
- Email: soporte@inmolegalmx.com
