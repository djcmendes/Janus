# Janus Platform — Building Plan

**Goal:** Rebuild the Directus (`examples/`) feature set as a production-quality platform using **Symfony 7** for the backend (`backend.janus.com`) and **Angular 19** for the frontend (`janus.com`), organized around Onion Architecture + CQRS.

The `examples/` folder is a local clone of the Directus monorepo used as **reference only**. Never copy-paste from it — understand the feature, then implement it in the Symfony/Angular stack.

---

## Legend

- `[x]` — Implemented
- `[ ]` — Pending
- `[~]` — Partially implemented (scaffold or stub only)

---

## Phase 1: Infrastructure & Docker

> **Verification:** `make up` → both `http://janus.com` and `http://backend.janus.com` respond.

- [x] `docker-compose.yml` with MariaDB 11.4, Redis 7, RabbitMQ 3.13, backend, frontend, nginx services
- [x] `.env.example` with all required environment variables
- [x] `Makefile` with `up`, `down`, `reset`, `migrate`, `shell-backend`, `shell-frontend`, `test-backend`, `test-frontend`, `test-e2e`
- [x] `backend.janus.com/Dockerfile` (PHP-FPM)
- [x] `janus.com/Dockerfile.dev` (Node dev server)
- [x] Nginx reverse proxy config for `janus.com` and `backend.janus.com`

---

## Phase 2: Backend — Heimdall Module (Auth Guard)

> **Verification:** `POST /auth/login` with valid credentials returns a JWT pair.

- [x] `Domain/Enum/ApiScope.php` — `LOCAL`, `PUBLIC`, `AUTHENTICATED`
- [x] `Domain/Enum/ApiVersion.php` — Version constants
- [x] `Domain/Enum/Client.php` — `ANDROID`, `IOS`, `WEB`
- [x] `Domain/Exception/UnauthorizedException.php`
- [x] `Domain/Service/RequestGuard.php` — `validate_webservice_request()`, `authorize()`, `validate_authenticated_user_id()`
- [x] `Infrastructure/JWT/JwtService.php` — Issue, validate, refresh tokens
- [x] `Application/DTO/AuthDto.php`
- [x] `Presentation/Controller/AuthController.php`
  - [x] `POST /auth/login`
  - [ ] `POST /auth/refresh`
  - [ ] `POST /auth/logout`
  - [ ] `POST /auth/password/request`
  - [ ] `POST /auth/password/reset`

---

## Phase 3: Backend — Core API Modules

Each module must follow the full Onion structure:
`Domain/Entity` → `Domain/Repository` (interface) → `Domain/Service` → `Application/Command+Query+Handler+DTO` → `Infrastructure/Repository` (Doctrine) → `Presentation/Controller+DTO`

### 3.1 Server

> `GET /server/ping`, `GET /server/info`, `GET /server/health`

- [~] `Presentation/Controller/ServerController.php` — stub exists
- [ ] `Domain/Service/ServerService.php`
- [ ] Implement `GET /server/ping` → `{"data": "pong"}`
- [ ] Implement `GET /server/info` → version, node info
- [ ] Implement `GET /server/health` → service health checks (DB, Redis, RabbitMQ)

### 3.2 Settings

> `GET /settings`, `PATCH /settings`

- [x] `Domain/Entity/Settings.php` — singleton, projectName, defaultLanguage, defaultAppearance, projectUrl/Logo/Color, updatedAt
- [x] `Domain/Repository/SettingsRepositoryInterface.php`
- [x] `Infrastructure/Repository/SettingsRepository.php` — implements interface, `getOrCreate()`, `save()`
- [x] `Application/DTO/SettingsDto.php` — `fromEntity()`, `toArray()`
- [x] `Application/Query/GetSettingsQuery.php` + `GetSettingsHandler`
- [x] `Application/Command/UpdateSettingsCommand.php` (UNCHANGED sentinel for nullable fields) + `UpdateSettingsHandler`
- [x] `Presentation/DTO/UpdateSettingsRequest.php` — validates `default_appearance`, UNCHANGED sentinel passthrough
- [x] `Presentation/Controller/SettingsController.php` — uses RequestGuard + Application handlers
- [x] Doctrine migration for `settings` table (`migrations/Version20260320000002.php`)
- [x] Implement `GET /settings` (authenticated, all clients)
- [x] Implement `PATCH /settings` (ROLE_ADMIN, WEB only)

