# Dashboards

Manages user dashboards — named workspaces that aggregate panels and visualisations. Dashboards may be owned by a specific user or shared globally (owner = null). Only ROLE_ADMIN users may create, update, or delete dashboards.

---

## Folder Structure

```
Dashboards/
  Domain/
    Entity/       ← Pure Dashboard POPO — no framework dependencies
    Repository/   ← DashboardRepositoryInterface (domain contract)
    Exception/    ← DashboardNotFoundException
  Application/
    Command/      ← CreateDashboardCommand, UpdateDashboardCommand, DeleteDashboardCommand
    Command/Handler/ ← CreateDashboardHandler, UpdateDashboardHandler, DeleteDashboardHandler
    Query/        ← GetDashboardsQuery, GetDashboardByIdQuery
    Query/Handler/ ← GetDashboardsHandler, GetDashboardByIdHandler
    DTO/          ← DashboardDto (response shape)
  Infrastructure/
    Persistence/
      Doctrine/
        Entity/   ← DashboardEntity (Doctrine ORM mapping for `dashboards` table)
        Mapper/   ← DashboardMapper (toDomain / toPersistence)
    Repository/   ← DashboardRepository (Doctrine implementation)
  Presentation/
    Controller/   ← DashboardsController (thin HTTP layer)
    DTO/          ← CreateDashboardRequest, UpdateDashboardRequest
```

---

## REST Endpoints

| Method   | Path                  | Auth               | Description                                       |
|----------|-----------------------|--------------------|---------------------------------------------------|
| `GET`    | `/dashboards`         | Authenticated      | Returns a paginated list of dashboards            |
| `GET`    | `/dashboards/{id}`    | Authenticated      | Returns a single dashboard by UUID                |
| `POST`   | `/dashboards`         | ROLE_ADMIN         | Creates a new dashboard                           |
| `PATCH`  | `/dashboards/{id}`    | ROLE_ADMIN         | Updates dashboard fields (partial update)         |
| `DELETE` | `/dashboards/{id}`    | ROLE_ADMIN         | Deletes a dashboard; cascade-removes its panels   |

---

## Query Parameters

The `GET /dashboards` endpoint accepts the following optional parameters:

| Parameter | Type     | Default | Description                                             |
|-----------|----------|---------|---------------------------------------------------------|
| `limit`   | `int`    | `25`    | Maximum number of records per page                      |
| `offset`  | `int`    | `0`     | Pagination offset                                       |
| `user`    | `string` | —       | Filter by owner UUID (ROLE_ADMIN only; ignored otherwise) |

---

## Response Envelope

**Collection (`GET /dashboards`):**

```json
{
  "data": [
    {
      "id": "uuid",
      "name": "My Dashboard",
      "icon": "chart",
      "note": "Optional description",
      "userId": "user-uuid",
      "createdAt": "2024-01-01T00:00:00+00:00",
      "updatedAt": "2024-06-01T00:00:00+00:00"
    }
  ],
  "meta": {
    "total_count": 10,
    "filter_count": 10
  }
}
```

**Single item (`GET /dashboards/{id}`, `POST /dashboards`, `PATCH /dashboards/{id}`):**

```json
{
  "data": {
    "id": "uuid",
    "name": "My Dashboard",
    "icon": null,
    "note": null,
    "userId": "user-uuid",
    "createdAt": "2024-01-01T00:00:00+00:00",
    "updatedAt": "2024-06-01T00:00:00+00:00"
  }
}
```

**Error:**

```json
{
  "errors": [
    { "message": "Dashboard \"uuid\" not found.", "extensions": { "code": "NOT_FOUND" } }
  ]
}
```

---

## Key Classes

| Class                      | File                                                              | Role                                                         |
|----------------------------|-------------------------------------------------------------------|--------------------------------------------------------------|
| `Dashboard`                | `Domain/Entity/Dashboard.php`                                     | Pure domain entity; owns `isOwnedBy()` ownership check      |
| `DashboardEntity`          | `Infrastructure/Persistence/Doctrine/Entity/DashboardEntity.php`  | Doctrine ORM model; sole owner of `#[ORM\*]` attributes     |
| `DashboardMapper`          | `Infrastructure/Persistence/Doctrine/Mapper/DashboardMapper.php`  | Converts between domain Dashboard and Doctrine DashboardEntity |
| `DashboardRepository`      | `Infrastructure/Repository/DashboardRepository.php`               | Doctrine implementation; uses mapper for all reads/writes   |
| `DeleteDashboardHandler`   | `Application/Command/Handler/DeleteDashboardHandler.php`          | Cascade-removes panels before deleting the dashboard        |
| `DashboardNotFoundException` | `Domain/Exception/DashboardNotFoundException.php`               | Thrown when a dashboard UUID yields no result               |

---

## External Dependencies

### Internal modules

| Module   | Class / Service used          | Why                                                              |
|----------|-------------------------------|------------------------------------------------------------------|
| Heimdall | `RequestGuard`                | Authentication, client authorisation, and user-id extraction     |
| Panels   | `PanelRepositoryInterface`    | Cascade-delete all panels belonging to a dashboard on deletion   |

### Third-party packages

| Package                | Used via                      | Why                              |
|------------------------|-------------------------------|----------------------------------|
| Symfony HttpFoundation | `JsonResponse`, `Request`     | HTTP request/response handling   |
| Doctrine ORM           | `ServiceEntityRepository`     | Database persistence             |
| Symfony UID            | `Uuid`                        | UUIDv7 generation                |
