# Janus — Portals Module: Implementation Checklist

Each item = one atomic commit on its own branch → merge to `main` manually.
Branch naming: `feature/portals-<NNN>-<slug>`

Mark items with [x] as you complete them.

---

## PHASE 1 — Foundation

### 1.1 — Database Migrations

- [x] **feat/portals-001-migration-portals-table**
  Create `portals` table: `id`, `name`, `base_route`, `status`, `settings_json`, `created_at`, `updated_at`

- [x] **feat/portals-002-migration-layout-templates-table**
  Create `layout_templates` table: `id`, `name`, `positions_json`, `template_markup`, `created_at`, `updated_at`

- [x] **feat/portals-003-migration-pages-table**
  Create `pages` table: `id`, `portal_id`, `parent_id`, `slug`, `full_path`, `title`, `layout_template_id`, `center_component_id`, `custom_css`, `meta_json`, `status`, `sort_order`, `created_at`, `updated_at`

- [x] **feat/portals-004-migration-modules-table**
  Create `modules` table: `id`, `type`, `name`, `config_json`, `portal_id` (nullable), `created_at`, `updated_at`

- [x] **feat/portals-005-migration-module-placements-table**
  Create `module_placements` table: `id`, `page_id`, `position_name`, `module_id`, `sort_order`

- [x] **feat/portals-006-migration-components-table**
  Create `components` table: `id`, `type`, `collection_id`, `query_config_json`, `render_config_json`, `created_at`, `updated_at`

- [x] **feat/portals-007-migration-acl-rules-table**
  Create `acl_rules` table: `id`, `subject_type`, `subject_id`, `role_id`, `permission`

- [x] **feat/portals-008-migration-magnets-table**
  Create `magnets` table: `id`, `portal_id`, `name`, `source_type`, `source_config_json`, `target_collection_id`, `schedule`, `status`, `created_at`, `updated_at`

- [x] **feat/portals-009-migration-magnet-runs-table**
  Create `magnet_runs` table: `id`, `magnet_id`, `started_at`, `finished_at`, `items_imported`, `errors_json`

---

### 1.2 — Backend: Portal Domain

- [x] **feat/portals-010-portal-value-objects**
  Add `PortalId`, `Route`, `PortalStatus`, `PortalSettings` value objects

- [x] **feat/portals-011-portal-aggregate**
  Add `Portal` aggregate root with `create()`, `updateSettings()`, `archive()` methods + domain events

- [x] **feat/portals-012-portal-repository-interface**
  Add `PortalRepositoryInterface` in Domain layer

- [x] **feat/portals-013-portal-doctrine-repository**
  Add Doctrine implementation of `PortalRepositoryInterface`

- [x] **feat/portals-014-create-portal-command**
  Add `CreatePortalCommand` + `CreatePortalCommandHandler`

- [x] **feat/portals-015-update-portal-settings-command**
  Add `UpdatePortalSettingsCommand` + handler

- [x] **feat/portals-016-archive-portal-command**
  Add `ArchivePortalCommand` + handler

- [x] **feat/portals-017-list-portals-query**
  Add `ListPortalsQuery` + handler + `PortalListDto`

- [x] **feat/portals-018-get-portal-by-id-query**
  Add `GetPortalByIdQuery` + handler + `PortalDetailDto`

- [x] **feat/portals-019-portal-api-controller**
  Add `PortalController`: `GET /api/portals`, `POST /api/portals`, `GET /api/portals/{id}`, `PATCH /api/portals/{id}`, `DELETE /api/portals/{id}`

- [x] **feat/portals-020-portal-api-tests**
  Add functional tests for all Portal API endpoints

---

### 1.3 — Backend: Layout Template Domain

- [x] **feat/portals-021-layout-template-aggregate**
  Add `LayoutTemplate` aggregate root, `Position` value object

- [x] **feat/portals-022-layout-template-repository**
  Add repository interface + Doctrine implementation

- [x] **feat/portals-023-layout-template-commands**
  Add `CreateLayoutTemplateCommand`, `UpdateLayoutTemplateCommand` + handlers

- [x] **feat/portals-024-layout-template-queries**
  Add `ListLayoutTemplatesQuery`, `GetLayoutTemplateByIdQuery` + handlers + DTOs

- [x] **feat/portals-025-layout-template-api-controller**
  Add `LayoutTemplateController`: full CRUD endpoints

- [x] **feat/portals-026-layout-template-api-tests**
  Add functional tests for Layout Template API

---

### 1.4 — Backend: Page Domain (CRUD only, no layout yet)

- [x] **feat/portals-027-page-value-objects**
  Add `PageId`, `Slug`, `PageStatus`, `PageMeta` value objects