### 3.3 Users

> Full CRUD + invite + TFA

- [x] `Domain/Entity/User.php` — id(UUID), email, roles, password, status, firstName, lastName, inviteToken, timestamps
- [x] `Domain/Repository/UserRepositoryInterface.php`
- [x] `Domain/Exception/UserNotFoundException.php`
- [x] `Domain/Exception/UserAlreadyExistsException.php`
- [x] `Infrastructure/Repository/UserRepository.php` — implements interface, findAllActive, countActive, findByInviteToken
- [x] `Application/DTO/UserDto.php` — `fromEntity()`, `toArray()`
- [x] `Application/Query/GetUsersQuery.php` + `GetUsersHandler`
- [x] `Application/Query/GetUserByIdQuery.php` + `GetUserByIdHandler`
- [x] `Application/Command/CreateUserCommand.php` + `CreateUserHandler`
- [x] `Application/Command/UpdateUserCommand.php` + `UpdateUserHandler`
- [x] `Application/Command/DeleteUserCommand.php` + `DeleteUserHandler`
- [x] `Application/Command/InviteUserCommand.php` + `InviteUserHandler` (generates 48h token, status=invited)
- [x] `Presentation/DTO/CreateUserRequest.php`
- [x] `Presentation/DTO/UpdateUserRequest.php`
- [x] `Presentation/DTO/InviteUserRequest.php`
- [x] `Presentation/Controller/UsersController.php` — uses RequestGuard + all Application handlers
- [x] Doctrine migration for `users` table (`migrations/Version20260320000001.php`)
- [x] Implement `GET /users`
- [x] Implement `GET /users/:id`
- [x] Implement `POST /users`
- [x] Implement `PATCH /users/:id`
- [x] Implement `DELETE /users/:id`
- [x] Implement `POST /users/invite`
- [ ] TFA setup (future — requires TOTP library)

### 3.4 Roles

> Full CRUD

- [~] `Presentation/Controller/RolesController.php` — stub exists
- [ ] `Domain/Entity/Role.php`
- [ ] `Domain/Repository/RoleRepositoryInterface.php`
- [ ] `Infrastructure/Repository/RoleRepository.php`
- [ ] `Application/Query/GetRolesQuery.php` + Handler
- [ ] `Application/Command/CreateRoleCommand.php` + Handler
- [ ] `Application/Command/UpdateRoleCommand.php` + Handler
- [ ] `Application/Command/DeleteRoleCommand.php` + Handler
- [ ] `Application/DTO/RoleDto.php`
- [ ] Doctrine migration for `roles` table
- [ ] Implement `GET /roles`, `POST /roles`, `GET /roles/:id`, `PATCH /roles/:id`, `DELETE /roles/:id`

### 3.5 Permissions & Policies

> Full CRUD, access checks

- [~] `Presentation/Controller/PermissionsController.php` — stub exists
- [ ] `Domain/Entity/Permission.php`
- [ ] `Domain/Entity/Policy.php`
- [ ] `Domain/Repository/PermissionRepositoryInterface.php`
- [ ] `Infrastructure/Repository/PermissionRepository.php`
- [ ] `Application/Query/GetPermissionsQuery.php` + Handler
- [ ] `Application/Command/CreatePermissionCommand.php` + Handler
- [ ] `Application/Command/UpdatePermissionCommand.php` + Handler
- [ ] `Application/Command/DeletePermissionCommand.php` + Handler
- [ ] `Application/DTO/PermissionDto.php`
- [ ] Doctrine migrations for `permissions`, `policies`, `access` tables
- [ ] Implement `GET /permissions`, `POST /permissions`, `PATCH /permissions/:id`, `DELETE /permissions/:id`
- [ ] Implement `GET /policies`, `POST /policies`, `PATCH /policies/:id`, `DELETE /policies/:id`
- [ ] Implement `GET /access`

### 3.6 Collections & Fields

> Schema introspection and management

