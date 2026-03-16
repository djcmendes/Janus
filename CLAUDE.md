# CLAUDE.md

> [!CAUTION]
> **CRITICAL RULE**: Do NOT autonomously use or load any workflows from the `.agents/skills/` directory. You must act as a standard, lightweight coding assistant. ONLY access the `.agents/skills/` directory if the user explicitly types `@skill [name]` in their prompt.

### CRITICAL GIT WORKFLOW & SAFETY RULES
You are operating in a strict version control environment. You must adhere to the following Git rules without exception:

1. **NEVER COMMIT TO MAIN:** You are strictly forbidden from committing directly to the `main` or `master` branches. 
2. **ALWAYS USE FEATURE BRANCHES:** Before staging or committing any code, you must create and checkout a new feature branch (e.g., `git checkout -b feature/brief-description`).
3. **NO DESTRUCTIVE COMMANDS:** You must NEVER run destructive Git commands. This means no `git reset --hard`, no `git push --force`, and no deleting branches.
4. **ALWAYS MOVE FORWARD:** Treat Git history as append-only. If you make a mistake, do not attempt to rewrite history. Instead, write a new commit to fix or revert the mistake and move forward.

This file provides guidance to AI coding assistants (Claude, Gemini, etc.) when working with code in this repository.

## Project Overview

