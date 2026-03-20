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
  - [x] `POST /auth/refresh`
  - [x] `POST /auth/logout`
  - [x] `POST /auth/password/request`
  - [x] `POST /auth/password/reset`

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

- [x] `Domain/Entity/Role.php` — id(UUID), name(unique), description, icon, enforceTfa, adminAccess, appAccess, timestamps
- [x] `Domain/Repository/RoleRepositoryInterface.php` — save, delete, findById, findByName, findAll, count
- [x] `Domain/Exception/RoleNotFoundException.php`
- [x] `Domain/Exception/RoleAlreadyExistsException.php`
- [x] `Infrastructure/Repository/RoleRepository.php` — implements interface, hard delete
- [x] `Application/DTO/RoleDto.php` — `fromEntity()`, `toArray()`
- [x] `Application/Query/GetRolesQuery.php` + `GetRolesHandler`
- [x] `Application/Query/GetRoleByIdQuery.php` + `GetRoleByIdHandler`
- [x] `Application/Command/CreateRoleCommand.php` + `CreateRoleHandler`
- [x] `Application/Command/UpdateRoleCommand.php` (UNCHANGED sentinel) + `UpdateRoleHandler` (unique name check on update)
- [x] `Application/Command/DeleteRoleCommand.php` + `DeleteRoleHandler` (hard delete)
- [x] `Presentation/DTO/CreateRoleRequest.php`, `UpdateRoleRequest.php`
- [x] `Presentation/Controller/RolesController.php` — wired through RequestGuard + Application handlers
- [x] Doctrine migration `Version20260320000003`: create `roles` table + add `role_id` FK to `users`
- [x] User entity: added `ManyToOne` relation to Role (`role_id` FK, ON DELETE SET NULL)
- [x] UserDto: exposes `role` (role_id) field
- [x] Implement `GET /roles`, `POST /roles`, `GET /roles/:id`, `PATCH /roles/:id`, `DELETE /roles/:id`

### 3.5 Permissions & Policies

> Full CRUD, access checks

**`src/Policies/` module — Policy entity + Access junction**
- [x] `Domain/Entity/Policy.php` — id(UUID), name(unique), description, icon, enforceTfa, adminAccess, appAccess, ipAccess(JSON)
- [x] `Domain/Entity/Access.php` — junction: role(nullable ManyToOne) + policy(ManyToOne), createdAt
- [x] `Domain/Repository/PolicyRepositoryInterface.php`
- [x] `Domain/Repository/AccessRepositoryInterface.php` — findByRoleAndPolicy for duplicate guard
- [x] `Domain/Exception/PolicyNotFoundException`, `PolicyAlreadyExistsException`, `AccessNotFoundException`, `AccessAlreadyExistsException`
- [x] `Application/DTO/PolicyDto.php`, `AccessDto.php`
- [x] `Application/Query/GetPoliciesQuery` + `Handler`, `GetPolicyByIdQuery` + `Handler`, `GetAccessQuery` + `Handler`
- [x] `Application/Command/CreatePolicyCommand` + `Handler`, `UpdatePolicyCommand` + `Handler`, `DeletePolicyCommand` + `Handler`
- [x] `Application/Command/CreateAccessCommand` + `Handler` (resolves role+policy, duplicate check), `DeleteAccessCommand` + `Handler`
- [x] `Infrastructure/Repository/PolicyRepository.php`, `AccessRepository.php`
- [x] `Presentation/DTO/CreatePolicyRequest.php`, `UpdatePolicyRequest.php`, `CreateAccessRequest.php`
- [x] `Presentation/Controller/PoliciesController.php` — `GET/POST /policies`, `GET/PATCH/DELETE /policies/{id}`
- [x] `Presentation/Controller/AccessController.php` — `GET/POST /access`, `DELETE /access/{id}`

