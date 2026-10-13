# test-manager-library-3145556-api

Backend de **Manager Library**, en **PHP 8.2 + Laravel 12**, con arquitectura hexagonal estricta y Sanctum.

## Estado

- Tareas T-01 a T-13 del spec (autenticación, libros, miembros, préstamos, devoluciones, reporte y bootstrap con admin) están implementadas.
- Endpoints disponibles bajo `/api/v1` (ver `spec-php/api-contract.md` en el repo `docs`).
- Pendientes: tests de dominio/servicios/arquitectura, infra completa y validación end-to-end.

## Requisitos

- PHP 8.2+
- Composer
- PostgreSQL 16 (o Docker)

## Variables de entorno

Ver `.env.example`. Mínimas para desarrollo local:

```env
APP_URL=http://localhost
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=library
DB_USERNAME=library
DB_PASSWORD=library
ADMIN_USERNAME=admin
ADMIN_PASSWORD=change-me
CORS_ORIGIN=http://localhost:5173
```

> Nota: una contraseña vacía es rechazada por validación.

## Ejecutar en local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

La API queda en `http://localhost:8000/api/v1`.

Salud:
```bash
curl http://localhost:8000/api/v1/health
```

## Tests

```bash
php artisan test
```

Para validar la migración con PostgreSQL, usar la configuración `DB_*` de Postgres.

## Arquitectura

Todo el dominio y los casos de uso viven en `src/Library/` bajo el namespace `Library\`. `app/` solo contiene glue de Laravel (providers, rutas, bootstrap).

```
src/Library/
├── domain/                # Entidades, value objects y excepciones de negocio
├── application/
│   ├── ports/outbound/    # Interfaces (repositorios, clock, hasher, token issuer, storage, etc.)
│   └── services/          # Casos de uso
├── adapters/
│   ├── inbound/http/      # Controllers, middleware y renderer de errores
│   └── outbound/          # Eloquent, almacenamiento, seguridad
└── bootstrap/composition/ # Único punto de composición (Article III)
```

