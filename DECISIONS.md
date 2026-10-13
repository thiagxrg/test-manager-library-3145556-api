# Decisiones técnicas — Manager Library (traducción a Laravel)

Este documento traduce [`plan.md`](../test-manager-library-3145556-docs/spec-python/plan.md) del spec
de referencia (Python) al stack real del reto: **React + Laravel**. El negocio, el modelo de datos y el
contrato de API no cambian — solo cómo se construyen.

## 1. Decisiones técnicas

| # | Decisión | Elección (Laravel) | Equivalente en el spec de referencia | Por qué |
|---|---|---|---|---|
| D-01 | Lenguaje y runtime | PHP 8.2 | Python 3.12 | Stack del reto |
| D-02 | Framework HTTP | Laravel 11 | FastAPI | Contrato en `api-contract.md` |
| D-03 | Persistencia | Eloquent + migraciones nativas de Laravel sobre PostgreSQL 16 | SQLAlchemy 2 + Alembic | El esquema sigue siendo propiedad de las migraciones (ADR-001) |
| D-04 | Concurrencia | `CHECK` de rango en `book` + transacción con bloqueo de fila (`lockForUpdate`) al descontar ejemplares | Testigo de versión + `CHECK` | Mismo objetivo: último ejemplar → un 201, un 409 (RNF-2) |
| D-05 | Fechas | Instantes UTC (`timestamptz`); reloj inyectable para pruebas | Puerto `Clock` | Probar vencimientos sin esperar 14 días |
| D-06 | Reporte | Consulta agregada única (`loan_item` agrupado por `book_id`, `book_title`, `category_name`) | Puerto de lectura agregado | Estable y barato |
| D-07 | Objetos de valor | `App\Domain\ValueObjects`: `Isbn`, `Quantity`, `DateRange` — PHP puro, sin Eloquent ni Laravel | `Isbn`, `Quantity`, `DateRange` | Viven dentro de la fila de su dueño, sin tabla; probables sin framework |
| D-08 | Portadas | Disco local (`storage/app/covers`) en desarrollo; la base guarda solo `cover_key` opaco | Disco/S3 detrás de puerto `CoverStorage` | Clave opaca, nunca ruta |
| D-09 | Contraseñas | Hash nativo de Laravel (bcrypt) detrás de `User::getAuthPassword()`; nunca se expone `password_hash` | Argon2id detrás de `PasswordHasher` | Artículo IX de la constitución |
| D-10 | Arranque | Seeder/comando de arranque crea el admin inicial desde `ADMIN_USERNAME` / `ADMIN_PASSWORD` | Igual | La base no puede producir el hash |
| D-11 | Autenticación | **Laravel Sanctum** (tokens personales de acceso), expiración configurada a 8 h | JWT HS256 firmado | Mismo contrato HTTP (`Authorization: Bearer <token>`); Sanctum es el estándar de Laravel para API tokens y evita reinventar firma/verificación |
| D-12 | Pruebas | PHPUnit/Pest: unitarias de dominio sin BD, de característica (feature) contra Postgres real | pytest con fakes + integración | Misma pirámide de pruebas |

## 2. Por qué Sanctum y no JWT manual (ADR corto)

- **Contexto:** el contrato (`api-contract.md`) solo exige `Authorization: Bearer <token>` y expiración de 8 horas; no exige que el token sea JWT.
- **Alternativas:** (a) JWT HS256 manual con `firebase/php-jwt`, igual que el spec de referencia; (b) Laravel Sanctum.
- **Decisión:** Sanctum. Es la solución nativa de Laravel para autenticación de API por token, reduce superficie de error (firma, expiración, revocación ya resueltas por el framework) y cumple el contrato igual de bien.
- **Consecuencia:** se acepta una tabla adicional (`personal_access_tokens`, fuera del esquema `library`, en `public`) que no aparece en `data-model.md` — es infraestructura de autenticación, no una entidad de negocio. Las 6 tablas del negocio en `library` no cambian.

## 3. Esquema de base de datos

Las tablas de negocio viven en el esquema `library` (`search_path=library,public`), exactamente como las describe
[`data-model.md`](../test-manager-library-3145556-docs/spec-python/data-model.md): `category`, `book`, `member`,
`user`, `loan`, `loan_item`. Ninguna columna tiene `DEFAULT`: los valores los pone el dominio (Artículo VI/VII).

## 4. Variables de entorno

| Variable | Obligatoria | Nota |
|---|---|---|
| `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | sí | Postgres 16 |
| `DB_SCHEMA` | no (default `library,public`) | search_path |
| `ADMIN_USERNAME` / `ADMIN_PASSWORD` | sí | Nunca en el repo; crea el admin inicial |
| `COVERS_DIR` | no | Carpeta de portadas en disco |
| `CORS_ORIGIN` | sí | Origen del frontend React |

## 5. Orden de construcción seguido

Esqueleto + `GET /health` → migración inicial (6 tablas + seed de categorías) → objetos de valor de dominio →
autenticación (Sanctum) → libros → socios → préstamos (con la transacción crítica) → devoluciones y vencidos →
reporte → pruebas de contrato → frontend React.