**`src/Permissions/` module — Permission rules within a policy**
- [x] `Domain/Enum/PermissionAction.php` — create|read|update|delete|share|sort
- [x] `Domain/Entity/Permission.php` — id(UUID), policy(ManyToOne), collection(?), action(enum), fields(JSON?), permissionsFilter(JSON?), validation(JSON?), presets(JSON?)
- [x] `Domain/Repository/PermissionRepositoryInterface.php` — findByPolicy for policy-scoped listing
- [x] `Domain/Exception/PermissionNotFoundException`
- [x] `Application/DTO/PermissionDto.php`
- [x] `Application/Query/GetPermissionsQuery` (optional policyId filter) + `Handler`, `GetPermissionByIdQuery` + `Handler`
- [x] `Application/Command/CreatePermissionCommand` + `Handler` (validates action enum, resolves policy), `UpdatePermissionCommand` + `Handler`, `DeletePermissionCommand` + `Handler`
- [x] `Infrastructure/Repository/PermissionRepository.php` — findByPolicy via QueryBuilder
- [x] `Presentation/DTO/CreatePermissionRequest.php`, `UpdatePermissionRequest.php`
- [x] `Presentation/Controller/PermissionsController.php` — `GET/POST /permissions`, `GET/PATCH/DELETE /permissions/{id}`; supports `?policy=<id>` filter
- [x] Doctrine migration `Version20260320000004`: create `policies`, `permissions`, `access` tables

### 3.6 Collections & Fields

> Schema introspection and management

- [x] `Collections/Domain/Entity/CollectionMeta.php`
- [x] `Collections/Domain/Repository/CollectionMetaRepositoryInterface.php`
- [x] `Collections/Domain/Exception/CollectionNotFoundException.php` + `CollectionAlreadyExistsException.php`
- [x] `Collections/Application/DTO/CollectionDto.php`
- [x] `Collections/Application/Query/GetCollectionsQuery.php` + Handler
- [x] `Collections/Application/Query/GetCollectionByNameQuery.php` + Handler
- [x] `Collections/Application/Command/CreateCollectionCommand.php` + Handler
- [x] `Collections/Application/Command/UpdateCollectionCommand.php` + Handler
- [x] `Collections/Application/Command/DeleteCollectionCommand.php` + Handler (cascades field meta + DDL drop)
- [x] `Collections/Infrastructure/Repository/CollectionMetaRepository.php`
- [x] `Collections/Infrastructure/Service/SchemaManagerService.php` — DDL wrapper (protected system tables)
- [x] `Collections/Presentation/DTO/CreateCollectionRequest.php` + `UpdateCollectionRequest.php`
- [x] `Collections/Presentation/Controller/CollectionsController.php` — full CRUD
- [x] `Fields/Domain/Entity/FieldMeta.php`
- [x] `Fields/Domain/Enum/FieldType.php` — string, text, integer, bigInteger, float, decimal, boolean, uuid, dateTime, date, time, json, csv, alias
- [x] `Fields/Domain/Repository/FieldMetaRepositoryInterface.php`
- [x] `Fields/Domain/Exception/FieldNotFoundException.php` + `FieldAlreadyExistsException.php`
- [x] `Fields/Application/DTO/FieldDto.php`
- [x] `Fields/Application/Query/GetFieldsQuery.php` + Handler
- [x] `Fields/Application/Query/GetFieldsByCollectionQuery.php` + Handler
- [x] `Fields/Application/Query/GetFieldByCollectionAndNameQuery.php` + Handler
- [x] `Fields/Application/Command/CreateFieldCommand.php` + Handler (alias = no DDL)
- [x] `Fields/Application/Command/UpdateFieldCommand.php` + Handler
- [x] `Fields/Application/Command/DeleteFieldCommand.php` + Handler (alias = no DDL)
- [x] `Fields/Infrastructure/Repository/FieldMetaRepository.php`
- [x] `Fields/Presentation/DTO/CreateFieldRequest.php` + `UpdateFieldRequest.php`
- [x] `Fields/Presentation/Controller/FieldsController.php` — `GET /fields`, `GET /fields/:collection`, `GET/POST/PATCH/DELETE /fields/:collection/:field`
- [x] Doctrine migration `Version20260320000005`: create `janus_collections` and `janus_fields` tables

### 3.7 Items

> Generic CRUD against any dynamic collection

- [x] `Domain/Service/ItemsService.php` — DBAL-based dynamic CRUD (no Doctrine entities)
- [x] `Domain/Exception/ItemNotFoundException.php`
- [x] `Application/Query/GetItemsQuery.php` + Handler (validates collection exists)
- [x] `Application/Query/GetItemByIdQuery.php` + Handler
- [x] `Application/Command/CreateItemCommand.php` + Handler (generates UUID v7, filters by janus_fields)
- [x] `Application/Command/UpdateItemCommand.php` + Handler
- [x] `Application/Command/DeleteItemCommand.php` + Handler
- [x] `Presentation/Controller/ItemsController.php` — `GET/POST /items/:collection`, `GET/PATCH/DELETE /items/:collection/:id`
- [x] No migration needed — tables created dynamically via SchemaManagerService