- [x] **feat/portals-028-page-entity**
  Add `Page` entity with tree structure support (`parentId`, `fullPath` computation)

- [x] **feat/portals-029-page-repository**
  Add `PageRepositoryInterface` + Doctrine implementation with tree queries

- [x] **feat/portals-030-create-page-command**
  Add `CreatePageCommand` + handler (computes `full_path` on create)

- [x] **feat/portals-031-move-page-command**
  Add `MovePageCommand` + handler (recomputes `full_path` for subtree)

- [x] **feat/portals-032-publish-unpublish-page-commands**
  Add `PublishPageCommand`, `UnpublishPageCommand` + handlers

- [x] **feat/portals-033-get-page-tree-query**
  Add `GetPageTreeQuery` + handler returning nested tree DTO

- [x] **feat/portals-034-page-api-controller**
  Add `PageController`: `GET /api/portals/{portalId}/pages`, `POST`, `GET /api/pages/{id}`, `PATCH`, `DELETE`, `POST /api/pages/{id}/move`

- [x] **feat/portals-035-page-api-tests**
  Add functional tests for Page API (CRUD + move + tree)

---

### 1.5 — Frontend: Portal Slice

- [x] **feat/portals-036-fe-portal-list**
  Frontend `portals/portal-list/`: list view, table/cards, status badges, create button

- [x] **feat/portals-037-fe-portal-create**
  Frontend `portals/portal-create/`: create form (name, base route, status)

- [x] **feat/portals-038-fe-portal-settings**
  Frontend `portals/portal-settings/`: edit form (branding, timezone, locale, base route)

---

### 1.6 — Frontend: Layout Template Slice

- [x] **feat/portals-039-fe-layout-template-list**
  Frontend layout template list + create/edit form with position slot manager

---

### 1.7 — Frontend: Page Tree Slice

- [x] **feat/portals-040-fe-page-tree**
  Frontend `pages/page-tree/`: nested tree with drag-and-drop reorder/reparent, status badges, full path display, context menu (add child, delete, publish)

---

## PHASE 2 — Page Layout Engine

### 2.1 — Backend: Module Domain

- [x] **feat/portals-041-module-aggregate**
  Add `Module` aggregate root, `ModuleType` enum, `ModuleConfig` value object with JSON Schema validation

- [x] **feat/portals-042-module-repository**
  Add `ModuleRepositoryInterface` + Doctrine implementation

- [x] **feat/portals-043-module-commands**
  Add `CreateModuleCommand`, `UpdateModuleConfigCommand`, `DeleteModuleCommand` + handlers

- [x] **feat/portals-044-module-queries**
  Add `ListModulesQuery`, `GetModuleByIdQuery` + handlers + DTOs

- [x] **feat/portals-045-module-api-controller**
  Add `ModuleController`: full CRUD `GET/POST/PATCH/DELETE /api/modules`

- [x] **feat/portals-046-module-api-tests**
  Add functional tests for Module API

---

### 2.2 — Backend: Module Placement Domain

- [x] **feat/portals-047-module-placement-entity**
  Add `ModulePlacement` entity

- [x] **feat/portals-048-place-module-command**
  Add `PlaceModuleCommand` + handler

- [x] **feat/portals-049-remove-module-command**
  Add `RemoveModuleCommand` + handler

- [x] **feat/portals-050-reorder-modules-command**
  Add `ReorderModulesCommand` + handler (bulk sort_order update)

- [x] **feat/portals-051-placement-api-controller**
  Add placement endpoints: `POST/PATCH/DELETE /api/pages/{pageId}/placements`, `POST /api/pages/{pageId}/placements/reorder`

- [x] **feat/portals-052-placement-api-tests**
  Add functional tests for placement endpoints

---

### 2.3 — Backend: Component Domain

- [x] **feat/portals-053-component-aggregate**
  Add `ComponentDefinition` aggregate root, `ComponentType` enum, `QueryConfig`, `RenderConfig` value objects

- [x] **feat/portals-054-component-repository**
  Add `ComponentRepositoryInterface` + Doctrine implementation

- [x] **feat/portals-055-component-commands**
  Add `CreateComponentCommand`, `UpdateComponentCommand`, `DeleteComponentCommand` + handlers

- [x] **feat/portals-056-component-queries**
  Add `ListComponentsQuery`, `GetComponentByIdQuery` + handlers + DTOs

- [x] **feat/portals-057-assign-center-component-command**
  Add `AssignCenterComponentCommand` + handler on Page aggregate

- [x] **feat/portals-058-get-page-with-layout-query**
  Add `GetPageWithLayoutQuery` + handler: resolves full layout (template positions + placements + modules + center component)

- [x] **feat/portals-059-component-api-controller**
  Add `ComponentController` + `GET /api/pages/{id}/layout` endpoint

