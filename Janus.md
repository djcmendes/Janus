# Janus Project Blueprint & System Design

## 1. System Design Evaluation
Your proposed technology stack is robust, scalable, and tailored for high performance and enterprise use cases. Here is a breakdown of your choices:

*   **Database (MariaDB):** Excellent choice for a primary relational store. It's performant, reliable, and perfectly suited for a CMS/Data Platform core where relational integrity and dynamic schema generation matter.
*   **Cache (Redis):** Essential for a Directus-like platform to cache permissions, database schema structures, and frequent API responses to maintain low latency.
*   **Messaging (RabbitMQ / Kafka / Solace):**
    *   *RabbitMQ* is generally best for traditional task queuing (e.g., dispatching emails, triggering webhooks, background image processing).
    *   *Kafka* excels in high-throughput event streaming (e.g., event sourcing, massive user analytics pipelines). For a CMS, RabbitMQ is usually the sweet spot, but Kafka allows for a purely event-driven architecture.
    *   *(Recommendation: Start with RabbitMQ for simplicity or Kafka if you anticipate massive data streaming).*
*   **Backend (PHP + C++/Rust):** 
    *   *PHP framework:* **Laravel** offers the fastest time-to-market with its rich ecosystem (Eloquent ORM for schema discovery, Queues). **Symfony** provides a more rigid, highly decoupled architecture which might be better for an API-only core engine. 
    *   *Compiled Extensions (C++/Rust):* Using Rust (via `ext-php-rs` or FFI) to handle CPU-heavy tasks (like image manipulation, complex data serializations, or custom protocol parsing) will give your PHP app a massive performance edge where it typically bottlenecks.
*   **Monitoring/Analytics (InfluxDB):** InfluxDB is fantastic for time-series data. It will perfectly handle application metrics, API latency tracking, and feeding data to machine learning models.
*   **Frontend (Angular + Playwright):** Angular's opinionated, enterprise-ready structure (RxJS, strong typing, Dependency Injection) is perfect for building a complex, state-heavy admin UI. Playwright is currently the top-tier choice for resilient and fast end-to-end testing.
*   **Research/Data Science (Python / Jupyter Notebooks):** Adding a Jupyter environment alongside the stack is a brilliant move. It allows you to seamlessly query MariaDB or InfluxDB, build machine learning models, and generate interactive documentation or bash setup scripts.

---

## 2. Professional Prompt Engineering
If you want to feed this architecture into an LLM (like Gemini) to bootstrap parts of the project, you need a precise system prompt. 

**Use the following prompt to ask Gemini to bootstrap the project:**

> "Act as a Lead Solutions Architect and Senior Full-Stack Engineer. I am building **Janus**, a headless CMS and Real-Time Data Platform (similar to Directus), but with a custom architecture. 
> 
> **Technology Stack:**
> - **Backend:** PHP (Laravel or Symfony) serving a REST/GraphQL API. Performance-critical modules will be written as Native Rust/C++ extensions via FFI.
> - **Database:** MariaDB (Primary Data Store).
> - **Caching:** Redis.
> - **Message Broker:** RabbitMQ (for webhooks, async tasks, and event distribution).
> - **Frontend:** Angular 17+ for the Administration SPA, tested with Playwright.
> - **Observability & Analytics:** InfluxDB for time-series metrics, monitoring, and machine learning data.
> - **Data Science / Tooling:** Jupyter Notebooks (Python) for research, build scripts, and dashboard prototyping.
> 
> **First Phase Objective:**
> Please generate a comprehensive `docker-compose.yml` that networks all these services together for local development. Then, outline the directory structure for the monorepo, and provide the initialization commands for the PHP backend and Angular frontend. Ensure the architecture promotes domain-driven design and microservice-readiness."

---

## 3. Project Implementation Plan (TODOs)

### Phase 1: Infrastructure & Orchestration
- [ ] Initialize Git repository for the monorepo.
- [ ] Create `docker-compose.yml`.
  - [ ] Add MariaDB container.
  - [ ] Add Redis container.
  - [ ] Add Message Broker container RabbitMQ.
  - [ ] Add InfluxDB container.
  - [ ] Add JupyterLab container (Python/Notebooks).
  - [ ] use .docker folder for docker like files and configs needed
- [ ] Create `Makefile` or initialization bash scripts to easily spin up and tear down the environment.

### Phase 2: Backend Core (PHP)
- [ ] Initialize the PHP Framework Symfony in `backend/`.
- [ ] Setup Onion Arquitecture with modules and CQRS, we can start by module "heimdall" 
      - API_VERSION,
      - AUTHORIZATION
      - TYPE CLIENTS
      - API_SCOPE
      - RequestGuard
      - authentication

      where we then can use in our own way things like:
      
          $this->guard->validate_webservice_request(static::API_VERSION_GET, ApiScope::LOCAL);
          $this->guard->authorize(Client::ANDROID, Client::IOS, Client::VEI);

          also

        $this->guard->validate_webservice_request(static::API_VERSION_PATCH, ApiScope::LOCAL);
        $this->guard->authorize(Client::ANDROID, Client::IOS);

        $user_id = $this->guard->validate_user_id();
        if ($this->request->api_version < ApiVersion::JANUS_150)
        {
            $user_id = $this->guard->validate_authenticated_user_id();
        }

        some reference links : 
         - https://cheatography.com/vikbert/cheat-sheets/onion-architecture-symfony/
         - https://dev.to/yasmine_ddec94f4d4/onion-architecture-in-domain-driven-design-ddd-35gn

- [ ] Configure database connections (MariaDB) and caching (Redis) via environment variables.
- [ ] Implement generic dynamic routing and schema inspection (the core engine of a headless CMS).
- [ ] Set up the Message Queue publisher/consumer for the broker.
- [ ] Integrate InfluxDB client to push API metrics (response times, query counts).
- [ ] Create a proof-of-concept Rust FFI extension (e.g., a lightning-fast data parser) and link it to PHP.

### Phase 3: Frontend Admin Panel (Angular)
- [ ] Initialize Angular workspace in `frontend/`.
- [ ] Configure Playwright for E2E testing (`npm init playwright@latest`).
- [ ] Setup Angular application architecture (Core, Shared, Features/Modules).
- [ ] Create the authentication flow (Login/Register views hitting the PHP API).
- [ ] Implement the dynamic collection/item viewer component using Angular Material or a custom UI library.
- [ ] Implement routes and examples pages, no need for functionality just some text indicating where we are and what section with all pages we can find in examples forlder.
- [ ] Create a page, list of all pages in a table, where we can go inside the page (routes list-pages and ttoute page) where we can configure and setup page, for example if we go inside the page we can see a form to create a new page, with fields like title, description, content, etc. and a button to save the page. and a list of all pages in a table, where we can go inside the page (routes list-pages and ttoute page) where we can configure and setup page, for example if we go inside the page we can see a form to create a new page, with fields like title, description, content, etc. and a button to save the page.

### Phase 4: Data Science & Tooling
- [ ] Set up the `notebooks/` directory.
- [ ] Create a Jupyter Notebook that connects to MariaDB to query schema information for documentation.
- [ ] Create a Jupyter Notebook that connects to InfluxDB to plot API performance metrics.

### Phase 5: CI/CD & Testing
- [ ] Write backend unit/feature tests (PHPUnit/Pest).
- [ ] Write frontend E2E tests covering the login and CRUD operations (Playwright).
- [ ] Create GitHub Actions (or GitLab CI) pipeline to run tests and build Docker containers.
