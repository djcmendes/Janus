# Activity Module

Read-only audit-log module that records every significant action performed inside Janus (content mutations, logins, deletions, etc.) and exposes that history through a paginated HTTP API.

---

## What this module does

- **Records** audit entries automatically via `ActivityLogger` — any other module can inject this service and call `log()` to persist an event.
- **Captures context** (client IP address and User-Agent) from the active HTTP request at the moment of logging, with no extra work required from the caller.
- **Exposes** two read-only REST endpoints (admin-only) for browsing and inspecting activity records.

---

## Folder Structure

```
Activity/
├── Application/
│   ├── DTO/
│   │   └── ActivityDto.php              # Read model — maps an Activity entity to a serialisable array
│   └── Query/
│       ├── GetActivityQuery.php         # Payload: limit, offset, collection, action, userId filters
│       ├── GetActivityByIdQuery.php     # Payload: single UUID lookup
│       └── Handler/
│           ├── GetActivityHandler.php   # Returns paginated ActivityDto[] + total count
│           └── GetActivityByIdHandler.php # Returns a single ActivityDto or throws ActivityNotFoundException
├── Domain/
│   ├── Entity/
│   │   └── Activity.php                # Core entity — UUID v7, action, collection, item, userId, ip, userAgent, timestamp
│   ├── Exception/
│   │   └── ActivityNotFoundException.php
│   ├── Repository/
│   │   └── ActivityRepositoryInterface.php  # record(), findById(), findPaginated(), countAll()
│   └── Service/
│       ├── ActivityLogger.php           # Public logging API for other modules — inject and call log()
│       └── Test/
│           └── ActivityLoggerTest.php
├── Infrastructure/
│   └── Repository/
│       └── ActivityRepository.php       # Doctrine ORM implementation of ActivityRepositoryInterface
└── Presentation/
    └── Controller/
        └── ActivityController.php       # HTTP layer — mounts on /activity
```

---

## REST Endpoints

| Method | Path            | Description                                             |
|--------|-----------------|---------------------------------------------------------|
| `GET`  | `/activity`     | Paginated list of activity records. Supports filters.   |
| `GET`  | `/activity/{id}`| Single activity record by UUID.                         |

### Query Parameters (`GET /activity`)

| Parameter    | Type   | Default | Max  | Description                          |
|--------------|--------|---------|------|--------------------------------------|
| `limit`      | int    | `25`    | `100`| Records per page                     |
| `offset`     | int    | `0`     | —    | Pagination offset                    |
| `collection` | string | —       | —    | Filter by affected collection name   |
| `action`     | string | —       | —    | Filter by action type (e.g. `create`)|
| `user`       | UUID   | —       | —    | Filter by acting user UUID           |

### Response Envelope

```json
// List
{
  "data": [ { "id": "...", "action": "create", "collection": "posts", "item": "42", "user": "...", "ip": "...", "user_agent": "...", "timestamp": "2026-05-05T12:00:00+00:00" } ],
  "meta": { "total_count": 200, "filter_count": 1 }
}

// Single
{ "data": { ... } }

// Not found
{ "errors": [ { "message": "Activity not found", "extensions": { "code": "NOT_FOUND" } } ] }
```

---

## How to Log an Activity from Another Module

Inject `ActivityLogger` into any service or handler:

```php
use App\Activity\Domain\Service\ActivityLogger;

public function __construct(private readonly ActivityLogger $activityLogger) {}

$this->activityLogger->log(
    action:     'create',
    collection: 'posts',
    item:       (string) $post->getId(),
    userId:     $currentUserId,
);
```

IP and User-Agent are captured automatically from the current request — no extra arguments needed.

---

## External Dependencies

These are dependencies on classes **outside** the `Activity` module namespace (`App\Activity\*`).

### Heimdall module (`App\Heimdall\*`)

| Class | Where used | Purpose |
|-------|-----------|---------|
| `RequestGuard` | `ActivityController` | Validates API version, scope (`AUTHENTICATED`), and permitted clients (`WEB`, `IOS`, `ANDROID`) |
| `ApiScope` | `ActivityController` | Enum constant `AUTHENTICATED` passed to `RequestGuard` |
| `ApiVersion` | `ActivityController` | Enum constant `JANUS_100` passed to `RequestGuard` |
| `Client` | `ActivityController` | Enum constants `WEB`, `IOS`, `ANDROID` passed to `RequestGuard` |

### Symfony Framework

| Component | Where used | Purpose |
|-----------|-----------|---------|
| `symfony/framework-bundle` — `AbstractController` | `ActivityController` | Base controller (provides `json()`, `denyAccessUnlessGranted()`) |
| `symfony/http-foundation` — `Request`, `JsonResponse`, `Response` | `ActivityController` | HTTP request/response primitives |
| `symfony/routing` — `#[Route]` | `ActivityController` | Attribute-based route registration |
| `symfony/http-foundation` — `RequestStack` | `ActivityLogger` | Reads client IP and User-Agent from the active request |
| `symfony/uid` — `Uuid` | `Activity` entity | Generates UUID v7 as the primary key |

### Doctrine ORM

| Component | Where used | Purpose |
|-----------|-----------|---------|
| `doctrine/orm` — `#[ORM\*]` attributes | `Activity` entity | Maps the entity to the `activity` table |
| `doctrine/doctrine-bundle` — `ServiceEntityRepository` | `ActivityRepository` | Base repository providing DQL query builder |
| `doctrine/persistence` — `ManagerRegistry` | `ActivityRepository` | Required by `ServiceEntityRepository` constructor |

---

## Access Control

All endpoints require:
- A valid JWT access token (`ApiScope::AUTHENTICATED`)
- The `ROLE_ADMIN` Symfony role

Regular authenticated users cannot access activity logs.
