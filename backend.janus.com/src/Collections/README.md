# Collections Module

Manages CMS collection definitions — each collection maps to a database table created and dropped dynamically via DDL. The module owns both the metadata (name, label, icon, etc.) and the schema lifecycle of those tables.

---

## Folder Structure

```
src/Collections/
  Domain/
    Entity/
      CollectionMeta.php                    ← Pure POPO — no framework imports
    Repository/
      CollectionMetaRepositoryInterface.php ← Contract for persistence
    Exception/
      CollectionAlreadyExistsException.php
      CollectionNotFoundException.php
  Application/
    DTO/
      CollectionDto.php                     ← Read-side response shape
    Command/
      CreateCollectionCommand.php
      UpdateCollectionCommand.php           ← UNCHANGED sentinel for no-op fields
      DeleteCollectionCommand.php
      Handler/
        CreateCollectionHandler.php         ← Creates meta + DDL table + PK field
        UpdateCollectionHandler.php         ← Partial update via UNCHANGED sentinel
        DeleteCollectionHandler.php         ← Drops table + removes fields + meta
    Query/
      GetCollectionsQuery.php
      GetCollectionByNameQuery.php
      Handler/
        GetCollectionsHandler.php
        GetCollectionByNameHandler.php
  Infrastructure/
    Persistence/
      Doctrine/
        Entity/
          CollectionMetaEntity.php          ← ORM mapping for janus_collections table
        Mapper/
          CollectionMetaMapper.php          ← Converts between domain and Doctrine model
    Repository/
      CollectionMetaRepository.php         ← Doctrine ServiceEntityRepository implementation
    Service/
      SchemaManagerService.php             ← DDL: createTable(), dropTable(), system-table guard
  Presentation/
    Controller/
      CollectionsController.php            ← HTTP layer, delegates to Application handlers
    DTO/
      CreateCollectionRequest.php          ← Input validation for POST
      UpdateCollectionRequest.php          ← Input shape for PATCH
```

---

## REST Endpoints

| Method   | Path                    | Auth          | Role       | Description                                      |
|----------|-------------------------|---------------|------------|--------------------------------------------------|
| `GET`    | `/collections`          | Authenticated | any        | Paginated list of all collections                |
| `GET`    | `/collections/{name}`   | Authenticated | any        | Single collection by name                        |
| `POST`   | `/collections`          | Authenticated | ROLE_ADMIN | Create a new collection + backing table + PK field |
| `PATCH`  | `/collections/{name}`   | Authenticated | ROLE_ADMIN | Partial update of collection metadata            |
| `DELETE` | `/collections/{name}`   | Authenticated | ROLE_ADMIN | Delete collection, its fields, and backing table  |

All endpoints require a valid JWT and `X-Client-Type: web` (WEB only for write operations; WEB / iOS / Android for reads).

---

## Query Parameters — `GET /collections`

| Parameter | Type | Default | Max | Description              |
|-----------|------|---------|-----|--------------------------|
| `limit`   | int  | `25`    | 100 | Records per page         |
| `offset`  | int  | `0`     | —   | Pagination start offset  |

---

## Request Body — `POST /collections`

```json
{
  "name": "articles",
  "label": "Articles",
  "icon": "article",
  "note": "Blog posts and editorial content",
  "hidden": false,
  "singleton": false,
  "sort_field": null,
  "primary_key_field": "id",
  "primary_key_type": "uuid"
}
```

`name` is required. All other fields are optional.

## Request Body — `PATCH /collections/{name}`

```json
{
  "label": "Featured Articles",
  "hidden": true
}
```

Only fields present in the body are updated. Omitted fields are left unchanged (handled by the `UNCHANGED` sentinel in `UpdateCollectionCommand`).

---

## Response Envelopes

### Paginated list — `GET /collections`

```json
{
  "data": [
    {
      "id": "01934c7e-...",
      "collection": "articles",
      "label": "Articles",
      "icon": "article",
      "note": null,
      "hidden": false,
      "singleton": false,
      "sort_field": null,
      "created_at": "2024-01-01T00:00:00+00:00",
      "updated_at": null
    }
  ],
  "meta": {
    "total_count": 12,
    "filter_count": 1
  }
}
```

