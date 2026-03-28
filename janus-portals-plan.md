# Janus — Portals Module: Project Plan

---

## 1. Vision & Scope

Janus evolves from a Directus-like content manager into a **multi-site content orchestration platform**. The new **Portals** module introduces the ability to manage multiple websites (portals) from a single admin, each with its own routing, page layout, module placement (à la Joomla Gantry), ACL, and component wiring.

---

## 2. Core Concepts & Glossary

| Concept | Description |
|---|---|
| **Portal** | A website/tenant. Has a base route, branding, and its own tree of Pages. |
| **Page** | A URL within a Portal. Has a layout, assigned modules per position, a center component, custom CSS, and ACL rules. |
| **Position** | A named slot in a layout template (e.g. `header`, `sidebar-left`, `footer`). |
| **Module** | A reusable widget placed in a Position (menu, search bar, HTML block, collection listing, etc.). |
| **Component** | The primary content unit rendered in the center area of a Page. |
| **Collection** | Existing Janus data source. Components can be wired to a collection. |
| **Magnet** | An importer/aggregator. Defines an external source and pulls content into a Collection on a schedule or trigger. |
| **ACL Rule** | Access control list entry scoped to a Portal, Page, or Section. |

---

## 3. Domain Model

### 3.1 Backend (Symfony / PSR-12 Onion + CQRS)

```
Domain
├── Portal
│   ├── Portal (Aggregate Root)
│   │   ├── id: PortalId
│   │   ├── name: string
│   │   ├── baseRoute: Route
│   │   ├── status: PortalStatus (active|draft|archived)
│   │   └── settings: PortalSettings (branding, timezone, locale)
│   ├── Page (Entity)
│   │   ├── id: PageId
│   │   ├── portalId: PortalId
│   │   ├── parentId: PageId|null        ← tree structure
│   │   ├── slug: Slug
│   │   ├── fullPath: string             ← computed from tree
│   │   ├── title: string
│   │   ├── layoutTemplateId: LayoutTemplateId
│   │   ├── centerComponent: ComponentRef|null
│   │   ├── customCss: string
│   │   ├── metaData: PageMeta
│   │   └── status: PageStatus
│   ├── ModulePlacement (Entity)
│   │   ├── id: ModulePlacementId
│   │   ├── pageId: PageId
│   │   ├── position: PositionName
│   │   ├── moduleId: ModuleId
│   │   └── order: int
│   └── AclRule (Entity)
│       ├── id: AclRuleId
│       ├── subjectType: portal|page|section
│       ├── subjectId: string
│       ├── roleId: RoleId
│       └── permission: view|edit|manage
│
├── Layout
│   ├── LayoutTemplate (Aggregate Root)
│   │   ├── id: LayoutTemplateId
│   │   ├── name: string
│   │   ├── positions: Position[]        ← named slots
│   │   └── templateMarkup: string       ← HTML skeleton with {position} tokens
│   └── Position (Value Object)
│       ├── name: string
│       └── description: string
│
├── Module
│   ├── Module (Aggregate Root)
│   │   ├── id: ModuleId
│   │   ├── type: ModuleType            ← menu|html|collection|search|custom
│   │   ├── name: string
│   │   └── config: ModuleConfig        ← type-specific settings (JSON schema)
│
├── Component
│   ├── ComponentDefinition (Aggregate Root)
│   │   ├── id: ComponentId
│   │   ├── type: ComponentType         ← content|collection-list|form|redirect
│   │   ├── collectionId: CollectionId|null
│   │   ├── queryParams: QueryConfig    ← filters, sorting, pagination config
│   │   └── renderConfig: RenderConfig
│
└── Magnet
    ├── Magnet (Aggregate Root)
    │   ├── id: MagnetId
    │   ├── name: string
    │   ├── sourceType: SourceType      ← rss|api|scraper|webhook|ftp|database
    │   ├── sourceConfig: SourceConfig  ← credentials, endpoint, mapping
    │   ├── targetCollectionId: CollectionId
    │   ├── schedule: CronExpression|null
    │   └── status: MagnetStatus
    └── MagnetRun (Entity)
        ├── id: MagnetRunId
        ├── magnetId: MagnetId
        ├── startedAt: DateTime
        ├── finishedAt: DateTime|null
        ├── itemsImported: int
        └── errors: string[]
```

### 3.2 CQRS — Commands & Queries

**Portal Commands**
- `CreatePortalCommand`
- `UpdatePortalSettingsCommand`
- `ArchivePortalCommand`