- [x] **feat/portals-060-component-api-tests**
  Add functional tests for Component API + layout resolution

---

### 2.4 — Frontend: Layout Canvas

- [x] **feat/portals-061-fe-layout-canvas**
  Frontend `pages/page-editor/layout-canvas/`: visual position grid matching template layout, module cards per slot with drag handles, reorder within position

- [x] **feat/portals-062-fe-module-library-picker**
  Module library picker modal (search, filter by type, select to place in position)

- [x] **feat/portals-063-fe-module-config-editor**
  `modules/module-config-editor/`: dynamic form rendered from JSON Schema per module type

- [x] **feat/portals-064-fe-center-component-picker**
  `pages/page-editor/center-component-picker/`: select or create component for page center

- [x] **feat/portals-065-fe-component-collection-wiring**
  `components/component-collection-wiring/`: collection picker + query config (filters, sort, pagination)

- [x] **feat/portals-066-fe-module-library**
  `modules/module-library/`: standalone module management (list + create + edit)

---

## PHASE 3 — Styling & ACL

### 3.1 — Backend: CSS & ACL

- [x] **feat/portals-067-set-page-css-command**
  Add `SetPageCustomCssCommand` + handler

- [x] **feat/portals-068-portal-css-field**
  Add `portal_css` field to `portals` table (migration) + `SetPortalCssCommand` + handler

- [x] **feat/portals-069-acl-rule-entity**
  Add `AclRule` entity + `AclRepositoryInterface` + Doctrine implementation

- [x] **feat/portals-070-set-page-acl-command**
  Add `SetPageAclCommand` + handler (replace full ACL set for a page)

- [x] **feat/portals-071-acl-enforcement-middleware**
  Add ACL enforcement in API layer (Symfony voter or middleware) for portal/page access checks

- [x] **feat/portals-072-acl-api-endpoint**
  Add `PATCH /api/pages/{id}/acl` + `PATCH /api/portals/{id}/css` + `PATCH /api/pages/{id}/css`

- [x] **feat/portals-073-acl-api-tests**
  Add functional tests for ACL enforcement + CSS endpoints

---

### 3.2 — Frontend: CSS Editor & ACL Editor

- [x] **feat/portals-074-fe-css-editor**
  `pages/page-editor/css-editor/`: CodeMirror CSS panel, scope toggle (page/portal), save action

- [x] **feat/portals-075-fe-acl-editor**
  `pages/page-editor/acl-editor/`: role × permission matrix, inherit-from-parent toggle

- [x] **feat/portals-076-fe-page-preview**
  `pages/page-preview/`: iframe preview panel wired to preview endpoint or mock render

---

## PHASE 4 — Portal Dashboard

### 4.1 — Backend: Dashboard

- [x] **feat/portals-077-portal-dashboard-query**
  Add `GetPortalDashboardMetricsQuery` + handler: counts (total pages, published, draft), active magnets count, last magnet run timestamp, recent activity log

- [x] **feat/portals-078-portal-dashboard-api**
  Add `GET /api/portals/{id}/dashboard` endpoint + DTO

- [x] **feat/portals-079-portal-dashboard-api-tests**
  Add functional tests for dashboard endpoint

---

### 4.2 — Frontend: Portal Dashboard

- [x] **feat/portals-080-fe-portal-dashboard**
  `portals/portal-dashboard/`: KPI cards, recent activity feed, quick-action buttons (New Page, Trigger Magnet, Preview Portal)

---

## PHASE 5 — Magnets

### 5.1 — Backend: Magnet Domain

- [x] **feat/portals-081-magnet-aggregate**
  Add `Magnet` aggregate root, `SourceType` enum, `SourceConfig` value object, `MagnetStatus` enum

- [x] **feat/portals-082-magnet-run-entity**
  Add `MagnetRun` entity

- [x] **feat/portals-083-magnet-repository**
  Add `MagnetRepositoryInterface` + Doctrine implementation

- [x] **feat/portals-084-create-magnet-command**
  Add `CreateMagnetCommand` + handler

- [x] **feat/portals-085-update-magnet-source-command**
  Add `UpdateMagnetSourceCommand` + handler

- [x] **feat/portals-086-pause-magnet-command**
  Add `PauseMagnetCommand` + handler (toggles status)

- [x] **feat/portals-087-trigger-magnet-run-command**
  Add `TriggerMagnetRunCommand` + handler: dispatches async Symfony Messenger message

- [x] **feat/portals-088-magnet-run-message-handler**
  Add Messenger `MagnetRunMessage` + handler: executes import, records `MagnetRun` with results/errors

