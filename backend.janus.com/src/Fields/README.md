# Fields Module

Manages field metadata for CMS collections — each field record describes a column's display properties (label, type, required, hidden, sort order, admin UI interface) without owning the underlying DDL. Write operations also add or drop the corresponding column in the collection's backing table via `SchemaManagerService`.

---

## Folder Structure

```
src/Fields/
  Domain/
    Entity/
      FieldMeta.php                                    ← Pure POPO — no framework imports
    Repository/
      FieldMetaRepositoryInterface.php                 ← Contract for persistence
    Enum/
      FieldType.php                                    ← Typed column categories; isAlias() / toColumnDdl()
    Exception/
      FieldAlreadyExistsException.php
      FieldNotFoundException.php
  Application/
    DTO/
      FieldDto.php                                     ← Read-side response shape
    Command/
      CreateFieldCommand.php
      UpdateFieldCommand.php                           ← UNCHANGED sentinel for no-op fields
      DeleteFieldCommand.php
      Handler/
        CreateFieldHandler.php                         ← Saves meta + addColumn DDL (skips alias types)
        UpdateFieldHandler.php                         ← Partial update via UNCHANGED sentinel
        DeleteFieldHandler.php                         ← Removes meta + dropColumn DDL (skips alias types)
    Query/
      GetFieldsQuery.php                               ← Paginated global list
      GetFieldsByCollectionQuery.php                   ← All fields for one collection
      GetFieldByCollectionAndNameQuery.php             ← Single field lookup
      Handler/
        GetFieldsHandler.php
        GetFieldsByCollectionHandler.php
        GetFieldByCollectionAndNameHandler.php
  Infrastructure/
    Persistence/
      Doctrine/
        Entity/
          FieldMetaEntity.php                          ← ORM mapping for janus_fields table
        Mapper/
          FieldMetaMapper.php                          ← Converts between domain and Doctrine model
    Repository/
      FieldMetaRepository.php                         ← Doctrine ServiceEntityRepository implementation
  Presentation/
    Controller/
      FieldsController.php                            ← HTTP layer, delegates to Application handlers
    DTO/
      CreateFieldRequest.php                          ← Input validation for POST
      UpdateFieldRequest.php                          ← Input shape for PATCH
```

---

## REST Endpoints

| Method   | Path                               | Auth          | Role       | Description                                         |
|----------|------------------------------------|---------------|------------|-----------------------------------------------------|
| `GET`    | `/fields`                          | Authenticated | any        | Paginated list of all field records                 |
| `GET`    | `/fields/{collection}`             | Authenticated | any        | All fields belonging to the specified collection    |
| `GET`    | `/fields/{collection}/{field}`     | Authenticated | any        | Single field by collection + column name            |
| `POST`   | `/fields/{collection}`             | Authenticated | ROLE_ADMIN | Create a new field record + add column to table     |
| `PATCH`  | `/fields/{collection}/{field}`     | Authenticated | ROLE_ADMIN | Partial update of field metadata                    |
| `DELETE` | `/fields/{collection}/{field}`     | Authenticated | ROLE_ADMIN | Delete field record + drop column from table        |

All endpoints require a valid JWT. Read endpoints accept `WEB`, `IOS`, and `ANDROID` clients; write endpoints accept `WEB` only and additionally require `ROLE_ADMIN`.

---

## Query Parameters — `GET /fields`

| Parameter | Type | Default | Max | Description             |
|-----------|------|---------|-----|-------------------------|
| `limit`   | int  | `25`    | 100 | Records per page        |
| `offset`  | int  | `0`     | —   | Pagination start offset |

---

## Request Body — `POST /fields/{collection}`

```json
{
  "field": "title",
  "type": "string",
  "label": "Title",
  "note": "Article headline",
  "required": true,
  "readonly": false,
  "hidden": false,
  "sort": 1,
  "interface": "input",
  "options": { "placeholder": "Enter title..." }
}
```

`field` and `type` are required. `field` must match `/^[a-z][a-z0-9_]{0,63}$/i`. `type` must be a valid `FieldType` value. All other fields are optional.

## Request Body — `PATCH /fields/{collection}/{field}`

```json
{
  "label": "Headline",
  "required": true
}
```

Only fields present in the body are updated. Omitted fields are left unchanged (handled by the `UNCHANGED` sentinel in `UpdateFieldCommand`).

---

## Response Envelopes

### Paginated list — `GET /fields`

```json
{
  "data": [
    {
      "id": "01934c7e-...",
      "collection": "articles",
      "field": "title",
      "type": "string",
      "label": "Title",
      "note": null,
      "required": true,
      "readonly": false,
      "hidden": false,
      "sort": 1,
      "interface": "input",
      "options": null,
      "created_at": "2024-01-01T00:00:00+00:00",
      "updated_at": null
    }
  ],
  "meta": {
    "total_count": 42,
    "filter_count": 5
  }
}
```

### Single item — `GET /fields/{collection}/{field}` / `POST` / `PATCH`

```json
{
  "data": {
    "id": "01934c7e-...",
    "collection": "articles",
    "field": "title",
    "type": "string",
    "label": "Title",
    "note": null,
    "required": true,
    "readonly": false,
    "hidden": false,
    "sort": 1,
    "interface": "input",
    "options": null,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": null
  }
}
```

### Deleted — `DELETE /fields/{collection}/{field}`

HTTP `204 No Content` — empty body.

### Not Found — 404