**Page Commands**
- `CreatePageCommand`
- `MovePageCommand` (tree restructure)
- `AssignCenterComponentCommand`
- `PlaceModuleCommand`
- `RemoveModuleCommand`
- `ReorderModulesCommand`
- `SetPageCustomCssCommand`
- `SetPageAclCommand`
- `PublishPageCommand` / `UnpublishPageCommand`

**Module Commands**
- `CreateModuleCommand`
- `UpdateModuleConfigCommand`
- `DeleteModuleCommand`

**Magnet Commands**
- `CreateMagnetCommand`
- `UpdateMagnetSourceCommand`
- `TriggerMagnetRunCommand`
- `PauseMagnetCommand`

**Queries**
- `GetPortalByIdQuery`
- `ListPortalsQuery`
- `GetPageTreeQuery(portalId)`
- `GetPageWithLayoutQuery(pageId)` ← full resolved layout with modules
- `GetPortalDashboardMetricsQuery(portalId)`
- `ListMagnetsQuery(portalId)`
- `GetMagnetRunHistoryQuery(magnetId)`

---

## 4. Frontend Architecture (Vertical DDD Slices)

Each domain feature is a self-contained vertical slice:

```
src/
├── portals/
│   ├── portal-list/
│   ├── portal-create/
│   ├── portal-dashboard/       ← metrics, quick links
│   └── portal-settings/
│
├── pages/
│   ├── page-tree/              ← tree view with drag-and-drop
│   ├── page-editor/
│   │   ├── layout-canvas/      ← visual position grid + module drag-drop
│   │   ├── center-component-picker/
│   │   ├── css-editor/         ← CodeMirror CSS panel
│   │   └── acl-editor/
│   └── page-preview/
│
├── modules/
│   ├── module-library/
│   ├── module-create/
│   └── module-config-editor/   ← dynamic form by module type
│
├── components/
│   ├── component-library/
│   ├── component-create/
│   └── component-collection-wiring/
│
└── magnets/
    ├── magnet-list/
    ├── magnet-create/
    ├── magnet-source-config/   ← dynamic by source type
    └── magnet-run-history/
```

---

## 5. UI/UX Feature Breakdown

### 5.1 Portal Dashboard
- KPIs: total pages, published pages, active magnets, last import run
- Quick actions: New Page, Trigger Magnet, Preview Portal
- Recent activity feed

### 5.2 Page Tree
- Nested tree with drag-and-drop reordering/reparenting
- Status badges (published, draft, archived)
- Inline slug display showing full resolved path
- Right-click context menu: add child, duplicate, delete, view ACL

### 5.3 Page Editor — Layout Canvas (Gantry-style)
- Visual grid showing all positions defined by the selected Layout Template
- Each position cell lists its assigned modules with drag handles for reordering
- "Add Module" button per position opens a module library picker
- Module cards show type icon, name, and quick-config popover
- Dedicated panel for Center Component selection
- Sidebar tabs: Layout | CSS | ACL | Meta | Preview

### 5.4 CSS Editor
- CodeMirror panel with CSS syntax highlighting and autocomplete
- Scope toggle: **Page CSS** (scoped to page) vs **Portal CSS** (global to portal)
- Live preview pane (iframe)

### 5.5 ACL Editor
- Role × Permission matrix per page/section
- Inherits from parent page (override toggle)
- Supports custom roles from the existing Janus role system

### 5.6 Magnets
- Source type selector (RSS, REST API, Scraper, Webhook, FTP, Database)
- Per-type dynamic config form (URL, auth, field mapping, transform rules)
- Target collection picker
- Schedule builder (cron UI)
- Run history table with status, item count, error log

---

## 6. API Endpoints (REST)

