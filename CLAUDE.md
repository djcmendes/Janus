# CLAUDE.md

> [!CAUTION]
> **CRITICAL RULE**: Do NOT autonomously use or load any workflows from the `.agents/skills/` directory. You must act as a standard, lightweight coding assistant. ONLY access the `.agents/skills/` directory if the user explicitly types `@skill [name]` in their prompt.

### CRITICAL GIT WORKFLOW & SAFETY RULES
You are operating in a strict version control environment. You must adhere to the following Git rules without exception:

1. **NEVER COMMIT TO MAIN:** You are strictly forbidden from committing directly to the `main` or `master` branches.
2. **ALWAYS USE FEATURE BRANCHES:** Before staging or committing any code, you must create and checkout a new feature branch (e.g., `git checkout -b feature/brief-description`).
3. **NO DESTRUCTIVE COMMANDS:** You must NEVER run destructive Git commands. This means no `git reset --hard`, no `git push --force`, and no deleting branches.
4. **ALWAYS MOVE FORWARD:** Treat Git history as append-only. If you make a mistake, do not attempt to rewrite history. Instead, write a new commit to fix or revert the mistake and move forward.

---

## Project Overview

**Janus** is a standalone headless CMS and Real-Time Data Platform built from scratch.

| Layer | Technology | Directory |
|---|---|---|
| Backend API | Symfony 7 (PHP 8.3) | `backend.janus.com/` |
| Frontend Admin | Angular 19 (SSR) | `janus.com/` |
| Database | MariaDB 11.4 | Docker service |
| Cache | Redis 7 | Docker service |
| Message Broker | RabbitMQ 3.13 | Docker service |
| Reverse Proxy | Nginx 1.27 | `nginx/conf.d/` |

> The `examples/` folder is a local reference clone — never modify it and never import from it.

---

## Common Commands

```bash
make up              # Start full Docker stack
make down            # Stop all services
make reset           # Tear down + remove volumes + rebuild
make migrate         # Run Doctrine migrations
make shell-backend   # Open bash in backend container
make shell-frontend  # Open sh in frontend container
make test-backend    # PHPUnit
make test-frontend   # Karma + Jasmine
make test-e2e        # Playwright
make tests           # Run all tests
```

---

## Backend Architecture (`backend.janus.com/src/`)

**Onion Architecture + CQRS.** Every module has the same folder structure:

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

### Heimdall — Every Controller Must Use This

```php
use App\Heimdall\Domain\Enum\ApiScope;   // LOCAL | PUBLIC | AUTHENTICATED
use App\Heimdall\Domain\Enum\ApiVersion; // V100, V200
use App\Heimdall\Domain\Enum\Client;     // ANDROID | IOS | WEB | CLI
use App\Heimdall\Domain\Service\RequestGuard;

public function __construct(private readonly RequestGuard $guard) {}

// In action:
$this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
$this->guard->authorize(Client::WEB, Client::IOS);
$userId = $this->guard->validate_authenticated_user_id();
```

### Standard JSON Envelope

```json
// Single item
{ "data": { ... } }

// Collection
{ "data": [ ... ], "meta": { "total_count": 100, "filter_count": 25 } }

// Error
{ "errors": [ { "message": "...", "extensions": { "code": "..." } } ] }
```

---

## Frontend Architecture (`janus.com/src/app/`)

Angular 19 **standalone components only** — no NgModules.

### Key Conventions
- **Signals** — `signal()`, `computed()`, `effect()`. No `BehaviorSubject` for new code.
- **Standalone only** — Every component declares its own `imports: []`.
- **`ApiService` for all HTTP** — never inject `HttpClient` directly in feature code.
- **Lazy-load everything** — routes use `loadChildren` / `loadComponent`.
- **Auth state** — `AuthService.user()` (signal), `AuthService.isAuthenticated()` (computed).
- **Control flow** — `@if`, `@for`, `@switch` (not `*ngIf`, `*ngFor`).
- **Inject pattern** — `inject()` function over constructor injection for new components.
- **Filenames** — `kebab-case.ts`, class names `PascalCase`.

---

## Code Style

### PHP
- `declare(strict_types=1);` at the top of **every** file
- `final` classes by default
- Type hints on all properties, parameters, and return types
- No `public` properties — always private with getters/setters

---

## Environment Variables (`.env.example`)

