# BUILD_PLAN_002 — Verification, Coverage & UI Fixes

> Living checklist. Tick items as they are completed.
> Branch: `feature/build-plan-002`

---

## §1 — Backend API Gap Checklist

### Authentication & Identity
| Endpoint | Status | Notes |
|---|---|---|
| `POST /auth/login` | ✅ | TFA-aware; returns `tfa_required` flag |
| `POST /auth/logout` | ✅ | Clears refresh token |
| `GET /auth/me` | ✅ | Returns current user from JWT |
| `POST /auth/password/request` | ✅ | Dispatches email via Messenger |
| `POST /auth/password/reset` | ✅ | Validates token, updates hash |
| `GET /auth/tfa/setup` | ✅ | Returns TOTP secret + provisioning URI |
| `POST /auth/tfa/enable` | ✅ | Verifies OTP, sets totp_enabled=true |
| `POST /auth/tfa/disable` | ✅ | Verifies OTP, sets totp_enabled=false |
| `POST /auth/tfa/verify` | ✅ | Exchanges tfa_pending token for full access token |

### Users
| Endpoint | Status | Notes |
|---|---|---|
| `GET /users` | ✅ | |
| `POST /users` | ✅ | |
| `GET /users/{id}` | ✅ | |
| `PATCH /users/{id}` | ✅ | |
| `DELETE /users/{id}` | ✅ | Soft-delete |
| `POST /users/invite` | ✅ | Dispatches invite email |
| `POST /users/invite/accept` | ✅ | Sets password on invited user |
| `GET /users/me` | 🔜 | Self-service endpoint — deferred |
| `PATCH /users/me` | 🔜 | Self-service endpoint — deferred |

### Settings
| Endpoint | Status | Notes |
|---|---|---|
| `GET /settings` | ✅ | |
| `PATCH /settings` | ✅ | ROLE_ADMIN only |

### Roles & Permissions
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /roles` | ✅ | |
| `GET/PATCH/DELETE /roles/{id}` | ✅ | |
| `GET/POST /policies` | ✅ | |
| `GET/PATCH/DELETE /policies/{id}` | ✅ | |
| `GET/POST /permissions` | ✅ | |
| `GET/PATCH/DELETE /permissions/{id}` | ✅ | |
| `GET/POST /access` | ✅ | |
| `DELETE /access/{id}` | ✅ | |

### Collections & Fields
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /collections` | ✅ | DDL via SchemaManagerService |
| `GET/PATCH/DELETE /collections/{name}` | ✅ | |
| `GET /fields` | ✅ | |
| `GET /fields/{collection}` | ✅ | |
| `GET/POST/PATCH/DELETE /fields/{collection}/{field}` | ✅ | |

### Items (Dynamic DBAL)
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /items/{collection}` | ✅ | |
| `GET/PATCH/DELETE /items/{collection}/{id}` | ✅ | |

### Files & Assets
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /files` | ✅ | Multipart upload only |
| `GET/PATCH/DELETE /files/{id}` | ✅ | |
| `GET/POST /folders` | ✅ | |
| `GET/PATCH/DELETE /folders/{id}` | ✅ | |
| `GET /assets/{id}` | ✅ | Resize/crop/format transforms |
| TUS chunked upload | 🔜 | Deferred — multipart only |

### Activity, Revisions, Relations
| Endpoint | Status | Notes |
|---|---|---|
| `GET /activity`, `GET /activity/{id}` | ✅ | |
| `GET /revisions`, `GET /revisions/{id}` | ✅ | |
| `GET/POST /relations` | ✅ | |
| `GET/PATCH/DELETE /relations/{collection}/{field}` | ✅ | |

### Comments, Presets, Notifications
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /comments`, `GET/PATCH/DELETE /comments/{id}` | ✅ | Ownership enforced |
| `GET/POST /presets`, `GET/PATCH/DELETE /presets/{id}` | ✅ | |
| `GET/POST /notifications`, `GET/PATCH/DELETE /notifications/{id}` | ✅ | |

### Shares
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /shares` | ✅ | |
| `DELETE /shares/{id}` | ✅ | |
| `POST /shares/auth` | ✅ | Public — validates password + expiry |

