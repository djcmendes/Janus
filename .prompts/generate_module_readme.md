# Generate Module README

Create a `README.md` inside `{MODULE_DIR}` (e.g. `backend.janus.com/src/{ModuleName}/`) documenting the module.

Read every file in the module directory before writing. The README must reflect the actual code — never invent endpoints, classes, or dependencies.

---

## Sections to include

### 1. Title + one-line summary

```markdown
# {ModuleName}

One sentence describing the module's responsibility within the system.
```

### 2. Folder structure

Annotated tree of every subdirectory and its role:

```
{ModuleName}/
  Domain/
    Entity/       ← description
    Repository/   ← description
    ...
  Application/
    Command/      ← description
    ...
  Infrastructure/
    Repository/   ← description
  Presentation/
    Controller/   ← description
```

### 3. REST Endpoints

Table of every HTTP route the module exposes:

| Method | Path | Auth | Description |
|---|---|---|---|
| `GET` | `/resource` | Required | What it returns |
| `POST` | `/resource` | Required | What it creates |

Include the auth requirement per endpoint (public / authenticated / admin).

### 4. Query Parameters (if applicable)

Table for endpoints that accept filter/pagination parameters:

| Parameter | Type | Default | Description |
|---|---|---|---|
| `limit` | `int` | `25` | Max records per page |
| `offset` | `int` | `0` | Pagination offset |
| `filter` | `string` | — | What it filters |

### 5. Response Envelope

Show the exact JSON shape returned, including the `data` / `meta` / `errors` envelope.

For collections:

```json
{
  "data": [ { ... } ],
  "meta": {
    "total_count": 100,
    "filter_count": 25
  }
}
```

For single items:

```json
{
  "data": { ... }
}
```

For errors:

```json
{
  "errors": [
    { "message": "...", "extensions": { "code": "..." } }
  ]
}
```

### 6. Key Classes

Table of the most important classes and their roles:

| Class | File | Role |
|---|---|---|
| `{Entity}` | `Domain/Entity/{Entity}.php` | What it models |
| `{Repository}` | `Infrastructure/Repository/...` | What it persists/queries |
| `{Service}` | `Domain/Service/...` | What business logic it owns |

Only list classes that are non-trivial to understand from the name alone.

### 7. External Dependencies

Two sub-sections — internal modules and third-party packages.

#### Internal modules (other `src/` modules this module calls)

| Module | Class / Service used | Why |
|---|---|---|
| Heimdall | `RequestGuard` | Authentication and authorization |

#### Third-party packages

| Package | Used via | Why |
|---|---|---|
| Symfony HttpFoundation | `JsonResponse` | HTTP response construction |
| Doctrine ORM | `ServiceEntityRepository` | Database persistence |

List only dependencies the module **directly** imports — do not list transitive ones.

---

## Style rules

- Use present tense: "Returns…", "Stores…", "Validates…"
- Keep descriptions to one sentence per row
- Do not duplicate information already obvious from the class or method name
- Do not add a "Contributing" or "License" section
- Do not add placeholders or TODOs — if something is not implemented, omit the section
