# Sistema Web de Observabilidad Operacional para Plataformas E-commerce

Documentación técnica para instalar, ejecutar y reconstruir el proyecto **Sistema Web de Observabilidad Operacional para Plataformas E-commerce**.

---

## Índice

1. [Descripción general](#descripción-general)
2. [Tecnologías utilizadas](#tecnologías-utilizadas)
3. [Estructura general del proyecto](#estructura-general-del-proyecto)
4. [Versión 1: instalación desde el repositorio de GitHub](#versión-1-instalación-desde-el-repositorio-de-github)
5. [Versión 2: construcción del proyecto desde cero](#versión-2-construcción-del-proyecto-desde-cero)
6. [Base de datos completa](#base-de-datos-completa)
7. [Configuración de Prometheus y Node Exporter](#configuración-de-prometheus-y-node-exporter)
8. [Configuración de Laravel](#configuración-de-laravel)
9. [Rutas principales](#rutas-principales)
10. [Modelos principales](#modelos-principales)
11. [Servicios principales](#servicios-principales)
12. [Comandos Artisan](#comandos-artisan)
13. [Controladores principales](#controladores-principales)
14. [Automatización con Cron](#automatización-con-cron)
15. [Pruebas automatizadas](#pruebas-automatizadas)
16. [Comandos útiles de operación](#comandos-útiles-de-operación)
17. [Solución de errores comunes](#solución-de-errores-comunes)
18. [Seguridad y versionamiento](#seguridad-y-versionamiento)
19. [Checklist final de instalación](#checklist-final-de-instalación)

---

# Descripción general

El proyecto corresponde a una aplicación web de observabilidad operacional para plataformas e-commerce. Su objetivo es consultar métricas técnicas, registrar alertas, contextualizarlas por cliente y sitio, convertirlas en incidencias, asignarlas a usuarios técnicos, registrar acciones en bitácora, cerrar incidencias de manera trazable y generar reportes operacionales.

La aplicación utiliza:

- **Laravel** como framework backend y frontend básico.
- **MySQL** como base de datos relacional operacional.
- **Prometheus** como fuente técnica de métricas.
- **Node Exporter** para exponer métricas del servidor.
- **Cron** para automatizar comandos Artisan.
- **PHPUnit / php artisan test** para pruebas automatizadas.
- **GitHub** para control de versiones.

Flujo principal del sistema:

```text
Prometheus / Node Exporter
        ↓
Laravel consulta métricas
        ↓
Se evalúan umbrales
        ↓
Se registran alertas en MySQL
        ↓
La alerta se contextualiza por servidor, sitio y cliente
        ↓
Se crea una incidencia asignada a un usuario
        ↓
Se registran acciones en bitácora
        ↓
Se cierra la incidencia y alerta asociada
        ↓
Se calcula el tiempo de resolución
        ↓
Se genera reporte operacional
```

---

# Tecnologías utilizadas

| Tecnología | Uso dentro del proyecto |
|---|---|
| Ubuntu Server | Sistema operativo del entorno de pruebas. |
| PHP | Lenguaje principal del backend Laravel. |
| Composer | Gestor de dependencias PHP. |
| Laravel | Framework principal de la aplicación web. |
| MySQL | Base de datos relacional del sistema. |
| Prometheus | Fuente técnica de métricas del servidor. |
| Node Exporter | Exposición de métricas del sistema operativo. |
| Cron | Automatización periódica de comandos Artisan. |
| Git | Control de versiones local. |
| GitHub | Repositorio privado del proyecto. |
| PHPUnit | Pruebas unitarias y funcionales mediante Laravel. |

---

# Estructura general del proyecto

```text
observabilidad-app/
├── app/
│   ├── Console/Commands/
│   │   ├── CheckServerAlerts.php
│   │   └── CheckSiteMetrics.php
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── IncidentController.php
│   │   ├── IncidentLogController.php
│   │   ├── MyIncidentController.php
│   │   └── ReportController.php
│   ├── Models/
│   │   ├── Alert.php
│   │   ├── Client.php
│   │   ├── Incident.php
│   │   ├── IncidentLog.php
│   │   ├── Report.php
│   │   ├── Server.php
│   │   ├── Site.php
│   │   ├── SiteMetric.php
│   │   └── User.php
│   └── Services/
│       ├── IncidentPriorityService.php
│       └── PrometheusService.php
├── config/
├── database/
├── resources/views/
│   ├── auth/
│   ├── dashboard.blade.php
│   ├── incidents/
│   └── reports/
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/
│   │   ├── AccessControlTest.php
│   │   └── ExampleTest.php
│   └── Unit/
│       ├── ExampleTest.php
│       └── IncidentPriorityServiceTest.php
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── package.json
└── README.md
```

---

# Versión 1: instalación desde el repositorio de GitHub

Esta sección está pensada para alguien que descarga el proyecto ya creado desde GitHub.

## 1. Requisitos previos

En el servidor o máquina virtual se debe contar con:

```text
Ubuntu Server
PHP 8.2 o superior
Composer
MySQL Server
Git
Node.js y NPM
Prometheus
Node Exporter
```

Instalación base recomendada:

```bash
sudo apt update
sudo apt upgrade -y
sudo apt install -y git unzip curl mysql-server php php-cli php-mbstring php-xml php-bcmath php-curl php-mysql php-zip composer nodejs npm
```

Verificar versiones:

```bash
php -v
composer -V
mysql --version
git --version
node -v
npm -v
```

---

## 2. Clonar el repositorio

```bash
cd /var/www
sudo git clone https://github.com/MayorinesEthan/sistema-observabilidad-operacional-taller-integracion.git observabilidad-app
cd /var/www/observabilidad-app
```

Dar permisos al usuario actual si es necesario:

```bash
sudo chown -R $USER:www-data /var/www/observabilidad-app
sudo chmod -R 775 storage bootstrap/cache
```

---

## 3. Instalar dependencias PHP

```bash
composer install
```

Si es entorno de producción:

```bash
composer install --optimize-autoloader --no-dev
```

---

## 4. Instalar dependencias frontend

```bash
npm install
npm run build
```

Para desarrollo:

```bash
npm run dev
```

---

## 5. Configurar archivo de entorno

Copiar `.env.example`:

```bash
cp .env.example .env
```

Generar clave de Laravel:

```bash
php artisan key:generate
```

Editar `.env`:

```bash
nano .env
```

Configuración recomendada:

```env
APP_NAME="Sistema de Observabilidad Operacional"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=America/Santiago

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=observabilidad_db
DB_USERNAME=observabilidad_user
DB_PASSWORD=tu_password_segura

PROMETHEUS_URL=http://localhost:9090
```

Después de editar:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 6. Crear base de datos y usuario MySQL

Entrar a MySQL:

```bash
sudo mysql
```

Crear base de datos y usuario:

```sql
CREATE DATABASE observabilidad_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'observabilidad_user'@'localhost' IDENTIFIED BY 'tu_password_segura';

GRANT ALL PRIVILEGES ON observabilidad_db.* TO 'observabilidad_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

Probar conexión:

```bash
mysql -u observabilidad_user -p observabilidad_db
```

---

## 7. Ejecutar migraciones Laravel

Si el repositorio incluye migraciones completas:

```bash
php artisan migrate
```

Revisar estado:

```bash
php artisan migrate:status
```

Si algunas tablas operacionales fueron creadas manualmente durante el prototipo, usar el script SQL de la sección [Base de datos completa](#base-de-datos-completa).

---

## 8. Cargar datos de prueba

Entrar a MySQL:

```bash
mysql -u observabilidad_user -p observabilidad_db
```

Insertar servidor, cliente y sitio de prueba:

```sql
INSERT INTO servers (name, ip_address, status, created_at, updated_at)
VALUES ('Servidor Local de Prueba', '127.0.0.1', 'active', NOW(), NOW());

INSERT INTO clients (name, contact_email, priority_level, status, created_at)
VALUES ('Cliente Demo E-commerce', 'cliente.demo@ejemplo.cl', 'high', 'active', NOW());

INSERT INTO sites (server_id, client_id, name, domain, url, document_root, status, created_at, updated_at)
VALUES (1, 1, 'Sitio E-commerce de Prueba', 'localhost', 'http://localhost', '/var/www/html', 'active', NOW(), NOW());
```

Verificar:

```sql
SELECT 
    sites.id,
    sites.name AS site_name,
    sites.domain,
    clients.name AS client_name,
    clients.priority_level
FROM sites
LEFT JOIN clients ON sites.client_id = clients.id;
```

---

## 9. Configurar Prometheus y Node Exporter

Verificar servicios:

```bash
sudo systemctl status prometheus
sudo systemctl status node_exporter
```

Probar Prometheus:

```bash
curl "http://localhost:9090/api/v1/query?query=up"
```

La respuesta debe contener:

```json
{
  "status": "success"
}
```

---

## 10. Levantar la aplicación

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Abrir en navegador:

```text
http://IP_DE_LA_VM:8000
```

Rutas principales:

```text
/login
/register
/dashboard
/mis-incidencias
/reports/operational
```

---

## 11. Ejecutar comandos del sistema

Revisión manual de alertas:

```bash
php artisan alerts:check
```

Revisión manual de métricas por sitio:

```bash
php artisan site-metrics:check
```

---

## 12. Configurar tareas Cron

Abrir crontab:

```bash
crontab -e
```

Agregar:

```cron
* * * * * cd /var/www/observabilidad-app && php artisan alerts:check >> /var/www/observabilidad-app/storage/logs/alerts-check.log 2>&1
*/5 * * * * cd /var/www/observabilidad-app && php artisan site-metrics:check >> /var/www/observabilidad-app/storage/logs/site-metrics-check.log 2>&1
```

Verificar:

```bash
crontab -l
```

Revisar logs:

```bash
tail -n 40 storage/logs/alerts-check.log
tail -n 40 storage/logs/site-metrics-check.log
```

---

## 13. Ejecutar pruebas automatizadas

```bash
php artisan test
```

Resultado esperado:

```text
PASS  Tests\Unit\IncidentPriorityServiceTest
PASS  Tests\Feature\AccessControlTest

Tests: 14 passed
Assertions: 20 passed
```

---

# Versión 2: construcción del proyecto desde cero

Esta sección está pensada para alguien que quiera reconstruir el proyecto desde una instalación limpia de Laravel.

---

## 1. Preparar servidor base

```bash
sudo apt update
sudo apt upgrade -y
sudo apt install -y git unzip curl mysql-server php php-cli php-mbstring php-xml php-bcmath php-curl php-mysql php-zip composer nodejs npm
```

---

## 2. Crear proyecto Laravel

```bash
cd /var/www
composer create-project laravel/laravel observabilidad-app
cd observabilidad-app
```

Asignar permisos:

```bash
sudo chown -R $USER:www-data /var/www/observabilidad-app
sudo chmod -R 775 storage bootstrap/cache
```

---

## 3. Configurar archivo .env

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Configuración base:

```env
APP_NAME="Sistema de Observabilidad Operacional"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=America/Santiago

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=observabilidad_db
DB_USERNAME=observabilidad_user
DB_PASSWORD=tu_password_segura

PROMETHEUS_URL=http://localhost:9090
```

Limpiar caché:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 4. Crear base de datos

```bash
sudo mysql
```

```sql
CREATE DATABASE observabilidad_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'observabilidad_user'@'localhost' IDENTIFIED BY 'tu_password_segura';
GRANT ALL PRIVILEGES ON observabilidad_db.* TO 'observabilidad_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 5. Crear autenticación simple

Este proyecto usa autenticación básica propia, no depende obligatoriamente de Breeze.

Crear controlador:

```bash
php artisan make:controller AuthController
```

Archivo:

```bash
nano app/Http/Controllers/AuthController.php
```

Código completo:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard')->with('message', 'Sesión iniciada correctamente.');
        }

        return back()->withErrors([
            'email' => 'Las credenciales ingresadas no son válidas.'
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'confirmed']
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('message', 'Usuario registrado correctamente.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Sesión cerrada correctamente.');
    }
}
```

---

## 6. Ejecutar migraciones base de Laravel

```bash
php artisan migrate
```

Esto crea tablas base como:

```text
users
cache
jobs
```

---

## 7. Crear tablas operacionales

Se puede hacer mediante migraciones o mediante SQL directo. Para reconstruir rápido el prototipo, usar el script completo de la sección [Base de datos completa](#base-de-datos-completa).

---

## 8. Crear modelos principales

```bash
php artisan make:model Server
php artisan make:model Client
php artisan make:model Site
php artisan make:model SiteMetric
php artisan make:model Alert
php artisan make:model Incident
php artisan make:model IncidentLog
php artisan make:model Report
```

Los modelos completos están en la sección [Modelos principales](#modelos-principales).

---

## 9. Crear controladores principales

```bash
php artisan make:controller DashboardController
php artisan make:controller IncidentController
php artisan make:controller IncidentLogController
php artisan make:controller MyIncidentController
php artisan make:controller ReportController
```

---

## 10. Crear servicios principales

```bash
mkdir -p app/Services
nano app/Services/PrometheusService.php
nano app/Services/IncidentPriorityService.php
```

Código completo en la sección [Servicios principales](#servicios-principales).

---

## 11. Crear comandos Artisan

```bash
php artisan make:command CheckServerAlerts
php artisan make:command CheckSiteMetrics
```

Código completo en la sección [Comandos Artisan](#comandos-artisan).

---

## 12. Registrar nombres de comandos

En Laravel moderno, el comando se registra por su propiedad `$signature`. Cada archivo debe definir su firma:

```php
protected $signature = 'alerts:check';
```

```php
protected $signature = 'site-metrics:check';
```

Probar:

```bash
php artisan list | grep check
```

---

# Base de datos completa

> Nota: si el proyecto ya tiene migraciones completas, se recomienda usar `php artisan migrate`. Si se está reconstruyendo el prototipo desde cero con SQL manual, usar este script.

Entrar a MySQL:

```bash
mysql -u observabilidad_user -p observabilidad_db
```

Script completo:

```sql
CREATE TABLE IF NOT EXISTS servers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    ip_address VARCHAR(100) NULL,
    status VARCHAR(30) DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact_email VARCHAR(150) NULL,
    priority_level VARCHAR(30) DEFAULT 'medium',
    status VARCHAR(30) DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    server_id INT NULL,
    client_id INT NULL,
    name VARCHAR(150) NOT NULL,
    domain VARCHAR(255) NULL,
    url VARCHAR(255) NULL,
    document_root VARCHAR(255) NULL,
    status VARCHAR(30) DEFAULT 'active',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sites_servers FOREIGN KEY (server_id) REFERENCES servers(id),
    CONSTRAINT fk_sites_clients FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE IF NOT EXISTS site_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site_id INT NOT NULL,
    http_status INT NULL,
    response_time_ms DECIMAL(10,2) NULL,
    disk_usage_mb DECIMAL(12,2) NULL,
    availability_status VARCHAR(30) DEFAULT 'unknown',
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_site_metrics_sites FOREIGN KEY (site_id) REFERENCES sites(id)
);

CREATE TABLE IF NOT EXISTS alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    server_id INT NULL,
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(12,2) NULL,
    threshold_value DECIMAL(12,2) NULL,
    severity VARCHAR(30) DEFAULT 'warning',
    status VARCHAR(30) DEFAULT 'open',
    message TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_alerts_servers FOREIGN KEY (server_id) REFERENCES servers(id)
);

CREATE TABLE IF NOT EXISTS incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alert_id INT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    status VARCHAR(30) DEFAULT 'open',
    priority VARCHAR(30) DEFAULT 'medium',
    assigned_to VARCHAR(100) NULL,
    assigned_user_id BIGINT UNSIGNED NULL,
    root_cause TEXT NULL,
    action_taken TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    resolution_time_minutes INT NULL,
    CONSTRAINT fk_incidents_alerts FOREIGN KEY (alert_id) REFERENCES alerts(id),
    CONSTRAINT fk_incidents_assigned_user FOREIGN KEY (assigned_user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS incident_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    action VARCHAR(150) NOT NULL,
    comment TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_incident_logs_incidents FOREIGN KEY (incident_id) REFERENCES incidents(id)
);

CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    summary TEXT NULL,
    generated_by BIGINT UNSIGNED NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reports_users FOREIGN KEY (generated_by) REFERENCES users(id)
);
```

Datos demo:

```sql
INSERT INTO servers (name, ip_address, status)
VALUES ('Servidor Local de Prueba', '127.0.0.1', 'active');

INSERT INTO clients (name, contact_email, priority_level, status)
VALUES ('Cliente Demo E-commerce', 'cliente.demo@ejemplo.cl', 'high', 'active');

INSERT INTO sites (server_id, client_id, name, domain, url, document_root, status)
VALUES (1, 1, 'Sitio E-commerce de Prueba', 'localhost', 'http://localhost', '/var/www/html', 'active');
```

Verificación:

```sql
SHOW TABLES;

SELECT 
    sites.id,
    sites.name AS site_name,
    sites.domain,
    sites.url,
    clients.name AS client_name,
    clients.priority_level,
    servers.name AS server_name
FROM sites
LEFT JOIN clients ON sites.client_id = clients.id
LEFT JOIN servers ON sites.server_id = servers.id;
```

---

# Configuración de Prometheus y Node Exporter

## 1. Instalar Node Exporter

```bash
cd /tmp
wget https://github.com/prometheus/node_exporter/releases/latest/download/node_exporter-1.8.2.linux-amd64.tar.gz
tar xvf node_exporter-*.tar.gz
sudo cp node_exporter-*/node_exporter /usr/local/bin/
```

Crear usuario:

```bash
sudo useradd --no-create-home --shell /bin/false node_exporter
sudo chown node_exporter:node_exporter /usr/local/bin/node_exporter
```

Crear servicio:

```bash
sudo nano /etc/systemd/system/node_exporter.service
```

Contenido:

```ini
[Unit]
Description=Node Exporter
After=network.target

[Service]
User=node_exporter
Group=node_exporter
Type=simple
ExecStart=/usr/local/bin/node_exporter

[Install]
WantedBy=multi-user.target
```

Activar:

```bash
sudo systemctl daemon-reload
sudo systemctl enable node_exporter
sudo systemctl start node_exporter
sudo systemctl status node_exporter
```

Probar:

```bash
curl http://localhost:9100/metrics | head
```

---

## 2. Instalar Prometheus

```bash
sudo apt install -y prometheus
```

Editar configuración:

```bash
sudo nano /etc/prometheus/prometheus.yml
```

Configuración mínima:

```yaml
global:
  scrape_interval: 15s

scrape_configs:
  - job_name: 'prometheus'
    static_configs:
      - targets: ['localhost:9090']

  - job_name: 'node'
    static_configs:
      - targets: ['localhost:9100']
```

Reiniciar:

```bash
sudo systemctl restart prometheus
sudo systemctl enable prometheus
sudo systemctl status prometheus
```

Probar API:

```bash
curl "http://localhost:9090/api/v1/query?query=up"
```

---

# Configuración de Laravel

## Archivo `.env` recomendado

```env
APP_NAME="Sistema de Observabilidad Operacional"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=America/Santiago

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=observabilidad_db
DB_USERNAME=observabilidad_user
DB_PASSWORD=tu_password_segura

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

PROMETHEUS_URL=http://localhost:9090
```

Después de editar:

```bash
php artisan key:generate
php artisan config:clear
php artisan cache:clear
```

---

# Rutas principales

Archivo:

```bash
nano routes/web.php
```

Código completo:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MyIncidentController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/alerts/{alert}/incident', [IncidentController::class, 'store'])
        ->name('alerts.incident');

    Route::post('/incidents/{incident}/close', [IncidentController::class, 'close'])
        ->name('incidents.close');

    Route::post('/incidents/{incident}/logs', [IncidentLogController::class, 'store'])
        ->name('incidents.logs.store');

    Route::get('/reports/operational', [ReportController::class, 'operational'])
        ->name('reports.operational');

    Route::get('/mis-incidencias', [MyIncidentController::class, 'index'])
        ->name('my-incidents.index');
});
```

Verificar rutas:

```bash
php artisan route:list
```

---

# Modelos principales

## `app/Models/Client.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'contact_email',
        'priority_level',
        'status',
        'created_at'
    ];

    public function sites()
    {
        return $this->hasMany(Site::class, 'client_id');
    }
}
```

## `app/Models/Server.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table = 'servers';

    protected $fillable = [
        'name',
        'ip_address',
        'status'
    ];

    public function sites()
    {
        return $this->hasMany(Site::class, 'server_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'server_id');
    }
}
```

## `app/Models/Site.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'sites';

    protected $fillable = [
        'server_id',
        'client_id',
        'name',
        'domain',
        'url',
        'document_root',
        'status'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function metrics()
    {
        return $this->hasMany(SiteMetric::class, 'site_id');
    }

    public function latestMetric()
    {
        return $this->hasOne(SiteMetric::class, 'site_id')->latest('checked_at');
    }
}
```

## `app/Models/SiteMetric.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteMetric extends Model
{
    protected $table = 'site_metrics';
    public $timestamps = false;

    protected $fillable = [
        'site_id',
        'http_status',
        'response_time_ms',
        'disk_usage_mb',
        'availability_status',
        'checked_at'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}
```

## `app/Models/Alert.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table = 'alerts';

    protected $fillable = [
        'server_id',
        'metric_name',
        'metric_value',
        'threshold_value',
        'severity',
        'status',
        'message',
        'closed_at'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'alert_id');
    }
}
```

## `app/Models/Incident.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';

    protected $fillable = [
        'alert_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'assigned_user_id',
        'root_cause',
        'action_taken',
        'closed_at',
        'resolved_at',
        'resolution_time_minutes'
    ];

    public function alert()
    {
        return $this->belongsTo(Alert::class, 'alert_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function logs()
    {
        return $this->hasMany(IncidentLog::class, 'incident_id')->latest('created_at');
    }
}
```

## `app/Models/IncidentLog.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentLog extends Model
{
    protected $table = 'incident_logs';
    public $timestamps = false;

    protected $fillable = [
        'incident_id',
        'action',
        'comment',
        'created_at'
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }
}
```

## `app/Models/Report.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'title',
        'summary',
        'generated_by',
        'generated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
```

---

# Servicios principales

## `app/Services/PrometheusService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PrometheusService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('PROMETHEUS_URL', 'http://localhost:9090'), '/');
    }

    public function query(string $query): array
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/api/v1/query', [
                'query' => $query
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Prometheus no respondió correctamente.',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'error' => null,
                'data' => $response->json()
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getFirstValue(string $query): ?float
    {
        $result = $this->query($query);

        if (!$result['success']) {
            return null;
        }

        $items = $result['data']['data']['result'] ?? [];

        if (count($items) === 0) {
            return null;
        }

        return (float) ($items[0]['value'][1] ?? 0);
    }

    public function isAvailable(): bool
    {
        $result = $this->query('up');
        return $result['success'];
    }
}
```

## `app/Services/IncidentPriorityService.php`

```php
<?php

namespace App\Services;

use App\Models\Alert;

class IncidentPriorityService
{
    public function calculate(Alert $alert): string
    {
        $clientPriority = $this->getClientPriority($alert);
        $metric = strtolower($alert->metric_name);
        $severity = strtolower($alert->severity);

        if ($clientPriority === 'high' && $severity === 'critical') {
            return 'critical';
        }

        if ($clientPriority === 'high' && str_contains($metric, 'disco')) {
            return 'critical';
        }

        if ($clientPriority === 'high' && $severity === 'warning') {
            return 'high';
        }

        if ($severity === 'critical') {
            return 'high';
        }

        if ($clientPriority === 'medium' && $severity === 'warning') {
            return 'medium';
        }

        return 'low';
    }

    public function recommendedAction(Alert $alert): string
    {
        $metric = strtolower($alert->metric_name);

        if (str_contains($metric, 'cpu')) {
            return 'Revisar procesos activos, consumo de recursos y servicios web asociados al servidor.';
        }

        if (str_contains($metric, 'memoria')) {
            return 'Revisar consumo de memoria, procesos en ejecución y posibles servicios saturados.';
        }

        if (str_contains($metric, 'disco')) {
            return 'Revisar espacio disponible, logs, archivos temporales, backups y crecimiento de archivos del sitio.';
        }

        return 'Revisar el estado general del servidor y validar el impacto sobre los sitios asociados.';
    }

    private function getClientPriority(Alert $alert): string
    {
        $server = $alert->server;

        if (!$server || $server->sites->count() === 0) {
            return 'medium';
        }

        $site = $server->sites->first();

        if (!$site || !$site->client) {
            return 'medium';
        }

        return strtolower($site->client->priority_level ?? 'medium');
    }
}
```

---

# Comandos Artisan

## `app/Console/Commands/CheckServerAlerts.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Server;
use App\Services\PrometheusService;
use Illuminate\Console\Command;

class CheckServerAlerts extends Command
{
    protected $signature = 'alerts:check';
    protected $description = 'Consulta métricas del servidor en Prometheus y registra alertas operacionales.';

    public function handle(PrometheusService $prometheus): int
    {
        $server = Server::first();

        if (!$server) {
            $this->error('No existe servidor registrado.');
            return Command::FAILURE;
        }

        $metrics = [
            [
                'name' => 'CPU',
                'query' => '100 - (avg by(instance) (rate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)',
                'threshold' => 80,
                'type' => 'greater'
            ],
            [
                'name' => 'Memoria',
                'query' => '(node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100',
                'threshold' => 10,
                'type' => 'less'
            ],
            [
                'name' => 'Disco',
                'query' => '(node_filesystem_avail_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"}) * 100',
                'threshold' => 10,
                'type' => 'less'
            ],
        ];

        foreach ($metrics as $metric) {
            $value = $prometheus->getFirstValue($metric['query']);

            if ($value === null) {
                $this->warn("No se pudo obtener la métrica {$metric['name']}.");
                continue;
            }

            $shouldAlert = $metric['type'] === 'greater'
                ? $value >= $metric['threshold']
                : $value <= $metric['threshold'];

            if (!$shouldAlert) {
                $this->info("{$metric['name']} OK: {$value}");
                continue;
            }

            $openAlert = Alert::where('metric_name', $metric['name'])
                ->where('status', 'open')
                ->first();

            if ($openAlert) {
                $this->warn("Ya existe una alerta abierta para {$metric['name']}.");
                continue;
            }

            $severity = $value >= 90 || $value <= 5 ? 'critical' : 'warning';

            Alert::create([
                'server_id' => $server->id,
                'metric_name' => $metric['name'],
                'metric_value' => round($value, 2),
                'threshold_value' => $metric['threshold'],
                'severity' => $severity,
                'status' => 'open',
                'message' => "La métrica {$metric['name']} alcanzó el valor {$value}."
            ]);

            $this->info("Alerta registrada para {$metric['name']}.");
        }

        return Command::SUCCESS;
    }
}
```

## `app/Console/Commands/CheckSiteMetrics.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Models\SiteMetric;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckSiteMetrics extends Command
{
    protected $signature = 'site-metrics:check';
    protected $description = 'Consulta disponibilidad, código HTTP, tiempo de respuesta y uso de disco por sitio.';

    public function handle(): int
    {
        $sites = Site::where('status', 'active')->get();

        if ($sites->isEmpty()) {
            $this->warn('No existen sitios activos para revisar.');
            return Command::SUCCESS;
        }

        foreach ($sites as $site) {
            $start = microtime(true);
            $httpStatus = null;
            $availability = 'unavailable';

            try {
                $response = Http::timeout(10)->get($site->url);
                $httpStatus = $response->status();
                $availability = $response->successful() ? 'available' : 'unavailable';
            } catch (\Throwable $e) {
                $availability = 'unavailable';
            }

            $end = microtime(true);
            $responseTime = round(($end - $start) * 1000, 2);

            $diskUsageMb = null;

            if ($site->document_root && is_dir($site->document_root)) {
                $diskUsageMb = round($this->folderSize($site->document_root) / 1024 / 1024, 2);
            }

            SiteMetric::create([
                'site_id' => $site->id,
                'http_status' => $httpStatus,
                'response_time_ms' => $responseTime,
                'disk_usage_mb' => $diskUsageMb,
                'availability_status' => $availability,
                'checked_at' => now()
            ]);

            $this->info("Sitio revisado: {$site->name} - {$availability}");
        }

        return Command::SUCCESS;
    }

    private function folderSize(string $path): int
    {
        $size = 0;

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }
}
```

---

# Controladores principales

## `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Site;
use App\Models\User;
use App\Services\PrometheusService;

class DashboardController extends Controller
{
    public function index(PrometheusService $prometheus)
    {
        $prometheusAvailable = $prometheus->isAvailable();

        $cpu = $prometheus->getFirstValue('100 - (avg by(instance) (rate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)');
        $memoryAvailable = $prometheus->getFirstValue('(node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100');
        $diskAvailable = $prometheus->getFirstValue('(node_filesystem_avail_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"}) * 100');

        $alerts = Alert::with(['server.sites.client'])
            ->latest()
            ->limit(10)
            ->get();

        $sites = Site::with(['client', 'server', 'latestMetric'])
            ->where('status', 'active')
            ->get();

        $users = User::orderBy('name')->get();

        return view('dashboard', compact(
            'prometheusAvailable',
            'cpu',
            'memoryAvailable',
            'diskAvailable',
            'alerts',
            'sites',
            'users'
        ));
    }
}
```

## `app/Http/Controllers/IncidentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Services\IncidentPriorityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function store(Request $request, Alert $alert, IncidentPriorityService $priorityService)
    {
        $data = $request->validate([
            'assigned_user_id' => ['required', 'exists:users,id']
        ]);

        $priority = $priorityService->calculate($alert);
        $recommendedAction = $priorityService->recommendedAction($alert);

        $incident = Incident::create([
            'alert_id' => $alert->id,
            'title' => 'Incidencia por alerta ' . $alert->metric_name,
            'description' => $alert->message,
            'status' => 'open',
            'priority' => $priority,
            'assigned_user_id' => $data['assigned_user_id']
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => 'Incidente creado',
            'comment' => 'Incidencia creada automáticamente desde alerta abierta.'
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => 'Acción sugerida',
            'comment' => $recommendedAction
        ]);

        return redirect('/dashboard')->with('message', 'Incidencia creada correctamente.');
    }

    public function close(Request $request, Incident $incident)
    {
        $data = $request->validate([
            'root_cause' => ['nullable', 'string'],
            'action_taken' => ['nullable', 'string']
        ]);

        $createdAt = Carbon::parse($incident->created_at, 'America/Santiago');
        $closedAt = Carbon::now('America/Santiago');
        $resolutionTime = max(1, $createdAt->diffInMinutes($closedAt));

        $incident->update([
            'status' => 'closed',
            'root_cause' => $data['root_cause'] ?? null,
            'action_taken' => $data['action_taken'] ?? null,
            'closed_at' => $closedAt,
            'resolved_at' => $closedAt,
            'resolution_time_minutes' => $resolutionTime
        ]);

        if ($incident->alert) {
            $incident->alert->update([
                'status' => 'closed',
                'closed_at' => $closedAt
            ]);
        }

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => 'Incidente cerrado',
            'comment' => 'Incidencia cerrada con tiempo de resolución de ' . $resolutionTime . ' minutos.'
        ]);

        return redirect('/mis-incidencias')->with('message', 'Incidencia cerrada correctamente.');
    }
}
```

## `app/Http/Controllers/IncidentLogController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentLog;
use Illuminate\Http\Request;

class IncidentLogController extends Controller
{
    public function store(Request $request, Incident $incident)
    {
        $data = $request->validate([
            'action' => ['required', 'string', 'max:150'],
            'comment' => ['nullable', 'string']
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => $data['action'],
            'comment' => $data['comment'] ?? null
        ]);

        return redirect('/mis-incidencias')->with('message', 'Acción registrada correctamente.');
    }
}
```

## `app/Http/Controllers/MyIncidentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class MyIncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::with(['alert.server.sites.client', 'logs'])
            ->where('assigned_user_id', Auth::id())
            ->latest()
            ->get();

        return view('incidents.my-incidents', compact('incidents'));
    }
}
```

## `app/Http/Controllers/ReportController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Incident;
use App\Models\Report;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function operational()
    {
        $alerts = Alert::with(['server.sites.client'])
            ->latest()
            ->limit(20)
            ->get();

        $incidents = Incident::with(['alert', 'assignedUser', 'logs'])
            ->latest()
            ->limit(20)
            ->get();

        $sites = Site::with(['client', 'latestMetric'])
            ->where('status', 'active')
            ->get();

        Report::create([
            'title' => 'Reporte operacional',
            'summary' => 'Reporte generado desde el sistema de observabilidad operacional.',
            'generated_by' => Auth::id(),
            'generated_at' => now()
        ]);

        return view('reports.operational', compact('alerts', 'incidents', 'sites'));
    }
}
```

---

# Automatización con Cron

Editar crontab:

```bash
crontab -e
```

Agregar:

```cron
* * * * * cd /var/www/observabilidad-app && php artisan alerts:check >> /var/www/observabilidad-app/storage/logs/alerts-check.log 2>&1
*/5 * * * * cd /var/www/observabilidad-app && php artisan site-metrics:check >> /var/www/observabilidad-app/storage/logs/site-metrics-check.log 2>&1
```

Verificar:

```bash
crontab -l
```

Ver logs:

```bash
tail -n 40 /var/www/observabilidad-app/storage/logs/alerts-check.log
tail -n 40 /var/www/observabilidad-app/storage/logs/site-metrics-check.log
```

---

# Pruebas automatizadas

## Test unitario: `tests/Unit/IncidentPriorityServiceTest.php`

```php
<?php

namespace Tests\Unit;

use App\Models\Alert;
use App\Models\Client;
use App\Models\Server;
use App\Models\Site;
use App\Services\IncidentPriorityService;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class IncidentPriorityServiceTest extends TestCase
{
    public function test_high_priority_client_with_critical_alert_returns_critical_priority(): void
    {
        $alert = $this->makeAlertWithClientPriority('high', 'CPU', 'critical');
        $service = new IncidentPriorityService();
        $this->assertEquals('critical', $service->calculate($alert));
    }

    public function test_high_priority_client_with_warning_alert_returns_high_priority(): void
    {
        $alert = $this->makeAlertWithClientPriority('high', 'CPU', 'warning');
        $service = new IncidentPriorityService();
        $this->assertEquals('high', $service->calculate($alert));
    }

    public function test_medium_priority_client_with_warning_alert_returns_medium_priority(): void
    {
        $alert = $this->makeAlertWithClientPriority('medium', 'CPU', 'warning');
        $service = new IncidentPriorityService();
        $this->assertEquals('medium', $service->calculate($alert));
    }

    public function test_critical_alert_without_client_context_returns_high_priority(): void
    {
        $alert = new Alert([
            'metric_name' => 'CPU',
            'severity' => 'critical',
        ]);

        $service = new IncidentPriorityService();
        $this->assertEquals('high', $service->calculate($alert));
    }

    public function test_cpu_alert_returns_cpu_recommended_action(): void
    {
        $alert = new Alert([
            'metric_name' => 'CPU',
            'severity' => 'warning',
        ]);

        $service = new IncidentPriorityService();

        $this->assertStringContainsString(
            'Revisar procesos activos',
            $service->recommendedAction($alert)
        );
    }

    public function test_disk_alert_returns_disk_recommended_action(): void
    {
        $alert = new Alert([
            'metric_name' => 'Disco',
            'severity' => 'warning',
        ]);

        $service = new IncidentPriorityService();

        $this->assertStringContainsString(
            'Revisar espacio disponible',
            $service->recommendedAction($alert)
        );
    }

    private function makeAlertWithClientPriority(
        string $clientPriority,
        string $metricName,
        string $severity
    ): Alert {
        $client = new Client([
            'name' => 'Cliente Demo',
            'priority_level' => $clientPriority,
        ]);

        $site = new Site([
            'name' => 'Sitio Demo',
            'domain' => 'demo.local',
        ]);

        $site->setRelation('client', $client);

        $server = new Server([
            'name' => 'Servidor Demo',
            'ip_address' => '127.0.0.1',
        ]);

        $server->setRelation('sites', new Collection([$site]));

        $alert = new Alert([
            'metric_name' => $metricName,
            'severity' => $severity,
        ]);

        $alert->setRelation('server', $server);

        return $alert;
    }
}
```

## Test funcional: `tests/Feature/AccessControlTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccessControlTest extends TestCase
{
    public function test_login_page_can_be_displayed(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Iniciar sesión');
    }

    public function test_register_page_can_be_displayed(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_my_incidents_requires_authentication(): void
    {
        $response = $this->get('/mis-incidencias');

        $response->assertRedirect('/login');
    }

    public function test_operational_report_requires_authentication(): void
    {
        $response = $this->get('/reports/operational');

        $response->assertRedirect('/login');
    }

    public function test_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }
}
```

## Test base corregido: `tests/Feature/ExampleTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }
}
```

Ejecutar:

```bash
php artisan test
```

---

# Comandos útiles de operación

## Laravel

```bash
php artisan serve --host=0.0.0.0 --port=8000
php artisan route:list
php artisan migrate:status
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan test
```

## Prometheus

```bash
sudo systemctl status prometheus
curl "http://localhost:9090/api/v1/query?query=up"
curl "http://localhost:9090/api/v1/query?query=node_memory_MemAvailable_bytes"
```

## Node Exporter

```bash
sudo systemctl status node_exporter
curl http://localhost:9100/metrics | head
```

## MySQL

```bash
mysql -u observabilidad_user -p observabilidad_db
```

```sql
SHOW TABLES;
SELECT * FROM alerts ORDER BY id DESC;
SELECT * FROM incidents ORDER BY id DESC;
SELECT * FROM incident_logs ORDER BY id DESC;
SELECT * FROM site_metrics ORDER BY id DESC;
```

## Cron

```bash
crontab -l
tail -n 40 storage/logs/alerts-check.log
tail -n 40 storage/logs/site-metrics-check.log
```

## Git

```bash
git status
git log --oneline -5
git add .
git commit -m "Actualizar documentación del proyecto"
git push
```

---

# Solución de errores comunes

## Error: `SQLSTATE[HY000] [1045] Access denied`

Revisar `.env`:

```env
DB_DATABASE=observabilidad_db
DB_USERNAME=observabilidad_user
DB_PASSWORD=tu_password_segura
```

Limpiar caché:

```bash
php artisan config:clear
```

Probar conexión:

```bash
mysql -u observabilidad_user -p observabilidad_db
```

---

## Error: `The stream or file storage/logs/laravel.log could not be opened`

Corregir permisos:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## Error: Prometheus no responde

Verificar servicio:

```bash
sudo systemctl status prometheus
```

Reiniciar:

```bash
sudo systemctl restart prometheus
```

Probar API:

```bash
curl "http://localhost:9090/api/v1/query?query=up"
```

---

## Error: Node Exporter no aparece UP en Prometheus

Verificar servicio:

```bash
sudo systemctl status node_exporter
```

Verificar puerto:

```bash
curl http://localhost:9100/metrics | head
```

Revisar `prometheus.yml`:

```yaml
- job_name: 'node'
  static_configs:
    - targets: ['localhost:9100']
```

Reiniciar Prometheus:

```bash
sudo systemctl restart prometheus
```

---

## Error: `php artisan test` falla porque `/` devuelve 302

En este proyecto `/` redirige a `/dashboard`, por lo tanto el test debe validar redirección y no estado 200.

Archivo correcto:

```php
$response = $this->get('/');
$response->assertRedirect('/dashboard');
```

---

## Error: el tiempo de resolución aparece incorrecto

Revisar zona horaria en `.env`:

```env
APP_TIMEZONE=America/Santiago
```

Limpiar caché:

```bash
php artisan config:clear
```

En controlador usar zona horaria explícita:

```php
$createdAt = Carbon::parse($incident->created_at, 'America/Santiago');
$closedAt = Carbon::now('America/Santiago');
$resolutionTime = max(1, $createdAt->diffInMinutes($closedAt));
```

---

# Seguridad y versionamiento

## Archivos que NO deben subirse a GitHub

```text
.env
vendor/
node_modules/
storage/logs/*.log
bootstrap/cache/*.php
```

Ejemplo de `.gitignore`:

```gitignore
.env
.env.*
!.env.example
/vendor
/node_modules
/storage/logs/*
/storage/framework/cache/*
/storage/framework/sessions/*
/storage/framework/views/*
/bootstrap/cache/*
```

## Verificar que no se subió `.env`

```bash
git status --ignored
```

## Subir cambios

```bash
git add .
git commit -m "Actualizar README del proyecto"
git push
```

## Repositorio privado

```text
https://github.com/MayorinesEthan/sistema-observabilidad-operacional-taller-integracion
```

---

# Checklist final de instalación

Antes de dar el proyecto por operativo, verificar:

```text
[ ] Proyecto clonado o creado en /var/www/observabilidad-app
[ ] composer install ejecutado
[ ] npm install ejecutado
[ ] .env configurado
[ ] APP_KEY generada
[ ] MySQL activo
[ ] Base de datos observabilidad_db creada
[ ] Usuario observabilidad_user con permisos
[ ] Migraciones o SQL ejecutado
[ ] Datos demo cargados
[ ] Prometheus activo
[ ] Node Exporter activo
[ ] curl a Prometheus responde success
[ ] php artisan serve funciona
[ ] /login carga correctamente
[ ] /dashboard requiere autenticación
[ ] php artisan alerts:check funciona
[ ] php artisan site-metrics:check funciona
[ ] Cron configurado
[ ] Logs de cron generándose
[ ] php artisan test aprobado
[ ] Repositorio GitHub actualizado
[ ] .env no subido a GitHub
```

---

# Resumen de defensa técnica del proyecto

El proyecto no se limita a instalar herramientas de monitoreo. Prometheus y Node Exporter se utilizan como fuente técnica de métricas, mientras que Laravel y MySQL implementan la capa operacional propia del sistema.

La aplicación permite:

```text
métrica técnica → alerta contextualizada → cliente/sitio → incidencia asignada → bitácora → cierre → tiempo de resolución → reporte
```

Esto evidencia desarrollo propio en:

- backend Laravel;
- integración con API Prometheus;
- modelamiento relacional MySQL;
- autenticación;
- controladores y rutas protegidas;
- comandos Artisan;
- automatización con Cron;
- reportes operacionales;
- pruebas unitarias y funcionales;
- control de versiones en GitHub.