- [~] `Presentation/Controller/CollectionsController.php` — stub exists
- [~] `Presentation/Controller/FieldsController.php` — stub exists
- [ ] `Domain/Service/SchemaIntrospectionService.php`
- [ ] `Domain/Service/CollectionManagerService.php`
- [ ] `Application/Query/GetCollectionsQuery.php` + Handler
- [ ] `Application/Query/GetCollectionByIdQuery.php` + Handler
- [ ] `Application/Command/CreateCollectionCommand.php` + Handler
- [ ] `Application/Command/UpdateCollectionCommand.php` + Handler
- [ ] `Application/Command/DeleteCollectionCommand.php` + Handler
- [ ] `Application/DTO/CollectionDto.php`
- [ ] `Application/DTO/FieldDto.php`
- [ ] Implement `GET /collections`, `POST /collections`, `GET /collections/:id`, `PATCH /collections/:id`, `DELETE /collections/:id`
- [ ] Implement `GET /fields`, `POST /fields/:collection`, `GET /fields/:collection/:field`, `PATCH /fields/:collection/:field`, `DELETE /fields/:collection/:field`

### 3.7 Items

> Generic CRUD against any dynamic collection

- [~] `Presentation/Controller/ItemsController.php` — stub exists
- [ ] `Domain/Service/ItemsService.php` (dynamic collection resolver)
- [ ] `Application/Query/GetItemsQuery.php` + Handler
- [ ] `Application/Query/GetItemByIdQuery.php` + Handler
- [ ] `Application/Command/CreateItemCommand.php` + Handler
- [ ] `Application/Command/UpdateItemCommand.php` + Handler
- [ ] `Application/Command/DeleteItemCommand.php` + Handler
- [ ] `Application/DTO/ItemDto.php`
- [ ] Implement `GET /items/:collection`
- [ ] Implement `POST /items/:collection`
- [ ] Implement `GET /items/:collection/:id`
- [ ] Implement `PATCH /items/:collection/:id`
- [ ] Implement `DELETE /items/:collection/:id`

### 3.8 Relations

> O2M, M2O, M2M schema management

- [~] `Presentation/Controller/RelationsController.php` — stub exists
- [ ] `Domain/Entity/Relation.php`
- [ ] `Domain/Service/RelationService.php`
- [ ] `Application/Query/GetRelationsQuery.php` + Handler
- [ ] `Application/Command/CreateRelationCommand.php` + Handler
- [ ] `Application/Command/UpdateRelationCommand.php` + Handler
- [ ] `Application/Command/DeleteRelationCommand.php` + Handler
- [ ] Doctrine migration for `relations` table
- [ ] Implement `GET /relations`, `POST /relations`, `GET /relations/:collection/:field`, `PATCH /relations/:collection/:field`, `DELETE /relations/:collection/:field`

### 3.9 Files & Folders

> File upload + folder management

- [~] `Presentation/Controller/FilesController.php` — stub exists
- [ ] `Domain/Entity/File.php`
- [ ] `Domain/Entity/Folder.php`
- [ ] `Domain/Service/FileStorageService.php` (local disk + S3 strategy)
- [ ] `Application/Command/UploadFileCommand.php` + Handler
- [ ] `Application/Command/UpdateFileCommand.php` + Handler
- [ ] `Application/Command/DeleteFileCommand.php` + Handler
- [ ] `Application/Query/GetFilesQuery.php` + Handler
- [ ] `Application/DTO/FileDto.php`
- [ ] Doctrine migrations for `files`, `folders` tables
- [ ] Implement `GET /files`, `POST /files`, `GET /files/:id`, `PATCH /files/:id`, `DELETE /files/:id`
- [ ] Implement `GET /folders`, `POST /folders`, `GET /folders/:id`, `PATCH /folders/:id`, `DELETE /folders/:id`

### 3.10 Assets

> Image transform/resizing endpoint

- [ ] `Domain/Service/AssetTransformService.php` (resize, crop, format)
- [ ] `Application/Query/GetAssetQuery.php` + Handler
- [ ] Implement `GET /assets/:id?width=&height=&fit=&format=`

### 3.11 Activity

> Read-only activity log