### 3.8 Relations

> O2M, M2O, M2M schema management

- [x] `Domain/Entity/Relation.php` — many_collection, many_field, one_collection, one_field, junction_collection
- [x] `Domain/Repository/RelationRepositoryInterface.php` — includes `deleteByCollection()` for cascade
- [x] `Domain/Exception/RelationNotFoundException.php` + `RelationAlreadyExistsException.php`
- [x] `Application/DTO/RelationDto.php`
- [x] `Application/Query/GetRelationsQuery.php` + Handler
- [x] `Application/Query/GetRelationByCollectionAndFieldQuery.php` + Handler
- [x] `Application/Command/CreateRelationCommand.php` + Handler
- [x] `Application/Command/UpdateRelationCommand.php` + Handler (UNCHANGED sentinel)
- [x] `Application/Command/DeleteRelationCommand.php` + Handler
- [x] `Infrastructure/Repository/RelationRepository.php`
- [x] `Presentation/DTO/CreateRelationRequest.php` + `UpdateRelationRequest.php`
- [x] `Presentation/Controller/RelationsController.php` — `GET /relations`, `POST /relations`, `GET/PATCH/DELETE /relations/:collection/:field`
- [x] Doctrine migration `Version20260320000006`: create `janus_relations` table

### 3.9 Files & Folders

> File upload + folder management

- [x] `Domain/Entity/Folder.php` — self-referential parent_id (SET NULL on delete)
- [x] `Domain/Entity/File.php` — storage, filename_disk, filename_download, title, type, filesize, width, height, uploaded_by, folder FK
- [x] `Domain/Repository/FileRepositoryInterface.php` + `FolderRepositoryInterface.php`
- [x] `Domain/Exception/FileNotFoundException.php` + `FolderNotFoundException.php`
- [x] `Application/DTO/FileDto.php` + `FolderDto.php`
- [x] `Application/Query/GetFilesQuery.php` (supports `?folder=` filter) + Handler
- [x] `Application/Query/GetFileByIdQuery.php` + Handler
- [x] `Application/Query/GetFoldersQuery.php` + Handler
- [x] `Application/Query/GetFolderByIdQuery.php` + Handler
- [x] `Application/Command/UploadFileCommand.php` + Handler (image dimensions via getimagesize)
- [x] `Application/Command/UpdateFileCommand.php` + Handler (UNCHANGED sentinel for title, filename_download, folder)
- [x] `Application/Command/DeleteFileCommand.php` + Handler (removes DB record + disk file)
- [x] `Application/Command/CreateFolderCommand.php` + Handler
- [x] `Application/Command/UpdateFolderCommand.php` + Handler (UNCHANGED sentinel for parent)
- [x] `Application/Command/DeleteFolderCommand.php` + Handler
- [x] `Infrastructure/Repository/FileRepository.php` + `FolderRepository.php`
- [x] `Infrastructure/Storage/FileStorageService.php` — local disk storage; S3 placeholder
- [x] `config/services.yaml` — wires `$storagePath` to `%kernel.project_dir%/var/storage`
- [x] `Presentation/Controller/FilesController.php` — multipart POST upload, `GET/PATCH/DELETE /files/:id`
- [x] `Presentation/Controller/FoldersController.php` — `GET/POST /folders`, `GET/PATCH/DELETE /folders/:id`
- [x] Doctrine migration `Version20260320000007`: create `folders` and `files` tables

### 3.10 Assets

> Image transform/resizing endpoint

- [x] `Domain/Service/AssetTransformService.php` (resize, crop, format)
- [x] `Application/Query/GetAssetQuery.php` + Handler
- [x] Implement `GET /assets/:id?width=&height=&fit=&format=`

### 3.11 Activity

> Read-only activity log

- [x] `Domain/Entity/Activity.php` — action, collection, item, userId, ip, userAgent, timestamp
- [x] `Domain/Repository/ActivityRepositoryInterface.php` — findAll/countAll with collection, action, userId filters
- [x] `Domain/Exception/ActivityNotFoundException.php`
- [x] `Domain/Service/ActivityLogger.php` — injectable; auto-captures IP + User-Agent from RequestStack
- [x] `Application/DTO/ActivityDto.php`
- [x] `Application/Query/GetActivityQuery.php` (collection/action/user filters) + Handler
- [x] `Application/Query/GetActivityByIdQuery.php` + Handler
- [x] `Infrastructure/Repository/ActivityRepository.php` — implements interface, QueryBuilder filtering
- [x] `Presentation/Controller/ActivityController.php` — RequestGuard + CQRS, ROLE_ADMIN only
- [x] Doctrine migration `Version20260320000008`: create `activity` table with indexes

