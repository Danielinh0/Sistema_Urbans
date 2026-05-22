<h1 align="center">Sistema Urbans</h1>

<p align="center">
  <strong>Sistema integral de gestión para empresas de transporte urbano de pasajeros</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 4">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Flux_UI-2-7C3AED?style=for-the-badge" alt="Flux UI 2">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 8">
</p>

---

## Descripción

**Sistema Urbans** es una plataforma web diseñada para digitalizar y optimizar las operaciones diarias de una empresa de autobuses urbanos. Abarca desde la venta de boletos en taquilla, la gestión de corridas y rutas, hasta el control de paquetería, turnos de cajeros, reportes estadísticos y predicciones de demanda con inteligencia artificial.

El sistema implementa un esquema de **roles y permisos granulares** que permite a cada tipo de usuario (administrador, gerente, cajero, chofer) acceder únicamente a las funcionalidades que le corresponden, garantizando seguridad y orden operativo.

---

## Funcionalidades principales

### Venta de boletos
- Punto de venta rápido con selección de asientos interactiva
- Asociación de boletos a clientes registrados
- Reimpresión, cancelación y reembolso de boletos
- Aplicación y autorización de descuentos

### Paquetería
- Registro de paquetes con formulario detallado
- Resumen de pago y seguimiento de envíos
- Asociación de paquetes a corridas y rutas

### Rutas y Corridas
- CRUD completo de rutas con origen, destino y sucursales asociadas
- Gestión de corridas: programación, asignación de choferes y urbans
- Registro de salida y llegada de corridas en tiempo real
- Vista detallada de pasajeros por corrida

### Flota (Urbans)
- Registro y administración de unidades de transporte
- Control de estado (activo/inactivo) con soft deletes
- Asignación de asientos por unidad

### Socios y Clientes
- Gestión de socios propietarios de unidades
- Registro de clientes con datos personales y de dirección
- Historial de compras por cliente

### Sucursales
- Administración multi-sucursal
- Asignación de taquillas por sucursal
- Control de datos por alcance (sucursal propia o todas)

### Usuarios y Roles
- Gestión de usuarios con roles diferenciados
- Validaciones y bloqueo/desbloqueo de cuentas
- Sistema de permisos granulares con [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)

### Turnos y Taquillas
- Apertura y cierre de turnos por cajero
- Administración de taquillas (abrir, cerrar, monitorear)
- Middleware de control: funciones de venta bloqueadas sin turno activo

### Reportes
- Dashboard de reportes con gráficas interactivas (Chart.js)
- Gráfica de boletos vendidos por mes
- Gráfica de corridas realizadas
- Gráfica de rutas más utilizadas
- Resumen diario y widgets de taquillas

### Predicción de demanda
- Módulo de predicciones con inteligencia artificial
- Estimación de demanda futura por ruta
- Acceso exclusivo para administradores

---

## Arquitectura y Stack Tecnológico

| Capa | Tecnología |
|---|---|
| **Backend** | Laravel 13 (PHP 8.3+) |
| **Frontend reactivo** | Livewire 4 |
| **Componentes UI** | Flux UI 2 |
| **Estilos** | Tailwind CSS 4 |
| **Bundler** | Vite 8 |
| **Gráficas** | Chart.js 4 |
| **Autenticación** | Laravel Fortify (con 2FA) |
| **Autorización** | Spatie Laravel Permission |
| **Testing** | Pest PHP 4 |
| **Base de datos** | MySQL / SQLite |
| **Deploy** | Heroku (configurado) |

---

## Modelo de datos

El sistema cuenta con **22 modelos** interconectados:

```
Usuario ─── Turno ─── Taquilla ─── Sucursal
  │                      │
  │                    Venta ─── DetalleVenta
  │                      │
  ├── Boleto ────── BoletoCliente ─── Cliente
  │      │
  │      └──────── BoletoPaquete
  │
  ├── Corrida ─── Ruta ─── Sucursal (origen/destino)
  │      │
  │    Urban ─── Asiento
  │      │
  │    Socio
  │
  └── Prediccion

Direccion ─── Calle ─── Colonia ─── CodigoPostal ─── Estado ─── Pais
```