- [~] `Domain/Entity/Activity.php` — exists
- [~] `Infrastructure/Repository/ActivityRepository.php` — exists
- [~] `Presentation/Controller/ActivityController.php` — stub exists
- [ ] `Domain/Repository/ActivityRepositoryInterface.php`
- [ ] `Domain/Service/ActivityLogger.php` (auto-log from event listeners)
- [ ] `Application/Query/GetActivityQuery.php` + Handler
- [ ] `Application/Query/GetActivityByIdQuery.php` + Handler
- [ ] `Application/DTO/ActivityDto.php`
- [ ] Doctrine migration for `activity` table
- [ ] Implement `GET /activity`
- [ ] Implement `GET /activity/:id`

### 3.12 Revisions

> Read-only revision history

- [~] `Presentation/Controller/RevisionsController.php` — stub exists
- [ ] `Domain/Entity/Revision.php`
- [ ] `Domain/Service/RevisionService.php` (capture diffs on item save)
- [ ] `Application/Query/GetRevisionsQuery.php` + Handler
- [ ] `Application/DTO/RevisionDto.php`
- [ ] Doctrine migration for `revisions` table
- [ ] Implement `GET /revisions`, `GET /revisions/:id`

### 3.13 Comments

> Threaded item comments

- [~] `Presentation/Controller/CommentsController.php` — stub exists
- [ ] `Domain/Entity/Comment.php`
- [ ] `Application/Command/CreateCommentCommand.php` + Handler
- [ ] `Application/Command/UpdateCommentCommand.php` + Handler
- [ ] `Application/Command/DeleteCommentCommand.php` + Handler
- [ ] `Application/Query/GetCommentsQuery.php` + Handler
- [ ] `Application/DTO/CommentDto.php`
- [ ] Doctrine migration for `comments` table
- [ ] Implement `GET /comments`, `POST /comments`, `PATCH /comments/:id`, `DELETE /comments/:id`

### 3.14 Presets

> User bookmarks and view preferences

- [~] `Presentation/Controller/PresetsController.php` — stub exists
- [ ] `Domain/Entity/Preset.php`
- [ ] `Application/Command/CreatePresetCommand.php` + Handler
- [ ] `Application/Command/UpdatePresetCommand.php` + Handler
- [ ] `Application/Command/DeletePresetCommand.php` + Handler
- [ ] `Application/Query/GetPresetsQuery.php` + Handler
- [ ] `Application/DTO/PresetDto.php`
- [ ] Doctrine migration for `presets` table
- [ ] Implement `GET /presets`, `POST /presets`, `GET /presets/:id`, `PATCH /presets/:id`, `DELETE /presets/:id`

### 3.15 Notifications

> User notification system

- [~] `Presentation/Controller/NotificationsController.php` — stub exists
- [ ] `Domain/Entity/Notification.php`
- [ ] `Application/Command/CreateNotificationCommand.php` + Handler
- [ ] `Application/Command/MarkNotificationReadCommand.php` + Handler
- [ ] `Application/Command/DeleteNotificationCommand.php` + Handler
- [ ] `Application/Query/GetNotificationsQuery.php` + Handler
- [ ] `Application/DTO/NotificationDto.php`
- [ ] Doctrine migration for `notifications` table
- [ ] Implement `GET /notifications`, `POST /notifications`, `PATCH /notifications/:id`, `DELETE /notifications/:id`

### 3.16 Shares

> Shared public links to items

- [~] `Presentation/Controller/SharesController.php` — stub exists
- [ ] `Domain/Entity/Share.php`
- [ ] `Domain/Service/ShareTokenService.php`
- [ ] `Application/Command/CreateShareCommand.php` + Handler
- [ ] `Application/Command/DeleteShareCommand.php` + Handler
- [ ] `Application/Query/GetSharesQuery.php` + Handler
- [ ] `Application/DTO/ShareDto.php`
- [ ] Doctrine migration for `shares` table
- [ ] Implement `GET /shares`, `POST /shares`, `GET /shares/:id`, `DELETE /shares/:id`
- [ ] Implement `POST /shares/auth` (authenticate with share token)

### 3.17 Dashboards & Panels

> Dashboard layouts + panel widgets