### 3.12 Revisions

> Read-only revision history

- [x] `Domain/Entity/Revision.php` — collection, item, data (JSON snapshot), delta (JSON diff), version, activity_id
- [x] `Domain/Repository/RevisionRepositoryInterface.php` — `findLatestForItem()`, `findAll/countAll` with collection+item filters
- [x] `Domain/Exception/RevisionNotFoundException.php`
- [x] `Domain/Service/RevisionRecorder.php` — injectable; auto-increments version, computes delta from previous snapshot
- [x] `Application/DTO/RevisionDto.php`
- [x] `Application/Query/GetRevisionsQuery.php` (collection/item filters) + Handler
- [x] `Application/Query/GetRevisionByIdQuery.php` + Handler
- [x] `Infrastructure/Repository/RevisionRepository.php` — `findLatestForItem()` uses ORDER BY version DESC LIMIT 1
- [x] `Presentation/Controller/RevisionsController.php` — read-only, ROLE_ADMIN; `GET /revisions`, `GET /revisions/:id`
- [x] Doctrine migration `Version20260320000009`: create `revisions` table with composite indexes

### 3.13 Comments

> Threaded item comments

- [x] `Domain/Entity/Comment.php` — collection, item, comment (text), userId; `isOwnedBy()`, `setComment()`
- [x] `Domain/Repository/CommentRepositoryInterface.php`
- [x] `Domain/Exception/CommentNotFoundException.php` + `CommentForbiddenException.php`
- [x] `Application/DTO/CommentDto.php`
- [x] `Application/Query/GetCommentsQuery.php` + `GetCommentByIdQuery.php` + Handlers
- [x] `Application/Command/CreateCommentCommand.php` + Handler
- [x] `Application/Command/UpdateCommentCommand.php` + Handler — ownership check via `isOwnedBy()` or `isAdmin`
- [x] `Application/Command/DeleteCommentCommand.php` + Handler — ownership check via `isOwnedBy()` or `isAdmin`
- [x] `Infrastructure/Repository/CommentRepository.php`
- [x] `Presentation/DTO/CreateCommentRequest.php` + `UpdateCommentRequest.php`
- [x] `Presentation/Controller/CommentsController.php` — full CRUD with auth + ownership
- [x] Doctrine migration `Version20260320000010`: create `comments` table with indexes on `(collection, item)` and `user_id`

### 3.14 Presets

> User bookmarks and view preferences

- [x] `Domain/Entity/Preset.php` — collection, layout, layoutOptions/Query/filter (JSON), search, bookmark, userId (nullable for global presets); `isOwnedBy()`
- [x] `Domain/Repository/PresetRepositoryInterface.php`
- [x] `Domain/Exception/PresetNotFoundException.php` + `PresetForbiddenException.php`
- [x] `Application/DTO/PresetDto.php`
- [x] `Application/Query/GetPresetsQuery.php` + `GetPresetByIdQuery.php` + Handlers
- [x] `Application/Command/CreatePresetCommand.php` + Handler
- [x] `Application/Command/UpdatePresetCommand.php` + Handler — UNCHANGED sentinel; ownership enforced
- [x] `Application/Command/DeletePresetCommand.php` + Handler — ownership enforced
- [x] `Infrastructure/Repository/PresetRepository.php`
- [x] `Presentation/DTO/CreatePresetRequest.php` + `UpdatePresetRequest.php`
- [x] `Presentation/Controller/PresetsController.php` — full CRUD; list scopes to current user unless admin
- [x] Doctrine migration `Version20260320000011`: create `presets` table

### 3.15 Notifications

> User notification system

- [x] `Domain/Entity/Notification.php` — recipientId, subject, message, senderId, collection, item, read (bool); `markAsRead()`, `isOwnedBy()`
- [x] `Domain/Repository/NotificationRepositoryInterface.php`
- [x] `Domain/Exception/NotificationNotFoundException.php` + `NotificationForbiddenException.php`
- [x] `Application/DTO/NotificationDto.php`
- [x] `Application/Query/GetNotificationsQuery.php` + `GetNotificationByIdQuery.php` + Handlers
- [x] `Application/Command/CreateNotificationCommand.php` + Handler
- [x] `Application/Command/MarkNotificationReadCommand.php` + Handler — ownership enforced
- [x] `Application/Command/DeleteNotificationCommand.php` + Handler — ownership enforced
- [x] `Infrastructure/Repository/NotificationRepository.php` — filterable by recipientId + read status
- [x] `Presentation/DTO/CreateNotificationRequest.php`
- [x] `Presentation/Controller/NotificationsController.php` — POST is ROLE_ADMIN only; PATCH marks as read; list scopes to current user
- [x] Doctrine migration `Version20260320000012`: create `notifications` table with indexes on `recipient_id` and `(recipient_id, read)`