---

## Roles y permisos

El sistema define **4 roles** con permisos específicos:

| Rol | Descripción | Permisos clave |
|---|---|---|
| **Admin** | Acceso total al sistema | Todos los permisos (70+) |
| **Gerente** | Gestión operativa de sucursal | Urbans, socios, rutas, corridas, sucursales, usuarios, cajas, ventas, boletos |
| **Cajero** | Operaciones de punto de venta | Cajas, vender boletos, ver/crear ventas, datos de sucursal propia |
| **Chofer** | Registro de operación en ruta | Ver corridas, registrar salida/llegada, ver pasajeros |

---

## Instalación y configuración

### Prerrequisitos

- PHP >= 8.3
- Composer
- Node.js >= 18 y npm (o bun/pnpm)
- MySQL 8+ o SQLite

### Pasos de instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/Danielinh0/Sistema_Urbans.git
cd Sistema_Urbans

# 2. Instalar dependencias de PHP
composer install

# 3. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar la base de datos en .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=sistema_urbans
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Ejecutar migraciones y seeders
php artisan migrate --seed

# 6. Instalar dependencias de frontend
npm install

# 7. Levantar el servidor de desarrollo
composer dev
```

> Este último comando ejecuta simultáneamente el servidor de Laravel, la cola de trabajos y Vite.

### Credenciales por defecto

| Rol | Email | Contraseña |
|---|---|---|
| Admin | `admin@example.com` | `password` |

---

## Estructura del proyecto

```
Sistema_Urbans/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # 14 controladores (Dashboard, Venta, Corrida, etc.)
│   │   ├── Middleware/        # EnsureTurnoOpen, etc.
│   │   └── Responses/        # LoginResponse personalizado
│   ├── Models/                # 22 modelos Eloquent
│   └── Policies/              # 7 policies de autorización
├── database/
│   ├── factories/             # Factories para testing
│   ├── migrations/            # 34 migraciones
│   └── seeders/               # 23 seeders (incluye RolSeeder con permisos)
├── resources/
│   ├── views/
│   │   ├── components/        # Componentes Blade reutilizables
│   │   │   ├── corrida/       # Formularios, tablas y detalles de corridas
│   │   │   ├── rutas/         # Gestión de rutas
│   │   │   ├── socio/         # Gestión de socios
│   │   │   ├── urban/         # Gestión de urbans
│   │   │   ├── usuario/       # Gestión de usuarios
│   │   │   ├── paqueteria/    # Registro y resumen de paquetes
│   │   │   ├── reportes/      # Gráficas y widgets
│   │   │   └── taquilla/      # Administración de taquillas
│   │   ├── flux/icon/         # Íconos SVG personalizados
│   │   └── layouts/           # Layouts de app y autenticación
│   └── css/                   # Estilos globales
├── routes/
│   └── web.php                # Definición de rutas (15 grupos)
├── tests/                     # Tests con Pest PHP
├── Procfile                   # Configuración de Heroku
└── vite.config.js             # Configuración de Vite
```

---

## Testing

El proyecto utiliza **Pest PHP** como framework de testing:

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar con lint check
composer test

# Ejecutar un test específico
php artisan test --filter=AuthenticationTest
```

---

## Deploy

El proyecto está preparado para desplegarse en **Heroku**:

```bash
# El Procfile ya está configurado
# El script heroku-postbuild compila los assets automáticamente
git push heroku main
```

---

## Equipo de desarrollo

Proyecto desarrollado de forma colaborativa como parte de un sistema de gestión empresarial de transporte.

---

## Licencia

Este proyecto se distribuye bajo la licencia [MIT](https://opensource.org/licenses/MIT).