| Variable | Description |
|---|---|
| `DB_ROOT_PASSWORD`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` | MariaDB credentials |
| `REDIS_PASSWORD` | Redis auth |
| `RABBITMQ_USER`, `RABBITMQ_PASSWORD` | RabbitMQ credentials |
| `APP_ENV`, `APP_SECRET` | Symfony config |
| `JWT_SECRET` | HMAC-SHA256 signing key |
| `JWT_ACCESS_TTL` | Access token TTL in seconds (default: 900) |
| `JWT_REFRESH_TTL` | Refresh token TTL (default: 604800) |
| `CORS_ALLOW_ORIGIN` | Allowed frontend origin (`http://janus.com`) |
| `MAILER_DSN` | SMTP connection string |

---

## Adding a New Backend Module

1. Create directory structure under `src/{ModuleName}/`
2. `Domain/Entity/` — pure PHP class with Doctrine attributes
3. `Domain/Repository/` — interface only
4. `Infrastructure/Repository/` — extends `ServiceEntityRepository`
5. `Application/` — Command, Query, Handler, DTO classes
6. `Presentation/Controller/` — thin, calls Application handlers, uses `RequestGuard`
7. Migration: `make shell-backend` → `php bin/console doctrine:migrations:diff && php bin/console doctrine:migrations:migrate`
8. Test: `tests/Feature/{ModuleName}Test.php`

## Adding a New Frontend Feature Page

1. Component: `src/app/features/{module}/pages/{page-name}/{page-name}.ts`
2. Route: add to `src/app/features/{module}/{module}.routes.ts`
3. Service: `src/app/features/{module}/services/{module}.service.ts` using `ApiService`
4. Test: Vitest unit test for the service

---

## Reference Files

| File | Purpose |
|---|---|
| `BUILDING_PLAN.md` | Full step-by-step build plan with `[x]`/`[ ]` checkboxes |
| `examples-backend-routes.md` | Reference: full backend route list |
| `examples-routes.md` | Reference: full frontend route list |
| `examples/` | Reference monorepo — read-only, never modify |

---

## PROJECT MAP — Quick Reference for Claude

> Use this section to navigate the codebase without file searches.

### Infrastructure

| File | Purpose |
|---|---|
| `docker-compose.yml` | 6 services: mariadb:3306, redis:6379, rabbitmq:5672+15672, backend(php-fpm), frontend:4200, nginx:80 |
| `Makefile` | All dev commands (see Common Commands above) |
| `.env.example` | All env var templates |
| `nginx/conf.d/janus.conf` | Two server blocks: `backend.janus.com` → php-fpm:9000, `janus.com` → frontend:4200 |

---

### Backend Modules — Implementation Status