### 3.16 Shares

> Shared public links to items

- [x] `Domain/Entity/Share.php` — token (unique), collection, item, userId, name, password (bcrypt), expiresAt, maxUses, timesUsed; `isValid()`, `isExpired()`, `isExhausted()`, `recordUse()`, `isOwnedBy()`
- [x] `Domain/Service/ShareTokenService.php` — generates cryptographically random URL-safe token
- [x] `Domain/Repository/ShareRepositoryInterface.php` — includes `findByToken()`
- [x] `Domain/Exception/ShareNotFoundException.php` + `ShareForbiddenException.php` + `ShareInvalidException.php`
- [x] `Application/DTO/ShareDto.php` — exposes `hasPassword` bool, never exposes raw password hash
- [x] `Application/Query/GetSharesQuery.php` + `GetShareByIdQuery.php` + Handlers
- [x] `Application/Command/CreateShareCommand.php` + Handler — bcrypt-hashes password; generates token via ShareTokenService
- [x] `Application/Command/DeleteShareCommand.php` + Handler — ownership enforced
- [x] `Application/Command/AuthenticateShareCommand.php` + Handler — validates expiry/maxUses, verifies password, increments timesUsed
- [x] `Infrastructure/Repository/ShareRepository.php`
- [x] `Presentation/DTO/CreateShareRequest.php` + `AuthenticateShareRequest.php`
- [x] `Presentation/Controller/SharesController.php` — `POST /shares/auth` is PUBLIC scope; list scopes to current user unless admin
- [x] Doctrine migration `Version20260320000013`: create `shares` table with unique index on `token`

### 3.17 Dashboards & Panels

> Dashboard layouts + panel widgets

- [x] `Dashboards/Domain/Entity/Dashboard.php` — name, icon, note, userId; `isOwnedBy()`
- [x] `Panels/Domain/Entity/Panel.php` — dashboardId, type, name, note, options (JSON), positionX/Y, width, height
- [x] `Dashboards/Domain/Repository/DashboardRepositoryInterface.php`
- [x] `Panels/Domain/Repository/PanelRepositoryInterface.php` — includes `deleteByDashboard()`
- [x] `Dashboards/Domain/Exception/DashboardNotFoundException.php`
- [x] `Panels/Domain/Exception/PanelNotFoundException.php`
- [x] Application DTOs, queries, and commands for both Dashboard and Panel (full CRUD)
- [x] `DeleteDashboardHandler` — cascades: calls `panelRepository->deleteByDashboard()` before removing dashboard
- [x] `CreatePanelHandler` — validates dashboard exists before inserting panel
- [x] Infrastructure repositories for Dashboard and Panel
- [x] `Dashboards/Presentation/Controller/DashboardsController.php` — ROLE_ADMIN for write; list scopes to current user
- [x] `Panels/Presentation/Controller/PanelsController.php` — ROLE_ADMIN for write; supports `?dashboard=` filter
- [x] Doctrine migration `Version20260320000014`: `dashboards` + `panels` (FK with CASCADE DELETE)

### 3.18 Flows & Operations

> Automation/webhook pipeline