- [~] `Presentation/Controller/DashboardsController.php` — stub exists
- [~] `Presentation/Controller/PanelsController.php` — stub exists
- [ ] `Domain/Entity/Dashboard.php`
- [ ] `Domain/Entity/Panel.php`
- [ ] `Application/Command/CreateDashboardCommand.php` + Handler
- [ ] `Application/Command/UpdateDashboardCommand.php` + Handler
- [ ] `Application/Command/DeleteDashboardCommand.php` + Handler
- [ ] `Application/Query/GetDashboardsQuery.php` + Handler
- [ ] `Application/DTO/DashboardDto.php`
- [ ] `Application/DTO/PanelDto.php`
- [ ] Doctrine migrations for `dashboards`, `panels` tables
- [ ] Implement `GET /dashboards`, `POST /dashboards`, `PATCH /dashboards/:id`, `DELETE /dashboards/:id`
- [ ] Implement `GET /panels`, `POST /panels`, `PATCH /panels/:id`, `DELETE /panels/:id`

### 3.18 Flows & Operations

> Automation/webhook pipeline

- [~] `Presentation/Controller/FlowsController.php` — stub exists
- [ ] `Domain/Entity/Flow.php`
- [ ] `Domain/Entity/Operation.php`
- [ ] `Domain/Service/FlowRunnerService.php` (RabbitMQ consumer)
- [ ] `Application/Command/CreateFlowCommand.php` + Handler
- [ ] `Application/Command/TriggerFlowCommand.php` + Handler
- [ ] `Application/Query/GetFlowsQuery.php` + Handler
- [ ] `Application/DTO/FlowDto.php`
- [ ] `Application/DTO/OperationDto.php`
- [ ] Doctrine migrations for `flows`, `operations` tables
- [ ] Implement `GET /flows`, `POST /flows`, `GET /flows/:id`, `PATCH /flows/:id`, `DELETE /flows/:id`
- [ ] Implement `POST /flows/:id/trigger`
- [ ] Implement `GET /operations`, `POST /operations`, `PATCH /operations/:id`, `DELETE /operations/:id`

### 3.19 Extensions

> Extension registry

- [~] `Presentation/Controller/` — stub exists
- [ ] `Domain/Entity/Extension.php`
- [ ] `Application/Query/GetExtensionsQuery.php` + Handler
- [ ] `Application/DTO/ExtensionDto.php`
- [ ] Implement `GET /extensions`

### 3.20 Translations

> i18n key/value store

- [~] `Presentation/Controller/TranslationsController.php` — stub exists
- [ ] `Domain/Entity/Translation.php`
- [ ] `Application/Command/CreateTranslationCommand.php` + Handler
- [ ] `Application/Command/UpdateTranslationCommand.php` + Handler
- [ ] `Application/Command/DeleteTranslationCommand.php` + Handler
- [ ] `Application/Query/GetTranslationsQuery.php` + Handler
- [ ] `Application/DTO/TranslationDto.php`
- [ ] Doctrine migration for `translations` table
- [ ] Implement `GET /translations`, `POST /translations`, `PATCH /translations/:id`, `DELETE /translations/:id`

### 3.21 Schema

> Snapshot, diff, apply

- [~] `Presentation/Controller/SchemaController.php` — stub exists
- [ ] `Domain/Service/SchemaSnapshotService.php`
- [ ] `Domain/Service/SchemaDiffService.php`
- [ ] `Application/Command/ApplySchemaCommand.php` + Handler
- [ ] `Application/Query/GetSchemaSnapshotQuery.php` + Handler
- [ ] Implement `GET /schema/snapshot`
- [ ] Implement `POST /schema/diff`
- [ ] Implement `POST /schema/apply`

### 3.22 Versions

> Content versioning

- [~] `Presentation/Controller/VersionsController.php` — stub exists
- [ ] `Domain/Entity/Version.php`
- [ ] `Domain/Service/VersionService.php`
- [ ] `Application/Query/GetVersionsQuery.php` + Handler
- [ ] `Application/Command/SaveVersionCommand.php` + Handler
- [ ] `Application/Command/PromoteVersionCommand.php` + Handler
- [ ] `Application/DTO/VersionDto.php`
- [ ] Doctrine migration for `versions` table
- [ ] Implement `GET /versions`, `POST /versions`, `GET /versions/:id`, `PATCH /versions/:id`, `DELETE /versions/:id`
- [ ] Implement `POST /versions/:id/promote`