| Module | Status | Key Classes | Endpoints |
|---|---|---|---|
| **Heimdall** | ✅ Complete | `RequestGuard`, `JwtService`, `AuthController`, `ApiScope`, `ApiVersion`, `Client` | `POST /auth/login`, `POST /auth/logout`, `GET /auth/me`, `POST /auth/password/request` |
| **Users** | ✅ Complete | `User` (UUID, soft-delete), `UserRepository`, `UsersController`, `InviteUserHandler` | `GET/POST /users`, `GET/PATCH/DELETE /users/{id}`, `POST /users/invite` |
| **Settings** | ✅ Complete | `Settings` (singleton), `SettingsRepository` (`getOrCreate()`), `SettingsController` | `GET /settings` (auth), `PATCH /settings` (ROLE_ADMIN) |
| **Activity** | ✅ Complete | `Activity`, `ActivityLogger` (auto-captures IP/UA), `ActivityController` | `GET /activity`, `GET /activity/{id}` |
| **Server** | ✅ Complete | `ServerService` (DB/Redis/RabbitMQ health), `ServerController` | `GET /server/ping` (public), `GET /server/info`, `GET /server/health` |
| **Roles** | ✅ Complete | `Role`, `RoleRepository`, `RolesController` | `GET/POST /roles`, `GET/PATCH/DELETE /roles/{id}` |
| **Permissions** | ✅ Complete | `Permission`, `Policy`, `Access`, `PoliciesController`, `PermissionsController`, `AccessController` | `GET/POST /policies`, `GET/PATCH/DELETE /policies/{id}`, `GET/POST /permissions`, `GET/PATCH/DELETE /permissions/{id}`, `GET/POST /access`, `DELETE /access/{id}` |
| **Collections** | ✅ Complete | `CollectionMeta`, `SchemaManagerService` (DDL), `CollectionsController` | `GET/POST /collections`, `GET/PATCH/DELETE /collections/{name}` |
| **Fields** | ✅ Complete | `FieldMeta`, `FieldType` (enum), `FieldsController` | `GET /fields`, `GET /fields/{collection}`, `GET/POST/PATCH/DELETE /fields/{collection}/{field}` |
| **Items** | ✅ Complete | Dynamic DBAL CRUD against any collection | `GET/POST /items/{collection}`, `GET/PATCH/DELETE /items/{collection}/{id}` |
| **Relations** | ✅ Complete | Relation metadata (no FK DDL) | `GET/POST /relations`, `GET/PATCH/DELETE /relations/{collection}/{field}` |
| **Files** | ✅ Complete | `FileStorageService` (local disk), `FilesController`, `FoldersController` | `GET/POST /files`, `GET/PATCH/DELETE /files/{id}`, `GET/POST /folders`, `GET/PATCH/DELETE /folders/{id}` |
| **Assets** | ✅ Complete | `AssetTransformService` (resize/crop/format), `AssetsController` | `GET /assets/{id}?width=&height=&fit=&format=` |
| **Revisions** | ✅ Complete | `Revision`, `RevisionRecorder` (auto delta/version), `RevisionsController` | `GET /revisions`, `GET /revisions/{id}` |
| **Comments** | ✅ Complete | `Comment` (`isOwnedBy()`), `CommentsController` (ownership enforcement) | `GET/POST /comments`, `GET/PATCH/DELETE /comments/{id}` |
| **Presets** | ✅ Complete | `Preset` (bookmark/view prefs, scoped to user), `PresetsController` | `GET/POST /presets`, `GET/PATCH/DELETE /presets/{id}` |
| **Notifications** | ✅ Complete | `Notification` (`markAsRead()`), `NotificationsController` | `GET/POST /notifications`, `GET/PATCH/DELETE /notifications/{id}` |
| **Shares** | ✅ Complete | `Share` (token, password, expiry, maxUses), `ShareTokenService`, `SharesController` | `GET/POST /shares`, `DELETE /shares/{id}`, `POST /shares/auth` (public) |
| **Dashboards** | ✅ Complete | `Dashboard`, `DashboardsController` (cascade-deletes panels) | `GET/POST /dashboards`, `GET/PATCH/DELETE /dashboards/{id}` |
| **Panels** | ✅ Complete | `Panel` (posX/Y/width/height/options), `PanelsController` | `GET/POST /panels`, `GET/PATCH/DELETE /panels/{id}` |
| **Flows** | ✅ Complete | `Flow`, `Operation` (linked-list graph), `FlowRunnerService` (Messenger async), `FlowsController` | `GET/POST /flows`, `GET/PATCH/DELETE /flows/{id}`, `POST /flows/{id}/trigger`, `GET/POST /operations`, `GET/PATCH/DELETE /operations/{id}` |
| **Extensions** | ✅ Complete | `Extension`, `ExtensionType` (enum), `ExtensionsController` | `GET/POST /extensions`, `GET/PATCH/DELETE /extensions/{id}` |
| **Translations** | ✅ Complete | `Translation` (BCP47 language, dot-notation key), `TranslationsController` | `GET/POST /translations` (GET public), `GET/PATCH/DELETE /translations/{id}` |
| **Schema** | ✅ Complete | `SchemaSnapshotService`, `SchemaDiffService`, `SchemaController` | `GET /schema/snapshot`, `POST /schema/diff`, `POST /schema/apply` |
| **Versions** | ✅ Complete | `Version`, `VersionService`, `VersionsController` | `GET/POST /versions`, `GET/PATCH/DELETE /versions/{id}`, `POST /versions/{id}/promote` |
| **Deployments** | ✅ Complete | `Deployment`, `DeploymentProvider`, `TriggerDeploymentHandler` (HttpClient), `DeploymentsController` | `GET/POST /deployments`, `GET/DELETE /deployments/{id}`, `POST /deployments/{id}/run` |
| **Utils** | ✅ Complete | `UtilsController` | `POST /utils/sort/{collection}`, `GET /utils/hash/generate`, `GET /utils/hash/verify`, `POST /utils/cache/clear`, `GET /utils/random/string` |

---

### Backend Key File Paths

