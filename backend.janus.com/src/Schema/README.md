# Schema Module

Manages schema snapshot, diff, and apply operations for the CMS.

---

## Directory Structure

```
Schema/
  Application/
    Command/
      ApplySchemaCommand.php                        ← Command payload (snapshot + force flag)
      Tests/
        ApplySchemaCommandTest.php                  ← Abstract base
        ApplySchemaCommandBaseTest.php
      Handler/
        ApplySchemaHandler.php                      ← Applies diff to live schema (13 deps)
        Tests/
          ApplySchemaHandlerTest.php                ← Abstract base
          ApplySchemaHandlerBaseTest.php
          ApplySchemaHandlerHandleTest.php
    Query/
      GetSchemaSnapshotQuery.php                    ← Marker query (no parameters)
      Tests/
        GetSchemaSnapshotQueryTest.php              ← Abstract base
        GetSchemaSnapshotQueryBaseTest.php
      Handler/
        GetSchemaSnapshotHandler.php                ← Delegates to SchemaSnapshotService
        Tests/
          GetSchemaSnapshotHandlerTest.php          ← Abstract base
          GetSchemaSnapshotHandlerBaseTest.php
          GetSchemaSnapshotHandlerHandleTest.php
  Domain/
    Service/
      SchemaSnapshotService.php                     ← Assembles full schema from repos
      SchemaDiffService.php                         ← Computes create/update/delete diff
      Tests/
        SchemaSnapshotServiceTest.php               ← Abstract base
        SchemaSnapshotServiceBaseTest.php
        SchemaSnapshotServiceSnapshotTest.php
        SchemaDiffServiceTest.php                   ← Abstract base
        SchemaDiffServiceBaseTest.php
        SchemaDiffServiceDiffTest.php
  Presentation/
    Controller/
      SchemaController.php                          ← HTTP controller (snapshot, diff, apply)
      Tests/
        SchemaControllerTest.php                    ← Abstract base
        SchemaControllerBaseTest.php
        SchemaControllerSnapshotTest.php
        SchemaControllerDiffTest.php
        SchemaControllerApplyTest.php
    DTO/
      ApplySchemaRequest.php                        ← Deserialization target for POST /schema/apply
      Tests/
        ApplySchemaRequestTest.php                  ← Abstract base
        ApplySchemaRequestBaseTest.php
```

---

## Endpoints

All endpoints require **ROLE_ADMIN** and support WEB + CLI clients.

| Method | Path             | Description                                         |
|--------|------------------|-----------------------------------------------------|
| GET    | /schema/snapshot | Export the full current schema as a JSON snapshot   |
| POST   | /schema/diff     | Diff a client-supplied snapshot against live schema |
| POST   | /schema/apply    | Apply a snapshot (force=true enables deletions)     |

---

## Snapshot Format

```json
{
  "version": 1,
  "collections": [
    {
      "collection": "articles",
      "meta": { "label": "Articles", "icon": null, "note": null, "hidden": false, "singleton": false, "sort_field": null },
      "fields": [
        { "field": "id", "type": "uuid", "meta": { "label": null, "note": null, "required": false, "readonly": false, "hidden": false, "sort_order": 0 } }
      ]
    }
  ],
  "relations": [
    { "many_collection": "articles", "many_field": "author_id", "one_collection": "users", "one_field": null, "junction_collection": null }
  ]
}
```

---

## Diff Format

```json
{
  "collections": {
    "create": [ { "collection": "new_col", "meta": { ... }, "fields": [ ... ] } ],
    "update": [ { "collection": "articles", "diff": { "label": "Changed" } } ],
    "delete": [ "old_col" ]
  },
  "fields": {
    "create": [ { "collection": "articles", "field": "slug", "type": "string", "meta": { ... } } ],
    "update": [ { "collection": "articles", "field": "title", "diff": { "meta": { "label": "Title" } } } ],
    "delete": [ { "collection": "articles", "field": "deprecated_field" } ]
  },
  "relations": {
    "create": [ { "many_collection": "articles", "many_field": "author_id", ... } ],
    "update": [],
    "delete": [ { "many_collection": "articles", "many_field": "old_field" } ]
  }
}
```

---

## POST /schema/apply

**Request body:**
```json
{ "snapshot": { ... }, "force": false }
```

**Response:**
```json
{ "data": { "applied": ["create_collection:articles", "create_field:articles.title"], "skipped": [] } }
```

- `force: false` — only creates and updates; deletions are skipped
- `force: true` — also deletes collections, fields, and relations absent from the snapshot
- Each operation string follows the pattern `{action}_{entity}:{identifier}`

**Error responses:**
- `422 VALIDATION_ERROR` — snapshot is null or fails constraint validation
- `422 SCHEMA_ERROR` — snapshot data causes an `InvalidArgumentException` in the handler

---

## Key Design Notes

- `SchemaDiffService` operates on plain arrays — no domain entity dependencies
- `SchemaSnapshotService` reads up to 10,000 rows per entity type
- `ApplySchemaHandler` catches `*AlreadyExistsException` per entity type and records them as skipped rather than failing the entire operation
- `SchemaController::apply()` uses `$this->container->get('serializer')` and `$this->container->get('validator')` — required pattern for PHP 8.3 where `AbstractController::$serializer` is null

---

## Dependencies

| Dependency                   | Module        | Purpose                                      |
|------------------------------|---------------|----------------------------------------------|
| `CollectionMetaRepositoryInterface` | Collections | Snapshot + apply collection operations |
| `FieldMetaRepositoryInterface`      | Fields      | Snapshot + apply field operations      |
| `RelationRepositoryInterface`       | Relations   | Snapshot + apply relation operations   |
| Collection/Field/Relation handlers  | All three   | Create/Update/Delete via CQRS commands |
