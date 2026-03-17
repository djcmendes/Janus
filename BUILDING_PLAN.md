# Janus Platform — Building Plan

**Goal:** Rebuild the Directus (`examples`) feature set as a production-quality platform, using **Symfony** for the backend (`backend.janus.com`) and **Angular** for the frontend (`janus.com`), organized around clean architecture principles.

---

## Reference: What `examples` Contains

The `examples` folder is a local clone of the Directus monorepo. It defines the full feature set we must replicate:

### Frontend Modules (from `examples/app`)
| Module | Routes |
|---|---|
| Auth | `/login`, `/logout`, `/register`, `/setup`, `/reset-password`, `/accept-invite`, `/tfa-setup` |
| Activity | `/activity`, `/activity/:id` |
| Content | `/content`, `/content/:collection`, `/content/:collection/:id` |
| Files | `/files`, `/files/:id`, `/files/folders/:folder` |
| Insights | `/insights`, `/insights/:id`, `/insights/:id/:panelId` |
| Settings | `/settings/project`, `/settings/data-model`, `/settings/roles`, `/settings/policies`, `/settings/flows`, `/settings/extensions`, `/settings/translations` |
| Users | `/users`, `/users/:id` |
| Deployment | `/deployment`, `/deployment/:provider`, `/deployment/:provider/settings` |
| Visual | `/visual`, `/visual/:url` |

### Backend Modules (from `examples/api`)
`/auth`, `/activity`, `/access`, `/assets`, `/collections`, `/comments`, `/dashboards`, `/deployments`, `/extensions`, `/fields`, `/files`, `/flows`, `/folders`, `/items`, `/notifications`, `/operations`, `/panels`, `/permissions`, `/policies`, `/presets`, `/relations`, `/revisions`, `/roles`, `/schema`, `/server`, `/settings`, `/shares`, `/translations`, `/users`, `/utils`, `/versions`

---

## Architecture Principles

- **Backend:** Onion Architecture with CQRS. No business logic in controllers.
- **Frontend:** Angular feature-based structure (`core/`, `shared/`, `features/`). Each feature is a lazy-loaded module.
- **Auth:** JWT-based (access token + refresh token). Symfony manages issue/validation; Angular guards routes.
- **Data Flow:** The Angular app never touches the database. All data goes through the Symfony REST API.

---

## Phase Overview

```
Phase 1 — Infrastructure & Docker
Phase 2 — Backend: Heimdall (Auth Guard)
Phase 3 — Backend: Core API Modules
Phase 4 — Frontend: Auth & Shell
Phase 5 — Frontend: All Modules
Phase 6 — Testing
```

---

## Phase 1: Infrastructure & Docker

**Files to create/update:**

- `docker-compose.yml` (project root `/html/Janus/`) with:
  - `mariadb` — Primary relational database
  - `redis` — Cache & session store
  - `rabbitmq` — Message broker for async tasks (webhooks, flows, emails)
  - `backend` — PHP-FPM container for `backend.janus.com`
  - `frontend` — Node container for `janus.com` (dev server)
  - `nginx` — Reverse proxy for both services
- `.env.example` — All required environment variables
- `Makefile` — `make up`, `make down`, `make reset`, `make migrate`, `make tests`

**Goal:** `make up` starts the full stack. Both http://backend.janus.com and http://janus.com are reachable locally.

---

## Phase 2: Backend — Heimdall Module (Auth Guard)

**Location:** `backend.janus.com/src/Heimdall/`

This is the foundational security module, inspired by the existing `Janus.md` design. All controllers will depend on this module.

### Structure
```
src/
  Heimdall/
    Domain/
      Enum/
        ApiScope.php      (LOCAL, PUBLIC, AUTHENTICATED)
        ApiVersion.php    (constants like V1, V2)
        Client.php        (ANDROID, IOS, WEB)
      Service/
        RequestGuard.php  (validate_webservice_request, authorize, validate_user_id)
    Infrastructure/
      JWT/
        JwtService.php    (issue, validate, refresh tokens)
      Middleware/
        AuthMiddleware.php (Symfony event listener / request listener)
```

### Usage in controllers (as per original design doc):
```php
$this->guard->validate_webservice_request(static::API_VERSION_GET, ApiScope::LOCAL);
$this->guard->authorize(Client::ANDROID, Client::IOS, Client::WEB);
$user_id = $this->guard->validate_authenticated_user_id();
```

**API endpoints this unlocks:**
- `POST /auth/login` — Returns access + refresh JWT
- `POST /auth/refresh` — Rotates tokens
- `POST /auth/logout` — Invalidates refresh token
- `POST /auth/password/request` — Sends reset email
- `POST /auth/password/reset` — Applies new password

---

## Phase 3: Backend — Core API Modules

Each module follows the Onion structure:
```
src/
  {Module}/
    Domain/
      Entity/       (Pure PHP classes, no framework dependencies)
      Repository/   (Interfaces)
      Service/      (Business logic)
    Application/
      Command/      (Write operations via CQRS)
      Query/        (Read operations via CQRS)
      Handler/
    Infrastructure/
      Repository/   (Doctrine implementations)
      Persistence/  (Entities/Migrations)
    Presentation/
      Controller/   (Thin controllers, call Application handlers)
      DTO/          (Request/Response data shapes)
```

### Order of Implementation (by dependency)