```
# Portals
GET    /api/portals
POST   /api/portals
GET    /api/portals/{id}
PATCH  /api/portals/{id}
DELETE /api/portals/{id}
GET    /api/portals/{id}/dashboard

# Pages
GET    /api/portals/{portalId}/pages           ← tree
POST   /api/portals/{portalId}/pages
GET    /api/pages/{id}
PATCH  /api/pages/{id}
DELETE /api/pages/{id}
POST   /api/pages/{id}/move
GET    /api/pages/{id}/layout                  ← resolved layout with modules
PATCH  /api/pages/{id}/css
PATCH  /api/pages/{id}/acl

# Module Placements
POST   /api/pages/{pageId}/placements
PATCH  /api/pages/{pageId}/placements/{id}
DELETE /api/pages/{pageId}/placements/{id}
POST   /api/pages/{pageId}/placements/reorder

# Modules
GET    /api/modules
POST   /api/modules
PATCH  /api/modules/{id}
DELETE /api/modules/{id}

# Layout Templates
GET    /api/layout-templates
POST   /api/layout-templates
PATCH  /api/layout-templates/{id}

# Components
GET    /api/components
POST   /api/components
PATCH  /api/components/{id}
DELETE /api/components/{id}

# Magnets
GET    /api/portals/{portalId}/magnets
POST   /api/portals/{portalId}/magnets
PATCH  /api/magnets/{id}
DELETE /api/magnets/{id}
POST   /api/magnets/{id}/trigger
GET    /api/magnets/{id}/runs
```

---

## 7. Database Schema (key tables)

```sql
portals            (id, name, base_route, status, settings_json, created_at, updated_at)
pages              (id, portal_id, parent_id, slug, full_path, title, layout_template_id,
                    center_component_id, custom_css, meta_json, status, sort_order)
layout_templates   (id, name, positions_json, template_markup)
modules            (id, type, name, config_json, portal_id nullable)
module_placements  (id, page_id, position_name, module_id, sort_order)
components         (id, type, collection_id, query_config_json, render_config_json)
acl_rules          (id, subject_type, subject_id, role_id, permission)
magnets            (id, portal_id, name, source_type, source_config_json,
                    target_collection_id, schedule, status)
magnet_runs        (id, magnet_id, started_at, finished_at, items_imported, errors_json)
```

---

## 8. Phased Implementation Roadmap

### Phase 1 — Foundation (Sprint 1–2)
- [ ] Portal CRUD (backend + frontend slice)
- [ ] Layout Template management
- [ ] Page tree CRUD (no layout editing yet)
- [ ] DB migrations

### Phase 2 — Page Layout Engine (Sprint 3–4)
- [ ] Layout Canvas UI (Gantry-style position grid)
- [ ] Module library + placement CRUD
- [ ] Module config dynamic forms
- [ ] Center Component picker + Collection wiring

### Phase 3 — Styling & ACL (Sprint 5)
- [ ] CSS Editor (page + portal scope)
- [ ] ACL editor with inheritance
- [ ] Page preview iframe

### Phase 4 — Portal Dashboard (Sprint 6)
- [ ] Dashboard metrics query + UI
- [ ] Activity feed
- [ ] Quick actions

### Phase 5 — Magnets (Sprint 7–8)
- [ ] Magnet CRUD
- [ ] Source type config forms (RSS, API, Webhook at minimum)
- [ ] Symfony Messenger / scheduler integration for cron runs
- [ ] Run history + error log UI

### Phase 6 — Polish & Hardening (Sprint 9)
- [ ] Portal-level CSS global scope
- [ ] Full ACL enforcement on API layer
- [ ] E2E tests for critical flows
- [ ] Documentation

---

## 9. Key Technical Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Page tree storage | Adjacency list + computed `full_path` | Simple to query subtrees; path caching avoids recursive joins |
| Module config schema | JSON Schema per module type | Dynamic forms on frontend; validated on backend via JsonSchema component |
| Layout positions | Stored in template as JSON array of named slots | Decouples layout definition from module assignment |
| CSS scoping | Page CSS wrapped in `[data-page-id="X"]` selector | Prevents bleed; no shadow DOM complexity |
| Magnet scheduling | Symfony Scheduler component (6.3+) | Native to the stack; avoids external cron infra |
| Component–Collection binding | Soft reference by CollectionId + QueryConfig | Keeps Component domain decoupled from Collection domain |
| ACL inheritance | Explicit override flag per page | Clear audit trail; no implicit surprises |

---

## 10. Open Questions to Resolve

1. **Multi-tenancy**: Are portals isolated by subdomain, path prefix, or both?
2. **Theme inheritance**: Can a Portal have a base CSS that pages inherit and override?
3. **Module scope**: Are modules global (shared across portals) or portal-scoped?
4. **Component rendering**: Does Janus render the portal pages server-side (Twig/Symfony), or does it serve a headless API consumed by a separate frontend?
5. **Magnet transforms**: Do you need a no-code field mapping UI, or is a JSON/JMES path config sufficient for v1?
6. **Preview**: Should page preview be a live iframe against an actual portal renderer, or a mock sandbox?
