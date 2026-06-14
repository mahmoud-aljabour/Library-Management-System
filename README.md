# Library Management System

A full-featured library management application built with **Laravel 12** for managing books, members, borrowings, authors, publishers, categories, and reviews. Includes role-based access control and a RESTful API secured with Sanctum.

![Tests](https://github.com/mahmoud-aljabour/Library-Management-System/actions/workflows/tests.yml/badge.svg)

## Features

- **Books** — CRUD, inventory tracking, ISBN, categories, status management
- **Members** — CRUD, active/inactive toggle, borrowing history
- **Borrowings** — borrow/return workflow, overdue detection, max borrow limit
- **Authors / Publishers / Categories** — full management with delete guards
- **Reviews** — ratings and comments on books
- **Dashboard** — statistics, overdue alerts, recent activity
- **API** — Sanctum-authenticated REST endpoints for books, members, and borrowings
- **Roles** — `admin` (full access) and `librarian` (borrowings + reviews, read-only management)

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, AdminLTE, custom CSS |
| Database | MySQL (SQLite for tests) |
| API Auth | Laravel Sanctum |
| Testing | Pest PHP |
| Build | Vite, Composer |

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- MySQL 8+ (or MariaDB)

## Quick Start

```bash
# Clone and install
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database (create laravel_library_system in MySQL first)
php artisan migrate --seed

# Run
php artisan serve
```

Open `http://localhost:8000` and sign in with a demo account (see below).

### Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@library.com` | `password` |
| Librarian | `librarian@library.com` | `password` |

> Public registration is disabled. Only seeded staff accounts can log in.

## Configuration

Add these to `.env` to customize library rules:

```env
LIBRARY_MAX_BORROWINGS=3      # Max active books per member
LIBRARY_DEFAULT_BORROW_DAYS=14  # Default loan period in days
```

## Testing

Tests use SQLite in-memory (configured in `phpunit.xml`). No MySQL setup is required.

```bash
composer test          # Run all tests
composer pint          # Format code
composer ci            # Pint check + tests (same as CI)
```

## API

Authenticate via `POST /api/login`:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@library.com","password":"password"}'
```

Use the returned Bearer token for protected routes:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/user` | Current user profile |
| POST | `/api/logout` | Revoke token |
| GET/POST/PUT/DELETE | `/api/books` | Books CRUD |
| GET/POST/PUT/DELETE | `/api/members` | Members CRUD |
| GET/POST/PUT/DELETE | `/api/borrowings` | Borrowings CRUD |

Rate limit: 60 requests/minute per user.

## Scheduled Tasks

Overdue borrowings are marked automatically:

```bash
# Runs daily via scheduler
php artisan library:mark-overdue
```

Add to cron (production):

```cron
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Deployment (XAMPP / Apache)

1. Point the virtual host document root to the `public/` directory.
2. Set `APP_ENV=production`, `APP_DEBUG=false` in `.env`.
3. Run optimizations:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

4. Ensure `storage/` and `bootstrap/cache/` are writable.
5. Configure the scheduler cron and queue worker if using database queues:

```bash
php artisan queue:work --daemon
```

## Health Check

Laravel exposes a health endpoint at `/up` for uptime monitoring.

## Project Structure

```
app/
  Http/Controllers/   # Web + API controllers
  Policies/           # Role-based authorization
  Services/           # BorrowingService business logic
  Enums/              # UserRole
resources/views/      # Blade templates (AdminLTE)
routes/web.php        # Web routes (auth required)
routes/api.php        # API routes (Sanctum)
tests/                # Pest feature + unit tests
```

## License

MIT