### Single item — `GET /collections/{name}` / `POST` / `PATCH`

```json
{
  "data": {
    "id": "01934c7e-...",
    "collection": "articles",
    "label": "Articles",
    "icon": null,
    "note": null,
    "hidden": false,
    "singleton": false,
    "sort_field": null,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": null
  }
}
```

### Deleted — `DELETE /collections/{name}`

HTTP `204 No Content` — empty body.

### Not Found — 404

```json
{
  "errors": [
    {
      "message": "Collection \"articles\" not found.",
      "extensions": { "code": "NOT_FOUND" }
    }
  ]
}
```

### Already Exists — 409

```json
{
  "errors": [
    {
      "message": "Collection \"articles\" already exists.",
      "extensions": { "code": "COLLECTION_EXISTS" }
    }
  ]
}
```

### Validation Error — 422

```json
{
  "errors": [
    {
      "message": "Collection name is required.",
      "extensions": { "code": "VALIDATION_ERROR" }
    }
  ]
}
```

---

## Key Classes

| Class | File | Role |
|---|---|---|
| `CollectionMeta` | `Domain/Entity/CollectionMeta.php` | Pure domain entity; UUIDv7 id; fluent setters call `touch()` |
| `CollectionMetaRepositoryInterface` | `Domain/Repository/CollectionMetaRepositoryInterface.php` | Persistence contract |
| `CollectionMetaEntity` | `Infrastructure/Persistence/Doctrine/Entity/CollectionMetaEntity.php` | Doctrine ORM mapping for `janus_collections` table |
| `CollectionMetaMapper` | `Infrastructure/Persistence/Doctrine/Mapper/CollectionMetaMapper.php` | `toDomain()` / `toPersistence()` bidirectional conversion |
| `CollectionMetaRepository` | `Infrastructure/Repository/CollectionMetaRepository.php` | Doctrine implementation; `save()`, `delete()`, `findByName()`, `findPaginated()`, `count()` |
| `SchemaManagerService` | `Infrastructure/Service/SchemaManagerService.php` | DDL wrapper; `createTable()` / `dropTable()`; guards against system table names |
| `CollectionDto` | `Application/DTO/CollectionDto.php` | `fromEntity()` factory + `toArray()` (key `'collection'` for the name field) |
| `CreateCollectionHandler` | `Application/Command/Handler/CreateCollectionHandler.php` | Creates meta record, DDL table, and default PK field atomically |
| `UpdateCollectionHandler` | `Application/Command/Handler/UpdateCollectionHandler.php` | Applies partial patch using `UpdateCollectionCommand::UNCHANGED` sentinel |
| `DeleteCollectionHandler` | `Application/Command/Handler/DeleteCollectionHandler.php` | Removes all `FieldMeta` records, drops the backing table, deletes the meta record |
| `CollectionsController` | `Presentation/Controller/CollectionsController.php` | Thin HTTP layer; delegates all logic to Application handlers |

---

## External Dependencies

| Module / Package | Symbol | Used By |
|---|---|---|
| **Fields module** | `FieldMetaRepositoryInterface` | `CreateCollectionHandler` (inserts PK field), `DeleteCollectionHandler` (removes all fields) |
| **Fields module** | `FieldType` enum | `CreateCollectionHandler` — sets primary key field type |
| **Heimdall module** | `RequestGuard` | `CollectionsController` — `validateWebserviceRequest()`, `authorize()` |
| **Heimdall module** | `ApiScope`, `ApiVersion`, `Client` | `CollectionsController` — guard configuration |
| **Doctrine ORM** | `ServiceEntityRepository` | `CollectionMetaRepository` base class |
| **Doctrine DBAL** | `Connection` | `SchemaManagerService` — executes raw DDL |
| **Symfony UID** | `Uuid` | `CollectionMetaEntity` — stores UUID primary key; `CollectionMetaMapper` — converts string ↔ Uuid |