### Dashboards & Panels
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /dashboards`, `GET/PATCH/DELETE /dashboards/{id}` | ✅ | Cascade-deletes panels |
| `GET/POST /panels`, `GET/PATCH/DELETE /panels/{id}` | ✅ | |

### Flows & Operations
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /flows`, `GET/PATCH/DELETE /flows/{id}` | ✅ | |
| `POST /flows/{id}/trigger` | ✅ | Async via Messenger |
| `GET/POST /operations`, `GET/PATCH/DELETE /operations/{id}` | ✅ | |

### Extensions, Translations, Versions
| Endpoint | Status | Notes |
|---|---|---|
| `GET/POST /extensions`, `GET/PATCH/DELETE /extensions/{id}` | ✅ | |
| `GET/POST /translations`, `GET/PATCH/DELETE /translations/{id}` | ✅ | GET public |
| `GET/POST /versions`, `GET/PATCH/DELETE /versions/{id}` | ✅ | |
| `POST /versions/{id}/promote` | ✅ | |

### Schema & Deployments
| Endpoint | Status | Notes |
|---|---|---|
| `GET /schema/snapshot` | ✅ | |
| `POST /schema/diff`, `POST /schema/apply` | ✅ | |
| `GET/POST /deployments`, `GET/DELETE /deployments/{id}` | ✅ | |
| `POST /deployments/{id}/run` | ✅ | HttpClient trigger |

### Server & Utils
| Endpoint | Status | Notes |
|---|---|---|
| `GET /server/ping` | ✅ | Public |
| `GET /server/info`, `GET /server/health` | ✅ | Authenticated |
| `POST /utils/sort/{collection}` | ✅ | |
| `GET /utils/hash/generate`, `GET /utils/hash/verify` | ✅ | |
| `POST /utils/cache/clear` | ✅ | |
| `GET /utils/random/string` | ✅ | |

### Deferred / Out of Scope
| Feature | Status | Notes |
|---|---|---|
| `SEARCH` HTTP verb | 🔜 | Symfony doesn't route SEARCH by default |
| GraphQL (`POST /graphql`) | ❌ | Not planned |
| TUS chunked upload | 🔜 | Deferred |
| OAuth2 / OpenID / LDAP / SAML | ❌ | JWT-only by design |
| Rate limiting middleware | 🔜 | Not yet implemented |
| MCP endpoints | ❌ | Not planned |
| AI endpoints | ❌ | Not planned |

---

## §2 — Frontend Route Checklist

