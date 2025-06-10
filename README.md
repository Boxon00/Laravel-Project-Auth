# Laravel Auth App

**Moderna autentikacija aplikacija sa Laravel backend-om i vanilla JavaScript frontend-om**

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![Sanctum](https://img.shields.io/badge/Laravel-Sanctum-FF2D20?style=for-the-badge)](https://laravel.com/docs/sanctum)

## 📋 Pregled

Ova aplikacija predstavlja kompletno rešenje za autentikaciju korisnika, implementiranu sa Laravel 12 backend-om i modernim vanilla JavaScript frontend-om. Koristi Laravel Sanctum za API token autentikaciju i pruža potpunu funkcionalnost registracije, prijave i upravljanja korisničkim sesijama.

## ✨ Funkcionalnosti

### 🔐 Autentikacija
- **Registracija korisnika** sa validacijom podataka
- **Prijava/odjava** sa token-based autentikacijom
- **Zaštićene rute** sa Sanctum middleware
- **Automatsko upravljanje tokenima** (30-dnevno važenje)

### 🛡️ Bezbednost
- **Server-side validacija** svih korisničkih unosa
- **Client-side validacija** za bolju UX
- **Password hashing** sa bcrypt algoritmom
- **CORS konfiguracija** za cross-origin zahteve

### 🎨 UI/UX
- **Responzivni dizajn** optimizovan za sve uređaje
- **Moderna UI** sa smooth animacijama
- **Real-time validacijske poruke**
- **Test nalozi** za lako testiranje

## 🏗️ Arhitektura

```
Auth-App/
├── Auth-app-be/              # Laravel Backend
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   └── AuthController.php
│   │   └── Models/
│   │       └── User.php
│   ├── config/
│   ├── database/
│   ├── routes/
│   │   └── api.php
│   └── ...
└── Auth-app-fe/              # Frontend
    ├── index.html
    ├── script.js
    ├── styles.css
    └── ...
```

## 🚀 Instalacija

### Preduslovи
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL/HeidiSQL

### Backend Setup

```bash
cd Auth-app-be

# Instaliranje dependencies
composer install

# Kreiranje .env fajla
cp .env.example .env

# Generisanje app key
php artisan key:generate

# Pokretanje migracija
php artisan migrate

# Seedovanje test podataka
php artisan db:seed

# Pokretanje servera
php artisan serve
```

### Frontend Setup

```bash
cd Auth-app-fe

# Pokretanje sa bilo kojim HTTP serverom
# Python
python -m http.server 3000

# Node.js (http-server)
npx http-server -p 3000

# Live Server extension u VS Code
```

## 🔧 Konfiguracija

### Environment Variables (.env)

```env
APP_NAME="Laravel Auth App"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1
```

### CORS Setup

Aplikacija je prekonfigurirana za rad sa frontend-om na portu 3000. Za promenu portova, ažurirajte `SANCTUM_STATEFUL_DOMAINS` u `.env` fajlu.

## 📡 API Endpointi

### Javni endpointi

| Metod | Endpoint | Opis |
|-------|----------|------|
| POST | `/api/auth/register` | Registracija novog korisnika |
| POST | `/api/auth/login` | Prijava korisnika |
| GET | `/api/test` | Test endpoint |

### Zaštićeni endpointi

| Metod | Endpoint | Opis | Headers |
|-------|----------|------|---------|
| GET | `/api/user` | Podaci trenutnog korisnika | `Authorization: Bearer {token}` |
| POST | `/api/logout` | Odjava korisnika | `Authorization: Bearer {token}` |

### Request/Response primeri

#### Registracija
```javascript
// Request
POST /api/auth/register
{
  "name": "Marko Petrović",
  "email": "marko@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

// Response
{
  "success": true,
  "message": "Uspešno ste se registrovali!",
  "user": {
    "id": 1,
    "name": "Marko Petrović",
    "email": "marko@example.com",
    "created_at": "2025-06-10T12:00:00.000000Z"
  },
  "token": "1|abc123..."
}
```

#### Prijava
```javascript
// Request
POST /api/auth/login
{
  "email": "marko@example.com",
  "password": "password123"
}

// Response
{
  "success": true,
  "message": "Uspešno ste se prijavili!",
  "user": { /* user data */ },
  "token": "2|def456..."
}
```

## 🧪 Test Nalozi

Aplikacija dolazi sa predefinsanim test nalozima:

| Tip | Email | Password |
|-----|-------|----------|
| Administrator | `admin@test.com` | `admin123` |
| Običan korisnik | `user@test.com` | `user123` |

## 🛠️ Development

### Pokretanje testova

```bash
# Backend testovi
cd Auth-app-be
php artisan test

# Ili specific test
php artisan test --filter AuthTest
```

### Database reset

```bash
php artisan migrate:fresh --seed
```

### Debugging

```bash
# Real-time logs
php artisan pail

# Ili standardni logs
tail -f storage/logs/laravel.log
```

## 📁 Ključne komponente

### Backend

#### AuthController
Centralni kontroler za sve autentikacione operacije:
- Validacija korisničkih unosa
- Hash-ovanje lozinki
- Generisanje Sanctum tokena
- Error handling

#### User Model
Eloquent model sa:
- Sanctum token funkcionalnostima
- Mass assignment protection
- Password casting

#### API Routes
RESTful rute sa middleware zaštitom i grupiranjem.

### Frontend

#### script.js
- Async/await API komunikacija
- Real-time validacija
- Token management
- State upravljanje

#### styles.css
- Mobile-first responzivni dizajn
- CSS Grid i Flexbox layout
- Smooth animacije
- Error states

## 🔒 Bezbednosni aspekti

- **Input sanitization** - svi unosi se validiraju i sanitizuju
- **SQL injection protection** - Eloquent ORM
- **XSS protection** - proper output escaping
- **CSRF protection** - Laravel built-in
- **Rate limiting** - moguće dodati na API rute
- **Token expiration** - 30-dnevno važenje tokena

## 🚀 Production Deployment

### Backend

```bash
# Optimizacija
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Environment
APP_ENV=production
APP_DEBUG=false
```

### Frontend

```bash
# Build optimizacija
# Koristiti bundler poput Vite, Webpack ili Parcel
# Minifikacija CSS/JS fajlova
# Image optimizacija
```

## 🎓 Projektni detalji

**Fakultetski projekat** - Demonstracija moderne web autentikacije

### Implementirane funkcionalnosti:
- ✅ Laravel 12 backend sa Sanctum autentikacijom
- ✅ Vanilla JavaScript frontend bez framework-a
- ✅ RESTful API komunikacija
- ✅ Kompletna CRUD funkcionalnost za korisnike
- ✅ Responzivni dizajn
- ✅ Bezbednosna validacija podataka
- ✅ Token-based session management