**Janus** is a headless CMS and Real-Time Data Platform — a production-quality rebuild of the [Directus](https://directus.io) feature set using a modern PHP/TypeScript stack.

| Layer | Technology | Directory |
|---|---|---|
| Backend API | Symfony 7 (PHP 8.3) | `backend.janus.com/` |
| Frontend Admin | Angular 19 (SSR) | `janus.com/` |
| Database | MariaDB 11.4 | Docker service |
| Cache | Redis 7 | Docker service |
| Message Broker | RabbitMQ 3.13 | Docker service |
| Reverse Proxy | Nginx 1.27 | `nginx/conf.d/` |

The `examples/` folder is a local clone of the Directus monorepo and serves as the **reference implementation only**. Never modify it.

---

## Requirements

- Docker + Docker Compose
- PHP 8.3 + Composer (for local backend development without Docker)
- Node.js 22 + npm (for local frontend development without Docker)

---

## Common Commands

```bash
# Start the full stack
make up

# Stop everything
make down

# Tear down including volumes (fresh start)
make reset

# Run database migrations
make migrate

# Open a backend shell
make shell-backend

# Open a frontend shell
make shell-frontend

# Run all tests
make tests
```

---

## Backend Architecture (`backend.janus.com/src/`)

Follows **Onion Architecture with CQRS**. Every module has the same folder structure:

```
src/{Module}/
  Domain/
    Entity/       ← Pure PHP classes, zero framework dependencies
    Repository/   ← Interfaces only
    Service/      ← Business logic
    Exception/    ← Domain exceptions
  Application/
    Command/      ← Write operation payloads (CQRS)
    Query/        ← Read operation payloads (CQRS)
    Handler/      ← Command/Query handlers
    DTO/          ← Request/Response shapes
  Infrastructure/
    Repository/   ← Doctrine implementations of Domain interfaces
    Persistence/  ← Doctrine entities and migrations
  Presentation/
    Controller/   ← Thin HTTP controllers (call Application handlers)
    DTO/          ← Request validation / response serialization
```

### The Heimdall Module

Every controller must use `RequestGuard` to validate requests:

```php
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;

// In constructor:
public function __construct(private readonly RequestGuard $guard) {}

// In action method:
$this->guard->validate_webservice_request(ApiVersion::V1, ApiScope::AUTHENTICATED);
$this->guard->authorize(Client::WEB, Client::IOS);
$userId = $this->guard->validate_authenticated_user_id();
```

### Response Format

All endpoints return JSON in the Directus-compatible envelope format:

```json
// Single item
{ "data": { ... } }

// Collection
{ "data": [ ... ], "meta": { "total_count": 100, "filter_count": 25 } }

// Error
{ "errors": [ { "message": "...", "extensions": { "code": "..." } } ] }
```

### Running Backend Tests

```bash
# Inside Docker
make test-backend

# Locally
cd backend.janus.com && php bin/phpunit
```

---

## Frontend Architecture (`janus.com/src/app/`)

Angular 19 standalone components. **No NgModules** — everything is standalone or lazy-loaded.

```
src/app/
  core/
    services/
      auth.service.ts    ← JWT storage, login/logout, signals-based state
      api.service.ts     ← Base HTTP client (use this, not HttpClient directly)
    guards/
      auth.guard.ts      ← Redirects to /login if unauthenticated
    interceptors/
      auth.interceptor.ts ← Injects Bearer token, handles 401
    layout/
      app-shell/         ← Authenticated layout wrapper
      sidebar-nav/       ← Module navigation
  features/
    auth/                ← Login, register, forgot-password, etc. (public)
    content/             ← Dynamic collection/item management
    files/               ← File and folder management
    users/               ← User CRUD
    activity/            ← Read-only activity log
    insights/            ← Analytics dashboards
    deployment/          ← Deployment provider management
    visual/              ← Visual editing
    settings/            ← All settings sub-pages
  shared/                ← Reusable components, pipes, directives
```

### Key Conventions

- **Signals everywhere** — Use `signal()`, `computed()`, and `effect()` for reactive state. Avoid `BehaviorSubject` for new code.
- **Standalone components only** — No `NgModule`. Every component declares its own `imports: []`.
- **Use `ApiService`** for all HTTP calls — never inject `HttpClient` directly in feature code.
- **Lazy-load all feature modules** — routes use `loadChildren` / `loadComponent`.
- **Auth state** is readable from `AuthService.user()` (signal) and `AuthService.isAuthenticated()` (computed).

### Running Frontend Tests

```bash
# Unit tests (Vitest)
make test-frontend
# locally: cd janus.com && npm run test

# E2E tests (Playwright)
make test-e2e
# locally: cd janus.com && npm run e2e

# Build (verify compilation)
cd janus.com && npm run build
```

---

## Adding a New Backend Module

1. Create the directory structure under `src/{ModuleName}/`
2. Start with the `Domain/Entity/` class (pure PHP, Doctrine attributes)
3. Create the `Infrastructure/Repository/` (extends `ServiceEntityRepository`)
4. Create the `Presentation/Controller/` (extends `AbstractController`)
5. Wire security: use `RequestGuard` from Heimdall in the constructor
6. Generate and run a Doctrine migration: `make shell-backend` → `php bin/console doctrine:migrations:diff && php bin/console doctrine:migrations:migrate`
7. Write a PHPUnit feature test in `tests/`

## Adding a New Frontend Feature Page

1. Create the component file in `src/app/features/{module}/pages/{page-name}/{page-name}.ts`
2. Add the route to `src/app/features/{module}/{module}.routes.ts`
3. Create a corresponding service in `src/app/features/{module}/services/{module}.service.ts` that uses `ApiService`
4. Write a Vitest unit test for the service

---

## Code Style

### PHP (Backend)
- `declare(strict_types=1);` at the top of **every** file
- `final` classes by default — only remove `final` when extension is explicitly needed
- Type hints on all properties, parameters, and return types
- No `public` properties — always private with getters/setters

### TypeScript (Frontend)
- `readonly` for all injected services and signals
- Prefer `inject()` function over constructor injection for new components
- Use `@for`, `@if`, `@switch` control flow (not `*ngFor`, `*ngIf`)
- Filenames: `kebab-case.ts`, class names: `PascalCase`

---

## Environment Variables

See `.env.example` at the project root for the full list. Key variables:

| Variable | Description |
|---|---|
| `DATABASE_URL` | MariaDB connection string |
| `REDIS_URL` | Redis connection string |
| `JWT_SECRET` | HMAC-SHA256 signing key |
| `JWT_ACCESS_TTL` | Access token TTL in seconds (default: 900) |
| `CORS_ALLOW_ORIGIN` | Allowed frontend origin |

---

## Reference

- Backend routes: [`examples-backend-routes.md`](./examples-backend-routes.md)
- Frontend routes: [`examples-routes.md`](./examples-routes.md)
- Full build plan: [`BUILDING_PLAN.md`](./BUILDING_PLAN.md)
- Concept / original reference: [`d_concept.md`](./d_concept.md)
