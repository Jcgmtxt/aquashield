# AquaShield

Sistema de gestión para servicios de lavado y protección automotriz.

## 🚀 Inicio Rápido

### Requisitos
- PHP 8.2+
- Node.js 18+
- Composer

### Instalación

```bash
# Clonar e instalar dependencias
git clone <repository-url>
cd aquashield
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Crear base de datos
touch database/database.sqlite
php artisan migrate --seed

# Iniciar desarrollo
composer run dev
```

El proyecto estará disponible en: http://localhost:8000

## 🛠️ Comandos Útiles

```bash
# Solo backend
php artisan serve

# Solo frontend  
npm run dev

# Ejecutar tests
composer run test

# Formatear código
npm run format
```

## 📱 Stack Tecnológico

- **Backend:** Laravel 12
- **Frontend:** React + TypeScript
- **UI:** Tailwind CSS + Radix UI
- **Base de datos:** SQLite
- **Autenticación:** Laravel Breeze
