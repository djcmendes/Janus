# Backend Project API Routes

This document lists all the API routes defined in the backend project (`examples/api`). 
The routes are primarily mounted in `examples/api/src/app.ts` and their handlers are defined in `examples/api/src/controllers/`.

## Core System and Web
- `GET /` - Root redirect (if configured)
- `GET /robots.txt` - Serves robots.txt
- `GET /server/ping` - Healthcheck endpoint (`pong`)
- `GET /admin/*` - Serves the frontend application dashboard
- `POST /deployments/webhooks` - Public webhook endpoint
- `/graphql` - GraphQL API endpoint
- `/files/tus` - TUS resumable uploads (if `TUS_ENABLED`)

## REST API Modules
The following base routes correspond to system modules and collections. Most of these modules provide standard RESTful CRUD operations (`GET /`, `GET /:id`, `POST /`, `PATCH /:id`, `DELETE /:id`) along with specific utility endpoints.

- `/auth` - Authentication logic (login, refresh, logout, password reset, SSO)
- `/activity` - Activity logs and tracking
- `/access` - Access control checks
- `/assets` - Asset processing and rendering
- `/collections` - Schema collections management
- `/comments` - Item comments
- `/dashboards` - Dashboard layouts and settings
- `/deployments` - Deployment management
- `/extensions` - System extensions
- `/fields` - Schema fields management
- `/files` - File upload, metadata, and management
- `/flows` - Automation workflows and triggers
- `/folders` - File folder organization
- `/items` - Generic CRUD for user-defined collections (e.g. `/items/:collection`)
- `/mcp` - MCP (if `MCP_ENABLED`)
- `/ai/chat` - AI chat (if `AI_ENABLED`)
- `/ai/files` - AI file processing (if `AI_ENABLED`)
- `/metrics` - System metrics (if `METRICS_ENABLED`)
- `/notifications` - User notifications
- `/operations` - Flow operations and steps
- `/panels` - Dashboard panels
- `/permissions` - Role and user permissions
- `/policies` - System policies
- `/presets` - User view presets and bookmarks
- `/translations` - System translations
- `/relations` - Schema relations management (O2M, M2O, M2M)
- `/revisions` - Content revisions and history
- `/roles` - User roles
- `/schema` - Schema overview, snapshots, and diffs
- `/server` - Server information and health
- `/settings` - Project settings
- `/shares` - Shared links and external access
- `/users` - User management, invitations, and 2FA
- `/utils` - Various utilities (sorting, hashing, hashing, cache clearing, random string generation)
- `/versions` - Content versions

*Note: Custom endpoints may also be dynamically loaded via the extension manager and mounted beside the core routes.*