| Route | Component | Status | Notes |
|---|---|---|---|
| `/login` | `SignInComponent` | ✅ | TFA-aware, navigates to /tfa-verify |
| `/register` | `RegisterComponent` | ✅ | |
| `/logout` | `LogoutComponent` | ✅ | |
| `/forgot-password` | `ForgotPasswordComponent` | ✅ | |
| `/reset-password` | `ResetPasswordComponent` | ✅ | |
| `/accept-invite` | `AcceptInvite` | ✅ | |
| `/tfa-setup` | `TfaSetup` | ✅ | Enable/disable TOTP |
| `/tfa-verify` | `TfaVerify` | ✅ | OTP entry during login |
| `/` | `HomeComponent` | ✅ | |
| `/users` | `UsersListComponent` | ✅ | |
| `/users/:id` | `UserDetailComponent` | ✅ | |
| `/users/+` | `UserCreateComponent` | ✅ | |
| `/content` | `ContentHomeComponent` | ✅ | |
| `/content/:collection` | `ContentCollectionComponent` | ✅ | |
| `/content/:collection/:id` | `ContentDetailComponent` | ✅ | |
| `/content/:collection/:id/preview` | `ContentPreviewComponent` | ✅ | |
| `/files` | `FilesHomeComponent` | ✅ | |
| `/files/:id` | `FileDetailComponent` | ✅ | |
| `/files/+` | `FileCreateComponent` | ✅ | |
| `/files/folders` | `FoldersHomeComponent` | ✅ | |
| `/files/folders/:id` | `FolderDetailComponent` | ✅ | |
| `/files/folders/+` | `FolderCreateComponent` | ✅ | |
| `/activity` | `ActivityHomeComponent` | ✅ | |
| `/activity/:id` | `ActivityDetailComponent` | ✅ | |
| `/insights` | `InsightsHomeComponent` | ✅ | |
| `/insights/:id` | `InsightDetailComponent` | ✅ | |
| `/insights/:id/panels/:panelId` | `InsightPanelComponent` | ✅ | |
| `/deployment` | `DeploymentHomeComponent` | ✅ | |
| `/deployment/:id` | `DeploymentProviderComponent` | ✅ | |
| `/deployment/:id/settings` | `DeploymentSettingsComponent` | ✅ | |
| `/deployment/:id/runs` | `DeploymentRunsComponent` | ✅ | |
| `/deployment/:id/runs/:runId` | `DeploymentRunDetailComponent` | ✅ | |
| `/visual` | `VisualHomeComponent` | ✅ | |
| `/settings` | Settings feature | ✅ | |
| `/settings/project` | `ProjectSettingsComponent` | ✅ | |
| `/settings/appearance` | `AppearanceSettingsComponent` | ✅ | |
| `/settings/data-model` | `DataModelComponent` | ✅ | |
| `/settings/roles` | `RolesListComponent` | ✅ | |
| `/settings/roles/+` | `RoleCreateComponent` | ✅ | |
| `/settings/roles/:id` | `RoleDetailComponent` | ✅ | |
| `/settings/policies` | `PoliciesListComponent` | ✅ | |
| `/settings/flows` | `FlowsListComponent` | ✅ | |
| `/settings/translations` | `TranslationsListComponent` | ✅ | |
| `/setup` | `SetupComponent` | ✅ | |
| `/shared/:id` | Shared page | 🔜 | Stub — needs share-token validation |
| Logo → `/` link | `SidebarNavComponent` | ✅ | Fixed in this plan |
| Password show/hide toggle | All auth pages | ✅ | Fixed in this plan |

---

## §3 — Test Coverage Matrix

| Module | Backend Unit Tests | Feature Tests | Frontend Spec Quality |
|---|---|---|---|
| **Auth (Heimdall)** | `AuthTest.php` (✅ 8 tests), `JwtServiceTest.php` (✅) | `AuthTest.php` (✅) | `sign-in.spec.ts` (stub → `[ ]`), `tfa-verify.spec.ts` (stub → `[ ]`) |
| **Users** | `CreateUserHandlerTest.php` (✅), `InviteUserHandlerTest.php` (✅) | `[ ]` UsersTest needed | `users-list.spec.ts` (stub), `user-create.spec.ts` (stub) |
| **Settings** | `[ ]` | `[ ]` | — |
| **Roles** | `[ ]` | `[ ]` RolesTest | — |
| **Permissions** | `[ ]` | `[ ]` PermissionsTest | — |
| **Collections** | `[ ]` | `[ ]` CollectionsTest | — |
| **Files** | `[ ]` FileStorageService | `[ ]` FilesTest | `files-home.spec.ts` (stub) |
| **Server** | — | `ServerTest.php` (✅ 3 tests) | — |
| **Activity** | — | `[ ]` ActivityTest | — |
| **Auth Pages** | — | — | `tfa-setup.spec.ts` (stub → `[ ]`), `forgot-password.spec.ts` (stub) |
| **Core Layout** | — | — | `sidebar-nav.spec.ts` (stub → `[ ]`) |

---

## §4 — PHP Unit Test Checklist

> Convention: `tests/` folder alongside each class; `{Class}TestCase.php` base (no `@Test` methods) + `{Class}_{method}Test.php` per public method.

### Domain Entity Tests

#### `src/Users/Domain/Entity/tests/`
- [ ] `UserTestCase.php` — setUp: `$this->user = new User('test@example.com')`
- [ ] `User_constructorTest.php` — id non-null, email, status=active, totpEnabled=false, createdAt is DateTimeImmutable, getRoles() includes ROLE_USER
- [ ] `User_settersTest.php` — setEmail/setFirstName/setLastName/setStatus/setRoles return static, mutate; setRoles deduplicates ROLE_USER
- [ ] `User_totpTest.php` — enableTotp sets secret+enabled=true; disableTotp clears; storeTotpSecret sets without enabling
- [ ] `User_inviteTokenTest.php` — setInviteToken(future)=valid; setInviteToken(past)=invalid; clearInviteToken=false
- [ ] `User_softDeleteTest.php` — softDelete() sets getDeletedAt() non-null DateTimeImmutable