- [x] `Domain/Enum/FlowStatus.php` (active|inactive) + `TriggerType.php` (manual|action|schedule|webhook)
- [x] `Domain/Entity/Flow.php` — name, status, trigger, triggerOptions (JSON), userId, description; `isActive()`
- [x] `Domain/Entity/Operation.php` — flowId, name, type, options (JSON), resolve, nextSuccess/nextFailure (linked-list graph), sortOrder
- [x] `Domain/Message/RunFlowMessage.php` — Symfony Messenger message envelope
- [x] `Domain/Service/FlowRunnerService.php` — dispatches `RunFlowMessage` to async bus
- [x] `Infrastructure/Messenger/RunFlowMessageHandler.php` — `#[AsMessageHandler]` consumer; iterates operations by sortOrder
- [x] `Infrastructure/Repository/FlowRepository.php` + `OperationRepository.php` — includes `deleteByFlow()`
- [x] Application DTOs, queries, and commands for Flow and Operation (full CRUD + trigger)
- [x] `DeleteFlowHandler` — cascades: `operationRepository->deleteByFlow()` before deleting flow
- [x] `TriggerFlowHandler` — validates flow is active, dispatches via FlowRunnerService
- [x] `CreateOperationHandler` — validates flow exists before inserting
- [x] `Presentation/Controller/FlowsController.php` — full CRUD + `POST /flows/:id/trigger`; all ROLE_ADMIN
- [x] `Presentation/Controller/OperationsController.php` — full CRUD; supports `?flow=` filter; all ROLE_ADMIN
- [x] `config/packages/messenger.yaml` — `RunFlowMessage` routed to async RabbitMQ transport (3x retry, exp backoff)
- [x] Doctrine migration `Version20260320000015`: `flows` + `operations` (FK CASCADE DELETE, composite index on `(flow_id, sort_order)`)

### 3.19 Extensions

> Extension registry

- [x] `Domain/Enum/ExtensionType.php` — interface|endpoint|hook|operation|display|layout|module|panel
- [x] `Domain/Entity/Extension.php` — name, type, version, enabled, description, meta (JSON); unique on (name, type)
- [x] `Domain/Repository/ExtensionRepositoryInterface.php`
- [x] `Domain/Exception/ExtensionNotFoundException.php`
- [x] `Application/DTO/ExtensionDto.php`
- [x] `Application/Query/GetExtensionsQuery.php` + `GetExtensionByIdQuery.php` + Handlers — filterable by type and enabled
- [x] `Application/Command/RegisterExtensionCommand.php` + Handler
- [x] `Application/Command/UpdateExtensionCommand.php` + Handler — UNCHANGED sentinel; updates enabled, version, meta
- [x] `Application/Command/DeleteExtensionCommand.php` + Handler
- [x] `Infrastructure/Repository/ExtensionRepository.php`
- [x] `Presentation/Controller/ExtensionsController.php` — GET open to all authenticated; POST/PATCH/DELETE ROLE_ADMIN only
- [x] Doctrine migration `Version20260320000016`: `extensions` table with unique index on `(name, type)`

### 3.20 Translations

> i18n key/value store

- [x] `Domain/Entity/Translation.php` — language (BCP 47), key (dot-notation), value (text); unique on (language, key)
- [x] `Domain/Repository/TranslationRepositoryInterface.php` — includes `findByLanguageAndKey()`
- [x] `Domain/Exception/TranslationNotFoundException.php` + `TranslationAlreadyExistsException.php`
- [x] `Application/DTO/TranslationDto.php`
- [x] `Application/Query/GetTranslationsQuery.php` + `GetTranslationByIdQuery.php` + Handlers — filterable by language; key uses prefix LIKE match
- [x] `Application/Command/CreateTranslationCommand.php` + Handler — duplicate check; returns 409 on conflict
- [x] `Application/Command/UpdateTranslationCommand.php` + Handler — only `value` is mutable
- [x] `Application/Command/DeleteTranslationCommand.php` + Handler
- [x] `Infrastructure/Repository/TranslationRepository.php` — ordered by language ASC, key ASC
- [x] `Presentation/Controller/TranslationsController.php` — GET/GET-by-id PUBLIC scope; POST/PATCH/DELETE ROLE_ADMIN only
- [x] Doctrine migration `Version20260320000017`: `translations` table with unique index on `(language, translation_key)`

### 3.21 Schema

> Snapshot, diff, apply

- [x] `Presentation/Controller/SchemaController.php`
- [x] `Domain/Service/SchemaSnapshotService.php`
- [x] `Domain/Service/SchemaDiffService.php`
- [x] `Application/Command/ApplySchemaCommand.php` + Handler
- [x] `Application/Query/GetSchemaSnapshotQuery.php` + Handler
- [x] Implement `GET /schema/snapshot`
- [x] Implement `POST /schema/diff`
- [x] Implement `POST /schema/apply`

### 3.22 Versions

> Content versioning