### 3.23 Deployments

> Deployment provider integrations

- [~] `Presentation/Controller/DeploymentsController.php` — stub exists
- [ ] `Domain/Entity/Deployment.php`
- [ ] `Domain/Entity/DeploymentProvider.php`
- [ ] `Application/Command/CreateDeploymentCommand.php` + Handler
- [ ] `Application/Command/TriggerDeploymentCommand.php` + Handler
- [ ] `Application/Query/GetDeploymentsQuery.php` + Handler
- [ ] `Application/DTO/DeploymentDto.php`
- [ ] Doctrine migrations for `deployments`, `deployment_providers` tables
- [ ] Implement `GET /deployments`, `POST /deployments`, `GET /deployments/:id`, `DELETE /deployments/:id`
- [ ] Implement `POST /deployments/:id/run`

### 3.24 Utils

> Sort, hash, cache-clear utilities

- [~] `Presentation/Controller/UtilsController.php` — stub exists
- [ ] Implement `POST /utils/sort/:collection`
- [ ] Implement `GET /utils/hash/generate`
- [ ] Implement `GET /utils/hash/verify`
- [ ] Implement `POST /utils/cache/clear`
- [ ] Implement `GET /utils/random/string`

---

## Phase 4: Frontend — Auth & Application Shell

> **Verification:** Navigating to a protected route redirects to `/login`; successful login redirects back.

### Core Services & Guards

- [x] `core/services/auth.service.ts` — JWT storage, login/logout, signals-based state
- [x] `core/services/api.service.ts` — Base HTTP client (signals-based)
- [x] `core/guards/auth.guard.ts` — Redirects to `/login` if unauthenticated
- [x] `core/interceptors/auth.interceptor.ts` — Injects Bearer token, handles 401

### Core Layout

- [x] `core/layout/app-shell/` — Authenticated layout wrapper
- [x] `core/layout/sidebar-nav/` — Module navigation
- [x] `core/layout/header/` — Top header bar
- [x] `core/layout/footer/` — Footer

### Auth Feature Pages

- [x] `features/auth/sign-in/` — `/login`
- [x] `features/auth/logout/` — `/logout`
- [x] `features/auth/register/` — `/register`
- [x] `features/core/pages/setup/` — `/setup`
- [x] `features/auth/reset-password/` — `/reset-password`
- [x] `features/auth/accept-invite/` — `/accept-invite`
- [x] `features/auth/tfa-setup/` — `/tfa-setup`
- [x] `features/auth/forgot-password/` — `/forgot-password`

**Note:** Pages are scaffolded. Verify these are wired to `AuthService` and calling real backend endpoints before marking complete.

- [ ] Verify `sign-in` calls `POST /auth/login` and stores JWT
- [ ] Verify `logout` calls `POST /auth/logout` and clears JWT
- [ ] Verify `auth.guard.ts` reads `AuthService.isAuthenticated()` correctly
- [ ] Verify `auth.interceptor.ts` injects Bearer token on all API calls
- [ ] Verify auto-refresh via `POST /auth/refresh` when access token expires

---

## Phase 5: Frontend — Feature Modules

> **Verification:** All sidebar nav links render their respective page and fetch data from the backend.

Each feature must have a service that uses `ApiService`. Pages must be lazy-loaded.

### 5.1 Users

- [x] `features/users/users.routes.ts`
- [x] `features/users/pages/users-list/`
- [x] `features/users/pages/user-detail/`
- [x] `features/users/pages/user-create/`
- [ ] `features/users/services/users.service.ts` — calls `GET /users`, `POST /users`, `PATCH /users/:id`, `DELETE /users/:id`
- [ ] Wire `users-list` to `UsersService.getAll()`
- [ ] Wire `user-detail` to `UsersService.getById()`
- [ ] Wire `user-create` to `UsersService.create()`

### 5.2 Content

- [x] `features/content/content.routes.ts`
- [x] `features/content/pages/content-home/`
- [x] `features/content/pages/content-collection/`
- [x] `features/content/pages/content-detail/`
- [x] `features/content/pages/content-preview/`
- [ ] `features/content/services/content.service.ts` — calls `/collections`, `/items/:collection`, `/items/:collection/:id`
- [ ] Wire `content-home` to list all collections via `ContentService`
- [ ] Wire `content-collection` to list items via `ContentService.getItems()`
- [ ] Wire `content-detail` to `ContentService.getItem()` and `ContentService.updateItem()`