#### `src/Settings/Domain/Entity/tests/`
- [ ] `SettingsTestCase.php`
- [ ] `Settings_constructorTest.php` — defaults: projectName='Janus', defaultLanguage='en-US', defaultAppearance='auto', projectUrl=null
- [ ] `Settings_settersTest.php`
- [ ] `Settings_toArrayTest.php`

#### `src/Roles/Domain/Entity/tests/`
- [ ] `RoleTestCase.php`
- [ ] `Role_constructorTest.php` — id non-null, name='editors', enforceTfa=false, adminAccess=false, appAccess=true
- [ ] `Role_settersTest.php`

### Domain Service Tests

#### `src/Heimdall/Domain/Service/tests/`
- [ ] `TotpServiceTestCase.php`
- [ ] `TotpService_generateSecretTest.php` — non-empty, valid base32 `/^[A-Z2-7]+=*$/`, two calls differ
- [ ] `TotpService_buildProvisioningUriTest.php` — starts with `otpauth://totp/`, contains 'Janus', email, `secret=`
- [ ] `TotpService_verifyCodeTest.php` — valid code=true; '000000' against real secret=false

#### `src/Revisions/Domain/Service/tests/`
- [ ] `RevisionRecorderTestCase.php`
- [ ] `RevisionRecorder_recordTest.php` — first version=1 delta=null; second version=2 delta of changed keys only; repository->save() called once

### Application Handler Tests

#### `src/Roles/Application/Command/Handler/tests/`
- [ ] `RolesHandlerTestCase.php`
- [ ] `CreateRoleHandler_handleTest.php`
- [ ] `UpdateRoleHandler_handleTest.php`
- [ ] `DeleteRoleHandler_handleTest.php`
- [ ] `GetAllRolesHandler_handleTest.php`
- [ ] `GetRoleByIdHandler_handleTest.php`

#### `src/Permissions/Application/Command/Handler/tests/`
- [ ] `PermissionsHandlerTestCase.php`
- [ ] `CreatePermissionHandler_handleTest.php`
- [ ] `UpdatePermissionHandler_handleTest.php`
- [ ] `DeletePermissionHandler_handleTest.php`
- [ ] `GetAllPermissionsHandler_handleTest.php`
- [ ] `GetPermissionByIdHandler_handleTest.php`

#### `src/Permissions/Application/Policy/Handler/tests/`
- [ ] `PoliciesHandlerTestCase.php`
- [ ] `CreatePolicyHandler_handleTest.php`
- [ ] `UpdatePolicyHandler_handleTest.php`
- [ ] `DeletePolicyHandler_handleTest.php`
- [ ] `GetAllPoliciesHandler_handleTest.php`
- [ ] `GetPolicyByIdHandler_handleTest.php`

#### `src/Permissions/Application/Access/Handler/tests/`
- [ ] `AccessHandlerTestCase.php`
- [ ] `CreateAccessHandler_handleTest.php`
- [ ] `DeleteAccessHandler_handleTest.php`
- [ ] `GetAllAccessHandler_handleTest.php`

#### `src/Collections/Application/Command/Handler/tests/`
- [ ] `CollectionsHandlerTestCase.php`
- [ ] `CreateCollectionHandler_handleTest.php`
- [ ] `UpdateCollectionHandler_handleTest.php`
- [ ] `DeleteCollectionHandler_handleTest.php`
- [ ] `GetAllCollectionsHandler_handleTest.php`
- [ ] `GetCollectionByNameHandler_handleTest.php`

