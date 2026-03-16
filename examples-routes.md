# Frontend Project Routes

This document lists all the structural routes defined in the frontend project (`app/src/router.ts` and `app/src/modules`).

## Core Routes
These are the root-level routes registered by the main router:
- `/` (Redirects to Setup or Login)
- `/setup` - Initial setup process
- `/login` - User login page
- `/reset-password` - Password reset
- `/register` - User registration
- `/accept-invite` - Accepting user invitations
- `/tfa-setup` - Two-factor authentication setup
- `/logout` - User logout
- `/shared/:id` - Shared items
- `/:_.+)+` - Private Not Found (Catch-all private 404)

## Module Routes
Module routes are mounted under their respective module ID prefixes (`/:moduleId/*`).

### Activity Module (`/activity`)
- `/activity`
- `/activity/:primaryKey`

### Content Module (`/content`)
- `/content`
- `/content/:collection`
- `/content/:collection/:primaryKey`
- `/content/:collection/:primaryKey/preview`
- `/content/:_(.+)+`

### Deployment Module (`/deployment`)
- `/deployment`
- `/deployment/:provider`
- `/deployment/:provider/settings`
- `/deployment/:provider/:projectId/runs`
- `/deployment/:provider/:projectId/runs/:runId`

### Files Module (`/files`)
- `/files`
- `/files/+`
- `/files/:primaryKey`
- `/files/folders`
- `/files/folders/:folder`
- `/files/folders/:folder/+`

### Insights Module (`/insights`)
- `/insights`
- `/insights/:primaryKey`
- `/insights/:primaryKey/:panelKey`

### Settings Module (`/settings`)
- `/settings`
- `/settings/project`
- `/settings/appearance`
- `/settings/data-model`
- `/settings/data-model/+`
- `/settings/data-model/:collection`
- `/settings/data-model/:collection/:field`
- `/settings/policies`
- `/settings/policies/+`
- `/settings/policies/:primaryKey`
- `/settings/roles`
- `/settings/roles/+`
- `/settings/roles/public`
- `/settings/roles/:primaryKey`
- `/settings/presets`
- `/settings/presets/:id`
- `/settings/ai`
- `/settings/flows`
- `/settings/flows/:primaryKey`
- `/settings/flows/:primaryKey/:operationId`
- `/settings/extensions`
- `/settings/marketplace`
- `/settings/marketplace/account/:accountId`
- `/settings/marketplace/extension/:extensionId`
- `/settings/translations`
- `/settings/translations/:primaryKey`
- `/settings/system-logs`
- `/settings/:_(.+)+`

### Users Module (`/users`)
- `/users`
- `/users/+`
- `/users/:primaryKey`

### Visual Module (`/visual`)
- `/visual`
- `/visual/invalid-url`
- `/visual/no-url`
- `/visual/:url(.*)`