### 5.3 Files

- [x] `features/files/files.routes.ts`
- [x] `features/files/pages/files-home/`
- [x] `features/files/pages/file-detail/`
- [x] `features/files/pages/file-create/`
- [x] `features/files/pages/folders-home/`
- [x] `features/files/pages/folder-detail/`
- [x] `features/files/pages/folder-create/`
- [ ] `features/files/services/files.service.ts` — calls `GET /files`, `POST /files`, `GET /folders`, `POST /folders`
- [ ] Wire file upload in `file-create` to `FilesService.upload()`
- [ ] Wire folder tree in `folders-home` to `FilesService.getFolders()`

### 5.4 Activity

- [x] `features/activity/activity.routes.ts`
- [x] `features/activity/pages/activity-home/`
- [x] `features/activity/pages/activity-detail/`
- [ ] `features/activity/services/activity.service.ts` — calls `GET /activity`, `GET /activity/:id`
- [ ] Wire `activity-home` to `ActivityService.getAll()`
- [ ] Wire `activity-detail` to `ActivityService.getById()`

### 5.5 Insights

- [x] `features/insights/insights.routes.ts`
- [x] `features/insights/pages/insights-home/`
- [x] `features/insights/pages/insight-detail/`
- [x] `features/insights/pages/insight-panel/`
- [ ] `features/insights/services/insights.service.ts` — calls `GET /dashboards`, `GET /panels`
- [ ] Wire `insights-home` to `InsightsService.getDashboards()`
- [ ] Wire `insight-detail` to `InsightsService.getDashboard()`
- [ ] Wire `insight-panel` to `InsightsService.getPanel()`

### 5.6 Deployment

- [x] `features/deployment/deployment.routes.ts`
- [x] `features/deployment/pages/deployment-home/`
- [x] `features/deployment/pages/deployment-provider/`
- [x] `features/deployment/pages/deployment-runs/`
- [x] `features/deployment/pages/deployment-run-detail/`
- [x] `features/deployment/pages/deployment-settings/`
- [ ] `features/deployment/services/deployment.service.ts` — calls `GET /deployments`, `POST /deployments/:id/run`
- [ ] Wire `deployment-home` to `DeploymentService.getAll()`
- [ ] Wire `deployment-provider` to `DeploymentService.getProvider()`

### 5.7 Visual

- [x] `features/visual/visual.routes.ts`
- [x] `features/visual/pages/visual-home/`
- [x] `features/visual/pages/visual-url/`
- [x] `features/visual/pages/visual-viewer/`
- [ ] `features/visual/services/visual.service.ts`
- [ ] Wire `visual-viewer` to render visual editing iframe

### 5.8 Settings

- [x] `features/settings/settings.routes.ts`
- [x] `features/settings/pages/project/`
- [x] `features/settings/pages/appearance/`
- [x] `features/settings/pages/data-model/`
- [x] `features/settings/pages/data-model-collection/`
- [x] `features/settings/pages/data-model-field/`
- [x] `features/settings/pages/roles/`
- [x] `features/settings/pages/role-detail/`
- [x] `features/settings/pages/policies/`
- [x] `features/settings/pages/policy-detail/`
- [x] `features/settings/pages/flows/`
- [x] `features/settings/pages/flow-detail/`
- [x] `features/settings/pages/translations/`
- [x] `features/settings/pages/extensions/`
- [x] `features/settings/pages/presets/`
- [x] `features/settings/pages/system-logs/`
- [x] `features/settings/pages/marketplace/`
- [ ] `features/settings/services/settings.service.ts` — calls `GET /settings`, `PATCH /settings`
- [ ] Wire `project` page to `SettingsService.get()` and `SettingsService.update()`
- [ ] Wire `data-model` page to `CollectionsService` for schema management
- [ ] Wire `roles` page to `RolesService.getAll()`
- [ ] Wire `policies` page to `PoliciesService.getAll()`
- [ ] Wire `flows` page to `FlowsService.getAll()`
- [ ] Wire `translations` page to `TranslationsService.getAll()`