```
backend.janus.com/
  src/
    Heimdall/
      Domain/Enum/ApiScope.php          ← LOCAL | PUBLIC | AUTHENTICATED
      Domain/Enum/ApiVersion.php        ← V100, V200
      Domain/Enum/Client.php            ← ANDROID | IOS | WEB | CLI
      Domain/Exception/UnauthorizedException.php
      Domain/Service/RequestGuard.php   ← validate_webservice_request(), authorize(), validate_authenticated_user_id()
      Infrastructure/JWT/JwtService.php ← issueAccessToken(), decode()
      Application/DTO/AuthDto.php
      Presentation/Controller/AuthController.php
    Users/
      Domain/Entity/User.php            ← UUID, email, roles(JSON), soft-delete, touchLastAccess()
      Infrastructure/Repository/UserRepository.php
      Presentation/Controller/UsersController.php
    Settings/
      Domain/Entity/Settings.php        ← Singleton, projectName, defaultLanguage, defaultAppearance
      Infrastructure/Repository/SettingsRepository.php  ← getOrCreate()
      Presentation/Controller/SettingsController.php
    Activity/
      Domain/Entity/Activity.php        ← action, collection, item, userId, ip, userAgent, timestamp
      Infrastructure/Repository/ActivityRepository.php  ← record()
      Presentation/Controller/ActivityController.php
    Server/
      Presentation/Controller/ServerController.php  ← ping, info
    {Other 20 modules}/
      Presentation/Controller/{Module}Controller.php  ← stubs only
  config/
    routes.yaml      ← auto-discovers #[Route] attributes
  Kernel.php
```

---

### Frontend Core Files

```
janus.com/src/app/
  core/
    services/
      auth.service.ts     ← login(), logout(), loadCurrentUser(), user(signal), isAuthenticated(computed)
                             token key: 'janus_access_token', AuthUser{id,email,first_name,last_name,roles}
      api.service.ts      ← get<T>(), post<T>(), patch<T>(), delete<T>()
                             base URL: environment.apiUrl
                             PaginatedResponse<T>{data,meta{total_count,filter_count}}
    guards/
      auth.guard.ts       ← CanActivateFn, checks isAuthenticated(), redirects to /login
    interceptors/
      auth.interceptor.ts ← HttpInterceptorFn, injects Bearer token, navigates to /login on 401
    layout/
      app-shell/          ← Authenticated layout wrapper (sidebar + router-outlet)
      sidebar-nav/        ← Module navigation links
      header/             ← Top bar
      footer/             ← Page footer
    components/
      search/             ← Global search
  shared/
    components/advertisement/
    models/advertisement.model.ts
    services/advertisement.service.ts
```

---

### Frontend Feature Routes

| Feature | Route Prefix | Pages | Routes File |
|---|---|---|---|
| **Auth** | `/login`, `/register`, etc. | sign-in, logout, register, forgot-password, reset-password, accept-invite, tfa-setup | `features/auth/` (no routes file — registered in app.routes.ts) |
| **Users** | `/users` | users-list, user-detail, user-create | `features/users/users.routes.ts` |
| **Content** | `/content` | content-home, content-collection, content-detail, content-preview, content-fallback | `features/content/content.routes.ts` |
| **Files** | `/files` | files-home, file-detail, file-create, folders-home, folder-detail, folder-create | `features/files/files.routes.ts` |
| **Activity** | `/activity` | activity-home, activity-detail | `features/activity/activity.routes.ts` |
| **Insights** | `/insights` | insights-home, insight-detail, insight-panel | `features/insights/insights.routes.ts` |
| **Deployment** | `/deployment` | deployment-home, deployment-provider, deployment-settings, deployment-runs, deployment-run-detail | `features/deployment/deployment.routes.ts` |
| **Visual** | `/visual` | visual-home, visual-url, visual-viewer, invalid-url, no-url | `features/visual/visual.routes.ts` |
| **Settings** | `/settings` | project, appearance, data-model (+create/collection/field), roles (+create/public/detail), policies (+create/detail), presets (+detail), flows (+detail/operation), extensions, marketplace (+account/extension), translations (+detail), system-logs, ai, not-found | `features/settings/settings.routes.ts` |
| **Core Pages** | — | setup (`/setup`), private-not-found, shared | `features/core/pages/` |
| **Home** | `/` | home | `features/home/home.component.ts` |

---

### Frontend Feature Page File Convention

Every page lives at:
```
features/{module}/pages/{page-name}/{page-name}.ts        ← component
features/{module}/pages/{page-name}/{page-name}.html      ← template
features/{module}/pages/{page-name}/{page-name}.css       ← styles
features/{module}/pages/{page-name}/{page-name}.spec.ts   ← test
features/{module}/services/{module}.service.ts            ← HTTP service (uses ApiService)
```

---

### What Is NOT in This Project

- No Directus SDK or client library
- No third-party partner API integrations (Stripe, SendGrid, etc.)
- No external authentication providers (OAuth, SSO) — JWT only
- `examples/` folder is read-only reference — zero imports from it