- [ ] **feat/portals-089-magnet-rss-source-adapter**
  Add RSS source adapter (fetches feed, maps to collection fields)

- [x] **feat/portals-090-magnet-api-source-adapter**
  Add REST API source adapter (configurable endpoint, auth, field mapping)

- [ ] **feat/portals-091-magnet-webhook-source-adapter**
  Add Webhook source adapter (inbound endpoint that triggers a run)

- [ ] **feat/portals-092-magnet-scheduler-integration**
  Wire Symfony Scheduler to dispatch `TriggerMagnetRunCommand` based on `cron` field

- [ ] **feat/portals-093-magnet-queries**
  Add `ListMagnetsQuery`, `GetMagnetRunHistoryQuery` + handlers + DTOs

- [ ] **feat/portals-094-magnet-api-controller**
  Add `MagnetController`: `GET/POST /api/portals/{portalId}/magnets`, `PATCH/DELETE /api/magnets/{id}`, `POST /api/magnets/{id}/trigger`, `GET /api/magnets/{id}/runs`

- [ ] **feat/portals-095-magnet-api-tests**
  Add functional tests for Magnet API + run triggering

---

### 5.2 — Frontend: Magnets Slice

- [ ] **feat/portals-096-fe-magnet-list**
  `magnets/magnet-list/`: list with status badges, last run info, trigger button, pause/resume

- [ ] **feat/portals-097-fe-magnet-create**
  `magnets/magnet-create/`: create form (name, source type selector, target collection)

- [ ] **feat/portals-098-fe-magnet-source-config**
  `magnets/magnet-source-config/`: dynamic config form per source type (RSS, API, Webhook)

- [ ] **feat/portals-099-fe-magnet-schedule-builder**
  Cron expression builder UI embedded in magnet form

- [ ] **feat/portals-100-fe-magnet-run-history**
  `magnets/magnet-run-history/`: table of runs, status, item count, expandable error log

---

## PHASE 6 — Polish & Hardening

- [ ] **feat/portals-101-portal-css-global-scope**
  Ensure portal-level CSS is injected as a global stylesheet with proper scoping strategy documented

- [ ] **feat/portals-102-full-acl-api-enforcement**
  Audit all endpoints for ACL voter coverage; add missing guards

- [ ] **feat/portals-103-page-tree-full-path-integrity**
  Add DB-level integrity check / migration test: ensure `full_path` is always consistent with tree

- [ ] **feat/portals-104-e2e-portal-crud**
  E2E test: create portal → create page tree → publish pages

- [ ] **feat/portals-105-e2e-layout-canvas**
  E2E test: assign layout template → place modules → assign center component → save

- [ ] **feat/portals-106-e2e-magnet-run**
  E2E test: create RSS magnet → trigger run → verify items imported into collection

- [ ] **feat/portals-107-e2e-acl-enforcement**
  E2E test: set page ACL → verify unauthorized role is denied → verify authorized role passes

- [ ] **feat/portals-108-api-documentation**
  Generate/update OpenAPI spec for all Portals module endpoints

- [ ] **feat/portals-109-frontend-documentation**
  Add slice-level README files documenting each frontend vertical

- [ ] **feat/portals-110-performance-page-tree-index**
  Add DB indexes on `pages.portal_id`, `pages.parent_id`, `pages.full_path`, `pages.status`

---

## Summary

| Phase | Branches | Focus |
|---|---|---|
| 1 — Foundation | 001–040 | Migrations, Portal/Page/LayoutTemplate domain + CRUD frontend |
| 2 — Layout Engine | 041–066 | Module/Component/Placement domain + Layout Canvas UI |
| 3 — Styling & ACL | 067–076 | CSS editor, ACL rules, enforcement, preview |
| 4 — Dashboard | 077–080 | Metrics query + dashboard UI |
| 5 — Magnets | 081–100 | Magnet domain, adapters, scheduler, frontend |
| 6 — Hardening | 101–110 | ACL audit, E2E tests, docs, indexes |
| **Total** | **110 branches** | |

---

## Git Workflow

> **RULE — always branch from the last implemented branch, never from `main`.**
> Each new branch must be checked out from the previous feature branch so it includes all prior work.

```bash
# Start a new item — checkout from the LAST implemented branch (NOT main)
git checkout feat/portals-NNN-previous-slug   # ← last completed branch
git checkout -b feat/portals-NNN+1-new-slug

# ... implement ...

git add .
git commit -m "feat(portals): description [#NNN+1]"
git push origin feat/portals-NNN+1-new-slug
```

> **Convention**: each branch has exactly **one commit** before the PR.
> If you need to amend: `git commit --amend` before pushing.
> Squash is not needed since each branch is already atomic.
> **After finishing each item, mark it as `[x]` in this checklist.**
