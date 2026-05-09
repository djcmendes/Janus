# Comments

Manages user-authored comments attached to items in any collection, with ownership-based write access control.

---

## Folder Structure

```
Comments/
  Domain/
    Entity/       ← Pure Comment POPO — no framework dependencies
    Repository/   ← CommentRepositoryInterface (domain contract)
    Exception/    ← CommentNotFoundException, CommentForbiddenException
  Application/
    Command/      ← CreateCommentCommand, UpdateCommentCommand, DeleteCommentCommand
    Command/Handler/ ← CreateCommentHandler, UpdateCommentHandler, DeleteCommentHandler
    Query/        ← GetCommentsQuery, GetCommentByIdQuery
    Query/Handler/ ← GetCommentsHandler, GetCommentByIdHandler
    DTO/          ← CommentDto (request/response shape)
  Infrastructure/
    Persistence/
      Doctrine/
        Entity/   ← CommentEntity (Doctrine ORM mapping for `comments` table)
        Mapper/   ← CommentMapper (toDomain / toPersistence)
    Repository/   ← CommentRepository (Doctrine implementation)
  Presentation/
    Controller/   ← CommentsController (thin HTTP layer)
    DTO/          ← CreateCommentRequest, UpdateCommentRequest
```

---

## REST Endpoints

| Method   | Path              | Auth          | Description                                      |
|----------|-------------------|---------------|--------------------------------------------------|
| `GET`    | `/comments`       | Authenticated | Returns a paginated list of comments             |
| `GET`    | `/comments/{id}`  | Authenticated | Returns a single comment by UUID                 |
| `POST`   | `/comments`       | Authenticated | Creates a new comment; userId taken from JWT     |
| `PATCH`  | `/comments/{id}`  | Authenticated | Updates comment text; owner or ROLE_ADMIN only   |
| `DELETE` | `/comments/{id}`  | Authenticated | Deletes a comment; owner or ROLE_ADMIN only      |

---

## Query Parameters

The `GET /comments` endpoint accepts the following optional filters:

| Parameter    | Type     | Default | Description                                  |
|--------------|----------|---------|----------------------------------------------|
| `limit`      | `int`    | `25`    | Maximum number of records per page           |
| `offset`     | `int`    | `0`     | Pagination offset                            |
| `collection` | `string` | —       | Filter to comments on a specific collection  |
| `item`       | `string` | —       | Filter to comments on a specific item id     |

---

## Response Envelope

**Collection (`GET /comments`):**

```json
{
  "data": [
    {
      "id": "uuid",
      "collection": "posts",
      "item": "42",
      "comment": "Great post!",
      "user": "user-uuid",
      "created_at": "2024-01-01T00:00:00+00:00",
      "updated_at": null
    }
  ],
  "meta": {
    "total_count": 100,
    "filter_count": 100
  }
}
```

**Single item (`GET /comments/{id}`, `POST /comments`, `PATCH /comments/{id}`):**

```json
{
  "data": {
    "id": "uuid",
    "collection": "posts",
    "item": "42",
    "comment": "Great post!",
    "user": "user-uuid",
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": null
  }
}
```

**Error:**

```json
{
  "errors": [
    { "message": "Comment \"uuid\" not found.", "extensions": { "code": "NOT_FOUND" } }
  ]
}
```

---

## Key Classes

| Class                    | File                                                         | Role                                                        |
|--------------------------|--------------------------------------------------------------|-------------------------------------------------------------|
| `Comment`                | `Domain/Entity/Comment.php`                                  | Pure domain entity; owns `isOwnedBy()` ownership check      |
| `CommentEntity`          | `Infrastructure/Persistence/Doctrine/Entity/CommentEntity.php` | Doctrine ORM model; sole owner of `#[ORM\*]` attributes   |
| `CommentMapper`          | `Infrastructure/Persistence/Doctrine/Mapper/CommentMapper.php` | Converts between domain Comment and Doctrine CommentEntity  |
| `CommentRepository`      | `Infrastructure/Repository/CommentRepository.php`            | Doctrine implementation; uses mapper for all reads/writes   |
| `CommentForbiddenException` | `Domain/Exception/CommentForbiddenException.php`          | Thrown when a non-owner non-admin attempts a write          |

---

## External Dependencies

### Internal modules

| Module   | Class / Service used | Why                                          |
|----------|----------------------|----------------------------------------------|
| Heimdall | `RequestGuard`       | Authentication, client authorisation, and user-id extraction |

### Third-party packages

| Package                     | Used via                      | Why                              |
|-----------------------------|-------------------------------|----------------------------------|
| Symfony HttpFoundation      | `JsonResponse`, `Request`     | HTTP request/response handling   |
| Doctrine ORM                | `ServiceEntityRepository`     | Database persistence             |
| Symfony UID                 | `Uuid`                        | UUID generation and parsing      |
