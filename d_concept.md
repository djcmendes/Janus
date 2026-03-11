# Project Overview: Directus Monorepo

This project in the `examples` folder is a clone or subtree of the **Directus Monorepo** (`directus-monorepo`). Directus is a popular open-source Real-Time Data Platform that wraps a headless CMS and API around any SQL database.

## 1. What is going on in the project?

The project is structured as a large npm/pnpm workspace (monorepo). Its two main parts are:
- **API (Backend):** Located in the `api` folder. It provides the core engines, database connections, and REST/GraphQL APIs.
- **App (Frontend):** Located in the `app` folder. It is a Vue 3 Single Page Application (SPA) that acts as the administration UI for managing data, settings, and users in the Directus instance.
- **Packages/Extensions:** Other folders like `packages`, `sdk`, and `extensions` contain reusable code, official SDKs, and modular extensions.

## 2. Setting it up with Docker Compose

Inside the `examples` folder, there is a `docker-compose.yml` file. This specific Compose file is explicitly for **debugging and local development**, not for production. 

It spins up instances of practically every database vendor and service Directus supports, such as:
- PostgreSQL (10 & 13)
- MySQL (5.7 & 8)
- MariaDB
- Microsoft SQL Server
- Oracle DB
- CockroachDB
- Redis (for caching)
- Minio & Azure Blob (for S3 and blob storage)
- Maildev (for catching sent emails)
- Keycloak (for testing SSO and OAuth)

### How to start it
1. Navigate to the `examples` directory:
   ```bash
   cd /var/www/html/personal_projects/Janus/examples
   ```
2. Start the infrastructure via Docker Compose (make sure Docker is running):
   ```bash
   docker-compose up -d
   ```
3. To start the actual Directus app and api, you typically install node modules using `pnpm`:
   ```bash
   pnpm install
   pnpm build
   ```
   *Note: Because this is a monorepo development setup, you would typically use `pnpm --filter directus dev` or rely on the testing commands inside `package.json` to run the stack.*

## 3. The Backend Structure (`api`)
The backend is a Node.js-based application (`api/src`) that dynamically inspects your database schema and serves an API. Key concepts inside the backend:
- **Controllers / Services:** Business logic for entities (authentication, files, items, permissions).
- **Database Layer / Query Builder:** Interfaces with the SQL databases.
- **Middleware:** Express-like request lifecycle middlewares.
- **Auth:** Handles JWT generation, SSO, and OAuth providers.
- **Websockets:** Real-time data sync capabilities.

## 4. The Frontend Structure (`app`)
The frontend is built with Vue 3, Vite, and native Vue libraries (Pinia for state). It has a modular architecture with dynamic routing. Key parts:
- **Views / Modules:** Main areas inside the admin panel (Content, Settings, Users, etc.).
- **Stores:** Pinia state management for user sessions, server info, and app state.
- **Components:** Reusable UI components for the Directus design system.

### Frontend Pages (Routes)
The primary Vue routes found in the frontend (`app/src/router.ts`) handle the public-facing or setup parts of the admin panel before diving into the dynamic content system:

- `/` (Redirects to `/login` or `/setup` based on initialization)
- `/setup` (Initial database/admin setup wizard)
- `/login` (Standard login page)
- `/register` (Public user registration, if enabled)
- `/reset-password` (Password recovery flow)
- `/accept-invite` (Accepting an invitation to the platform)
- `/tfa-setup` (Two-Factor Authentication setup)
- `/logout` (Ends the session)
- `/shared/:id` (Accessing shared items/dashboards publicly)
- `/:_(.+)+` (Private wildcard/404 handling, where dynamically injected module routes usually happen)

Once authenticated, standard Directus injects routes dynamically based on active modules like Content Management, User Directory, File Library, Insights/Dashboards, and Settings.