---

## Phase 6: Testing

> **Verification:** All unit tests pass; E2E login + CRUD test passes.

### Backend (PHPUnit)

- [ ] Unit tests for `Heimdall/Domain/Service/RequestGuard.php`
- [ ] Unit tests for `Heimdall/Infrastructure/JWT/JwtService.php`
- [ ] Feature tests for `POST /auth/login`, `POST /auth/refresh`, `POST /auth/logout`
- [ ] Feature tests for `GET /users`, `POST /users`, `PATCH /users/:id`, `DELETE /users/:id`
- [ ] Feature tests for `GET /collections`, `POST /collections`
- [ ] Feature tests for `GET /items/:collection`, `POST /items/:collection`
- [ ] Feature tests for `GET /files`, `POST /files`
- [ ] Feature tests for `GET /settings`, `PATCH /settings`
- [ ] Integration tests for `ActivityLogger` auto-logging

### Frontend (Vitest + Playwright)

- [ ] Unit tests for `AuthService` (login, logout, token refresh)
- [ ] Unit tests for `ApiService` (request building, error handling)
- [ ] Unit tests for `auth.guard.ts`
- [ ] Unit tests for `auth.interceptor.ts`
- [ ] Unit tests for `UsersService`
- [ ] Unit tests for `ContentService`
- [ ] E2E: Login flow (valid credentials → redirect to dashboard)
- [ ] E2E: Create + read + delete a content item
- [ ] E2E: Upload a file

---

## Verification Milestones

| Phase | Verification |
|---|---|
| 1 | `make up` → both `http://janus.com` and `http://backend.janus.com` respond |
| 2 | `POST /auth/login` with valid credentials returns a JWT pair |
| 3 | All API endpoints return correct Directus-envelope JSON responses |
| 4 | Protected route redirects to `/login`; login redirects back |
| 5 | All sidebar nav links load their page and display live data from the API |
| 6 | All unit tests pass; E2E login + CRUD test passes |

---

## File Map

```
/html/Janus/
├── BUILDING_PLAN.md           ← This file
├── docker-compose.yml         ✓
├── Makefile                   ✓
├── .env.example               ✓
├── examples/                  ← Directus reference (READ ONLY, never modify)
├── backend.janus.com/         ← Symfony (Onion + CQRS)
│   └── src/
│       ├── Heimdall/          ✓ Complete
│       ├── Server/            ~ Stub
│       ├── Settings/          ~ Partial
│       ├── Users/             ~ Partial
│       ├── Roles/             ~ Stub
│       ├── Permissions/       ~ Stub
│       ├── Collections/       ~ Stub
│       ├── Fields/            ~ Stub
│       ├── Items/             ~ Stub
│       ├── Relations/         ~ Stub
│       ├── Files/             ~ Stub
│       ├── Activity/          ~ Partial
│       ├── Revisions/         ~ Stub
│       ├── Comments/          ~ Stub
│       ├── Presets/           ~ Stub
│       ├── Notifications/     ~ Stub
│       ├── Shares/            ~ Stub
│       ├── Dashboards/        ~ Stub
│       ├── Panels/            ~ Stub
│       ├── Flows/             ~ Stub
│       ├── Extensions/        ~ Stub
│       ├── Translations/      ~ Stub
│       ├── Schema/            ~ Stub
│       ├── Versions/          ~ Stub
│       ├── Deployments/       ~ Stub
│       └── Utils/             ~ Stub
└── janus.com/                 ← Angular 19
    └── src/app/
        ├── core/              ✓ Complete
        ├── shared/            ✓ Complete
        └── features/
            ├── auth/          ✓ Scaffolded (wiring pending)
            ├── users/         ✓ Scaffolded (wiring pending)
            ├── content/       ✓ Scaffolded (wiring pending)
            ├── files/         ✓ Scaffolded (wiring pending)
            ├── activity/      ✓ Scaffolded (wiring pending)
            ├── insights/      ✓ Scaffolded (wiring pending)
            ├── deployment/    ✓ Scaffolded (wiring pending)
            ├── visual/        ✓ Scaffolded (wiring pending)
            └── settings/      ✓ Scaffolded (wiring pending)
```