| # | Module | Key Endpoints |
|---|---|---|
| 1 | **Server** | `GET /server/ping`, `GET /server/info` |
| 2 | **Settings** | `GET/PATCH /settings` |
| 3 | **Users** | Full CRUD, invite, 2FA |
| 4 | **Roles & Policies** | Full CRUD |
| 5 | **Permissions & Access** | Full CRUD, access checks |
| 6 | **Collections & Fields** | Schema introspection and management |
| 7 | **Items** | Generic CRUD against any dynamic collection |
| 8 | **Relations** | O2M, M2O, M2M schema management |
| 9 | **Files & Folders** | Upload (standard), folder management |
| 10 | **Assets** | Image transform/resizing endpoint |
| 11 | **Activity** | Read-only activity log |
| 12 | **Revisions** | Read-only revision history |
| 13 | **Comments** | Threaded item comments |
| 14 | **Presets** | User bookmarks/view preferences |
| 15 | **Notifications** | User notification system |
| 16 | **Shares** | Shared public links |
| 17 | **Dashboards & Panels** | Dashboard layouts |
| 18 | **Insights** | Analytics dashboards |
| 19 | **Flows & Operations** | Automation/webhook pipeline |
| 20 | **Extensions** | Extension registry |
| 21 | **Translations** | i18n key/value store |
| 22 | **Schema** | Snapshot/diff/apply |
| 23 | **Versions** | Content versioning |
| 24 | **Deployments** | Deployment provider integrations |
| 25 | **Utils** | Sort, hash, cache-clear utilities |

---

## Phase 4: Frontend — Auth & Application Shell

**Location:** `janus.com/src/app/`

### Core Module (`core/`)
- `AuthGuard` — Redirects unauthenticated users to `/login`
- `AuthService` — Manages JWT storage, auto-refresh, and logout
- `ApiService` — Base HTTP client with auth header injection and error interceptor
- `AppShellComponent` — Main layout (sidebar nav + router outlet)
- `NavigationComponent` — Sidebar with links to all modules

### Auth Feature (`features/auth/`)
| Page | Route |
|---|---|
| LoginPage | `/login` |
| LogoutPage | `/logout` |
| RegisterPage | `/register` |
| SetupPage | `/setup` |
| ResetPasswordPage | `/reset-password` |
| AcceptInvitePage | `/accept-invite` |
| TfaSetupPage | `/tfa-setup` |

---

## Phase 5: Frontend — All Feature Modules

Each feature module is **lazy-loaded** and has its own `routes.ts`. Structure per module:

```
features/{module}/
  pages/
    {module}-list/
    {module}-detail/
  components/
  services/
    {module}.service.ts   (HTTP calls to backend)
  {module}.routes.ts
```

### Implementation Order

| # | Feature | Pages |
|---|---|---|
| 1 | **Users** | List, Detail |
| 2 | **Content** | Collection list, Item list, Item detail, Item preview |
| 3 | **Files** | File list, Folder tree, File detail |
| 4 | **Activity** | Activity list, Activity detail |
| 5 | **Insights** | Dashboard list, Dashboard view, Panel view |
| 6 | **Deployment** | Provider list, Provider settings, Runs list, Run detail |
| 7 | **Visual** | Visual viewer |
| 8 | **Settings** | Project, Appearance, Data Model, Roles, Policies, Presets, Flows, Extensions, Marketplace, Translations, System Logs |

---

## Phase 6: Testing

### Backend (PHPUnit)
- **Unit:** Domain Services & Entities (no I/O)
- **Integration:** Repository tests using an in-memory SQLite DB or test MariaDB
- **API/Feature:** Symfony WebTestCase — test full HTTP request/response cycles for each controller

Run: `cd backend.janus.com && php bin/phpunit`

### Frontend (Vitest + Playwright)
- **Unit (Vitest):** Angular services, guards, and pure utility functions
- **E2E (Playwright):** Login flow, CRUD on a collection, file upload

Run unit: `cd janus.com && npm run test`
Run E2E: `cd janus.com && npm run e2e`

---

## Verification Milestones

After each phase, the following should be verifiable:

| Phase | Manual Verification |
|---|---|
| 1 | `make up` → both http://janus.com and http://backend.janus.com respond |
| 2 | `POST /auth/login` with valid credentials returns a JWT pair |
| 3 | All listed `/server/ping`, `/users`, `/collections`, `/items` endpoints return correct responses |
| 4 | Navigating to a protected route redirects to `/login`; login redirects back |
| 5 | All sidebar nav links are reachable and render their respective page |
| 6 | All unit tests pass; E2E login + CRUD test passes |

---

## File Map

```
/html/Janus/
├── BUILDING_PLAN.md           ← This file
├── docker-compose.yml
├── Makefile
├── .env.example
├── backend.janus.com/         ← Symfony (Onion + CQRS)
│   └── src/
│       ├── Heimdall/          ← Auth guard module
│       ├── Server/
│       ├── Settings/
│       ├── Users/
│       ├── ... (one dir per module)
└── janus.com/                 ← Angular
    └── src/app/
        ├── core/
        ├── shared/
        └── features/
            ├── auth/
            ├── users/
            ├── content/
            ├── files/
            ├── activity/
            ├── insights/
            ├── deployment/
            ├── visual/
            └── settings/
```