#### `src/Fields/Application/Command/Handler/tests/`
- [ ] `FieldsHandlerTestCase.php`
- [ ] `CreateFieldHandler_handleTest.php`
- [ ] `UpdateFieldHandler_handleTest.php`
- [ ] `DeleteFieldHandler_handleTest.php`
- [ ] `GetAllFieldsHandler_handleTest.php`
- [ ] `GetFieldsByCollectionHandler_handleTest.php`
- [ ] `GetFieldByCollectionAndNameHandler_handleTest.php`

#### `src/Comments/Application/Command/Handler/tests/`
- [ ] `CommentsHandlerTestCase.php`
- [ ] `CreateCommentHandler_handleTest.php`
- [ ] `UpdateCommentHandler_handleTest.php` — ownership check
- [ ] `DeleteCommentHandler_handleTest.php` — ownership check

#### `src/Dashboards/Application/Command/Handler/tests/`
- [ ] `DashboardsHandlerTestCase.php`
- [ ] `CreateDashboardHandler_handleTest.php`
- [ ] `UpdateDashboardHandler_handleTest.php`
- [ ] `DeleteDashboardHandler_handleTest.php` — cascades panels

#### `src/Panels/Application/Command/Handler/tests/`
- [ ] `PanelsHandlerTestCase.php`
- [ ] `CreatePanelHandler_handleTest.php` — validates dashboard exists
- [ ] `UpdatePanelHandler_handleTest.php`
- [ ] `DeletePanelHandler_handleTest.php`

#### `src/Deployments/Application/Command/Handler/tests/`
- [ ] `DeploymentsHandlerTestCase.php`
- [ ] `CreateDeploymentHandler_handleTest.php`
- [ ] `DeleteDeploymentHandler_handleTest.php`
- [ ] `TriggerDeploymentHandler_handleTest.php` — calls HttpClientInterface

#### `src/Flows/Application/Command/Handler/tests/`
- [ ] `FlowsHandlerTestCase.php`
- [ ] `CreateFlowHandler_handleTest.php`
- [ ] `UpdateFlowHandler_handleTest.php`
- [ ] `DeleteFlowHandler_handleTest.php` — cascades operations
- [ ] `TriggerFlowHandler_handleTest.php` — dispatches MessageBusInterface

#### `src/Flows/Application/Operation/Handler/tests/`
- [ ] `OperationsHandlerTestCase.php`
- [ ] `CreateOperationHandler_handleTest.php` — validates flow exists
- [ ] `UpdateOperationHandler_handleTest.php`
- [ ] `DeleteOperationHandler_handleTest.php`

#### `src/Files/Application/Command/Handler/tests/`
- [ ] `FilesHandlerTestCase.php`
- [ ] `UploadFileHandler_handleTest.php` — calls FileStorageService
- [ ] `UpdateFileHandler_handleTest.php`
- [ ] `DeleteFileHandler_handleTest.php` — calls storage.delete

#### `src/Folders/Application/Command/Handler/tests/`
- [ ] `FoldersHandlerTestCase.php`
- [ ] `CreateFolderHandler_handleTest.php`
- [ ] `UpdateFolderHandler_handleTest.php`
- [ ] `DeleteFolderHandler_handleTest.php`

#### `src/Notifications/Application/Command/Handler/tests/`
- [ ] `NotificationsHandlerTestCase.php`
- [ ] `CreateNotificationHandler_handleTest.php`
- [ ] `MarkAsReadHandler_handleTest.php` — ownership check
- [ ] `DeleteNotificationHandler_handleTest.php` — ownership check

#### `src/Presets/Application/Command/Handler/tests/`
- [ ] `PresetsHandlerTestCase.php`
- [ ] `CreatePresetHandler_handleTest.php`
- [ ] `UpdatePresetHandler_handleTest.php` — ownership
- [ ] `DeletePresetHandler_handleTest.php` — ownership

#### `src/Shares/Application/Command/Handler/tests/`
- [ ] `SharesHandlerTestCase.php`
- [ ] `CreateShareHandler_handleTest.php` — bcrypt password hash
- [ ] `AuthenticateShareHandler_handleTest.php` — validates expiry + password + maxUses
- [ ] `DeleteShareHandler_handleTest.php`