- [x] `Presentation/Controller/VersionsController.php`
- [x] `Domain/Entity/Version.php`
- [x] `Domain/Service/VersionService.php`
- [x] `Application/Query/GetVersionsQuery.php` + Handler
- [x] `Application/Command/SaveVersionCommand.php` + Handler
- [x] `Application/Command/UpdateVersionCommand.php` + Handler
- [x] `Application/Command/DeleteVersionCommand.php` + Handler
- [x] `Application/Command/PromoteVersionCommand.php` + Handler
- [x] `Application/DTO/VersionDto.php`
- [x] Doctrine migration for `versions` table (`Version20260320000018`)
- [x] Implement `GET /versions`, `POST /versions`, `GET /versions/:id`, `PATCH /versions/:id`, `DELETE /versions/:id`
- [x] Implement `POST /versions/:id/promote`

### 3.23 Deployments

> Deployment provider integrations

- [x] `Presentation/Controller/DeploymentsController.php`
- [x] `Domain/Entity/Deployment.php` (run log record)
- [x] `Domain/Entity/DeploymentProvider.php` (provider config)
- [x] `Domain/Enum/DeploymentProviderType.php` — webhook|netlify|vercel|custom
- [x] `Domain/Enum/DeploymentRunStatus.php` — pending|running|success|failure
- [x] `Application/Command/CreateDeploymentCommand.php` + Handler
- [x] `Application/Command/DeleteDeploymentCommand.php` + Handler
- [x] `Application/Command/TriggerDeploymentCommand.php` + Handler (Symfony HttpClient)
- [x] `Application/Query/GetDeploymentsQuery.php` + Handler
- [x] `Application/DTO/DeploymentProviderDto.php` + `DeploymentDto.php`
- [x] Doctrine migration `Version20260320000019`: `deployment_providers` + `deployments` tables
- [x] Implement `GET /deployments`, `POST /deployments`, `GET /deployments/:id`, `DELETE /deployments/:id`
- [x] Implement `POST /deployments/:id/run`

### 3.24 Utils

> Sort, hash, cache-clear utilities

- [x] `Presentation/Controller/UtilsController.php`
- [x] Implement `POST /utils/sort/:collection`
- [x] Implement `GET /utils/hash/generate`
- [x] Implement `GET /utils/hash/verify`
- [x] Implement `POST /utils/cache/clear`
- [x] Implement `GET /utils/random/string`

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

- [x] Verify `sign-in` calls `POST /auth/login` and stores JWT
- [x] Verify `logout` calls `POST /auth/logout` and clears JWT
- [x] Verify `auth.guard.ts` reads `AuthService.isAuthenticated()` correctly
- [x] Verify `auth.interceptor.ts` injects Bearer token on all API calls
- [x] Verify auto-refresh via `POST /auth/refresh` when access token expires

---

## Phase 5: Frontend — Feature Modules

> **Verification:** All sidebar nav links render their respective page and fetch data from the backend.

Each feature must have a service that uses `ApiService`. Pages must be lazy-loaded.

### 5.1 Users

- [x] `features/users/users.routes.ts`
- [x] `features/users/pages/users-list/`
- [x] `features/users/pages/user-detail/`
- [x] `features/users/pages/user-create/`
- [x] `features/users/services/users.service.ts` — calls `GET /users`, `POST /users`, `PATCH /users/:id`, `DELETE /users/:id`
- [x] Wire `users-list` to `UsersService.getAll()`
- [x] Wire `user-detail` to `UsersService.getById()`
- [x] Wire `user-create` to `UsersService.create()`

### 5.2 Content

- [x] `features/content/content.routes.ts`
- [x] `features/content/pages/content-home/`
- [x] `features/content/pages/content-collection/`
- [x] `features/content/pages/content-detail/`
- [x] `features/content/pages/content-preview/`
- [x] `features/content/services/content.service.ts` — calls `/collections`, `/items/:collection`, `/items/:collection/:id`
- [x] Wire `content-home` to list all collections via `ContentService`
- [x] Wire `content-collection` to list items via `ContentService.getItems()`
- [x] Wire `content-detail` to `ContentService.getItem()` and `ContentService.updateItem()`

### 5.3 Files

- [x] `features/files/files.routes.ts`
- [x] `features/files/pages/files-home/`
- [x] `features/files/pages/file-detail/`
- [x] `features/files/pages/file-create/`
- [x] `features/files/pages/folders-home/`
- [x] `features/files/pages/folder-detail/`
- [x] `features/files/pages/folder-create/`
- [x] `features/files/services/files.service.ts` — calls `GET /files`, `POST /files`, `GET /folders`, `POST /folders`
- [x] Wire file upload in `file-create` to `FilesService.upload()`
- [x] Wire folder tree in `folders-home` to `FilesService.getFolders()`

### 5.4 Activity