```json
{
  "errors": [
    {
      "message": "Field \"title\" in collection \"articles\" not found.",
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
      "message": "Field \"title\" already exists in collection \"articles\".",
      "extensions": { "code": "FIELD_EXISTS" }
    }
  ]
}
```

### Validation Error — 422

```json
{
  "errors": [
    {
      "message": "field is required.",
      "extensions": { "code": "VALIDATION_ERROR" }
    }
  ]
}
```

---

## FieldType Enum

| Value       | DDL Column Type      | Notes                                               |
|-------------|----------------------|-----------------------------------------------------|
| `string`    | `VARCHAR(255)`       |                                                     |
| `text`      | `LONGTEXT`           |                                                     |
| `integer`   | `INT`                |                                                     |
| `biginteger`| `BIGINT`             |                                                     |
| `float`     | `FLOAT`              |                                                     |
| `decimal`   | `DECIMAL(10,2)`      |                                                     |
| `boolean`   | `TINYINT(1)`         |                                                     |
| `date`      | `DATE`               |                                                     |
| `datetime`  | `DATETIME`           |                                                     |
| `timestamp` | `TIMESTAMP`          |                                                     |
| `time`      | `TIME`               |                                                     |
| `json`      | `JSON`               |                                                     |
| `uuid`      | `CHAR(36)`           |                                                     |
| `alias`     | *(no column)*        | Virtual field — `CreateFieldHandler` and `DeleteFieldHandler` skip DDL for this type |

---

## Key Design Decisions

### UNCHANGED sentinel

`UpdateFieldCommand` and `UpdateFieldRequest` use the sentinel string `'__UNCHANGED__'` to distinguish "key absent from request body" from "key explicitly set to null". The handler skips applying any property equal to the sentinel, enabling true partial updates.

### `$interface` property naming

`interface` is a reserved word in PHP. The domain entity and all related classes use `$interface` as the property name, accessed via `getInterface()` / `setInterface()`. This is valid PHP 8.3.

### Alias type skips DDL

`FieldType::ALIAS` represents a virtual computed field. `CreateFieldHandler` and `DeleteFieldHandler` check `FieldType::isAlias()` and skip `SchemaManagerService::addColumn()` / `dropColumn()` for alias-typed fields.

### Double-model pattern

`FieldMeta` is a pure PHP class with zero framework dependencies. `FieldMetaEntity` holds all `#[ORM\*]` attributes and maps to the `janus_fields` table. `FieldMetaMapper` translates between the two, using `FieldMeta::reconstitute()` on reads to avoid generating a new UUID or resetting `createdAt`.

---

## Key Classes

| Class | File | Role |
|---|---|---|
| `FieldMeta` | `Domain/Entity/FieldMeta.php` | Pure domain entity; UUIDv7 id; fluent setters call `touch()` |
| `FieldType` | `Domain/Enum/FieldType.php` | Backed enum; `isAlias()` and `toColumnDdl()` helpers |
| `FieldMetaRepositoryInterface` | `Domain/Repository/FieldMetaRepositoryInterface.php` | Persistence contract |
| `FieldMetaEntity` | `Infrastructure/Persistence/Doctrine/Entity/FieldMetaEntity.php` | Doctrine ORM mapping for `janus_fields` table |
| `FieldMetaMapper` | `Infrastructure/Persistence/Doctrine/Mapper/FieldMetaMapper.php` | `toDomain()` / `toPersistence()` bidirectional conversion |
| `FieldMetaRepository` | `Infrastructure/Repository/FieldMetaRepository.php` | Doctrine implementation; `save()`, `delete()`, `findByCollectionAndField()`, `findByCollection()`, `findPaginated()`, `countAll()`, `deleteByCollection()` |
| `FieldDto` | `Application/DTO/FieldDto.php` | `fromEntity()` factory + `toArray()` (key `'sort'` for sortOrder) |
| `CreateFieldHandler` | `Application/Command/Handler/CreateFieldHandler.php` | Saves meta + calls `SchemaManagerService::addColumn()` unless alias type |
| `UpdateFieldHandler` | `Application/Command/Handler/UpdateFieldHandler.php` | Applies partial patch using `UpdateFieldCommand::UNCHANGED` sentinel |
| `DeleteFieldHandler` | `Application/Command/Handler/DeleteFieldHandler.php` | Removes meta + calls `SchemaManagerService::dropColumn()` unless alias type |
| `FieldsController` | `Presentation/Controller/FieldsController.php` | Thin HTTP layer; all 6 handlers injected via constructor |

---

## External Dependencies

| Module / Package | Symbol | Used By |
|---|---|---|
| **Collections module** | `CollectionMetaRepositoryInterface` | `CreateFieldHandler` — verifies collection exists before adding field |
| **Collections module** | `CollectionNotFoundException` | `CreateFieldHandler` — thrown when collection is missing |
| **Collections module** | `SchemaManagerService` | `CreateFieldHandler`, `DeleteFieldHandler` — DDL column add/drop |
| **Heimdall module** | `RequestGuard` | `FieldsController` — `validate_webservice_request()`, `authorize()` |
| **Heimdall module** | `ApiScope`, `ApiVersion`, `Client` | `FieldsController` — guard configuration |
| **Doctrine ORM** | `ServiceEntityRepository` | `FieldMetaRepository` base class |
| **Symfony UID** | `Uuid` | `FieldMeta` — generates UUIDv7 on construction |