#### `src/Translations/Application/Command/Handler/tests/`
- [ ] `TranslationsHandlerTestCase.php`
- [ ] `CreateTranslationHandler_handleTest.php` — duplicate check → 409
- [ ] `UpdateTranslationHandler_handleTest.php`
- [ ] `DeleteTranslationHandler_handleTest.php`

#### `src/Versions/Application/Command/Handler/tests/`
- [ ] `VersionsHandlerTestCase.php`
- [ ] `SaveVersionHandler_handleTest.php`
- [ ] `UpdateVersionHandler_handleTest.php`
- [ ] `DeleteVersionHandler_handleTest.php`
- [ ] `PromoteVersionHandler_handleTest.php`

#### `src/Revisions/Application/Query/Handler/tests/`
- [ ] `RevisionsHandlerTestCase.php`
- [ ] `GetAllRevisionsHandler_handleTest.php`
- [ ] `GetRevisionByIdHandler_handleTest.php`

#### `src/Schema/Application/Command/Handler/tests/`
- [ ] `SchemaHandlerTestCase.php`
- [ ] `ApplySchemaHandler_handleTest.php`

### Infrastructure Service Tests

#### `src/Files/Infrastructure/Storage/tests/`
- [ ] `FileStorageServiceTestCase.php` — setUp: creates temp dir; tearDown: removes it
- [ ] `FileStorageService_storeTest.php` — file exists on disk after call; returns string filename
- [ ] `FileStorageService_deleteTest.php` — file removed; non-existent does not throw
- [ ] `FileStorageService_unsupportedDriverTest.php` — store(file, 's3') throws RuntimeException

---

## §5 — Backend Feature Tests

All extend `ApiTestCase`. Seed DB via `createUser()`, token via `getToken()`.

| File | Endpoints | Status |
|---|---|---|
| `tests/Feature/RolesTest.php` | `GET/POST/GET:id/PATCH/DELETE /roles` | [ ] |
| `tests/Feature/PermissionsTest.php` | `GET/POST/GET:id/PATCH/DELETE /permissions` + `?policy=` filter | [ ] |
| `tests/Feature/PoliciesTest.php` | `GET/POST/GET:id/PATCH/DELETE /policies` + `/access` | [ ] |
| `tests/Feature/CollectionsTest.php` | `GET/POST/GET:name/PATCH/DELETE /collections` | [ ] |
| `tests/Feature/ServerTest.php` | `GET /server/ping` (no auth), `GET /server/info`, `GET /server/health` | ✅ |
| `tests/Feature/ActivityTest.php` | `GET /activity`, `GET /activity/:id` | [ ] |

---

## §6 — Angular Spec Upgrades

### Auth page specs

| File | Status |
|---|---|
| `sign-in.component.spec.ts` | [ ] Upgrade from stub |
| `register.component.spec.ts` | [ ] Upgrade from stub |
| `forgot-password.component.spec.ts` | [ ] Upgrade from stub |
| `reset-password.component.spec.ts` | [ ] Upgrade from stub |
| `accept-invite.spec.ts` | [ ] Upgrade from stub |
| `tfa-setup.spec.ts` | [ ] Upgrade from stub |
| `tfa-verify.spec.ts` | [ ] Upgrade from stub |

### Feature page specs

| File | Status |
|---|---|
| `users-list.spec.ts` | [ ] Upgrade from stub |
| `user-create.spec.ts` | [ ] Upgrade from stub |
| `files-home.spec.ts` | [ ] Upgrade from stub |

### Core layout spec

| File | Status |
|---|---|
| `sidebar-nav.component.spec.ts` | [ ] Upgrade from stub |

---

## §7 — Deferred Tracker

- [ ] `GET /users/me` + `PATCH /users/me` — self-service user endpoints
- [ ] `SEARCH` HTTP verb on all resources — Symfony routing limitation
- [ ] GraphQL (`POST /graphql`) — not planned
- [ ] TUS chunked upload — multipart only for now
- [ ] OAuth2 / OpenID / LDAP / SAML — JWT-only by design
- [ ] Rate limiting middleware
- [ ] MCP endpoints — not planned
- [ ] AI endpoints — not planned
- [ ] `/users/roles/:role` route — role-filtered user view
- [ ] `/shared/:id` — real share-token validation (currently stub)