- [x] `features/activity/activity.routes.ts`
- [x] `features/activity/pages/activity-home/`
- [x] `features/activity/pages/activity-detail/`
- [x] `features/activity/services/activity.service.ts` — calls `GET /activity`, `GET /activity/:id`
- [x] Wire `activity-home` to `ActivityService.getAll()`
- [x] Wire `activity-detail` to `ActivityService.getById()`

### 5.5 Insights

- [x] `features/insights/insights.routes.ts`
- [x] `features/insights/pages/insights-home/`
- [x] `features/insights/pages/insight-detail/`
- [x] `features/insights/pages/insight-panel/`
- [x] `features/insights/services/insights.service.ts` — calls `GET /dashboards`, `GET /panels`
- [x] Wire `insights-home` to `InsightsService.getDashboards()`
- [x] Wire `insight-detail` to `InsightsService.getDashboard()`
- [x] Wire `insight-panel` to `InsightsService.getPanel()`

### 5.6 Deployment

- [x] `features/deployment/deployment.routes.ts`
- [x] `features/deployment/pages/deployment-home/`
- [x] `features/deployment/pages/deployment-provider/`
- [x] `features/deployment/pages/deployment-runs/`
- [x] `features/deployment/pages/deployment-run-detail/`
- [x] `features/deployment/pages/deployment-settings/`
- [x] `features/deployment/services/deployment.service.ts` — calls `GET /deployments`, `POST /deployments/:id/run`
- [x] Wire `deployment-home` to `DeploymentService.getAll()`
- [x] Wire `deployment-provider` to `DeploymentService.getProvider()`

### 5.7 Visual

- [x] `features/visual/visual.routes.ts`
- [x] `features/visual/pages/visual-home/`
- [x] `features/visual/pages/visual-url/`
- [x] `features/visual/pages/visual-viewer/`
- [x] Wire `visual-viewer` — URL bar + sandboxed iframe renderer (no separate service needed)

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
- [x] `features/settings/services/settings.service.ts` — calls `GET /settings`, `PATCH /settings`
- [x] Wire `project` page to `SettingsService.get()` and `SettingsService.update()`
- [x] Wire `data-model` page to `CollectionsService` for schema management
- [x] Wire `roles` page to `RolesService.getAll()`
- [x] Wire `policies` page to `PoliciesService.getAll()`
- [x] Wire `flows` page to `FlowsService.getAll()`
- [x] Wire `translations` page to `TranslationsService.getAll()`

---

## Phase 6: Testing

> **Verification:** All unit tests pass; E2E login + CRUD test passes.

### Backend (PHPUnit)

- [x] Unit tests for `Heimdall/Domain/Service/RequestGuard.php` (9 tests)
- [x] Unit tests for `Heimdall/Infrastructure/JWT/JwtService.php` (10 tests)
- [x] Unit tests for `Users/Application/Command/Handler/CreateUserHandler.php` (5 tests)
- [x] Unit tests for `Settings/Application/Query/Handler/GetSettingsHandler.php` (2 tests)
- [x] Unit tests for `Settings/Application/Command/Handler/UpdateSettingsHandler.php` (8 tests)
- [x] Integration tests for `Activity/Domain/Service/ActivityLogger.php` (5 tests)
- [x] Feature tests for `POST /auth/login`, `POST /auth/refresh`, `POST /auth/logout`, `GET /auth/me` (9 tests)
- [x] Feature tests for `GET /users`, `POST /users`, `PATCH /users/:id`, `DELETE /users/:id` (12 tests)
- [x] Feature tests for `GET /settings`, `PATCH /settings` (6 tests)

### Frontend (Karma + Jasmine)

- [x] Unit tests for `AuthService` (login, logout, token storage, refresh)
- [x] Unit tests for `ApiService` (GET/POST/PATCH/DELETE request building)
- [x] Unit tests for `auth.guard.ts`
- [x] Unit tests for `auth.interceptor.ts` (token injection, 401 refresh, redirect)
- [x] Unit tests for `UsersService`
- [x] Unit tests for `ActivityService`
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
│       ├── Collections/       ✅ Full CRUD + DDL
│       ├── Fields/            ✅ Full CRUD + DDL
│       ├── Items/             ✅ Dynamic DBAL CRUD
│       ├── Relations/         ✅ Full CRUD (metadata only)
│       ├── Files/             ✅ Upload + folders + local storage
│       ├── Activity/          ✅ Read-only log + ActivityLogger
│       ├── Revisions/         ✅ Read-only + RevisionRecorder
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
