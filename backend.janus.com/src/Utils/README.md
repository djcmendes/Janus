# Utils Module

Provides miscellaneous administrative utility endpoints.

---

## Directory Structure

```
Utils/
  Presentation/
    Controller/
      UtilsController.php           ← HTTP controller (sort, hash, cache, random)
      Tests/
        UtilsControllerTest.php     ← Abstract base
        UtilsControllerBaseTest.php
        UtilsControllerSortTest.php
        UtilsControllerHashGenerateTest.php
        UtilsControllerHashVerifyTest.php
        UtilsControllerCacheClearTest.php
        UtilsControllerRandomStringTest.php
```

---

## Endpoints

All endpoints require **ROLE_ADMIN** and support WEB + CLI clients.

| Method | Path                       | Description                                              |
|--------|----------------------------|----------------------------------------------------------|
| POST   | /utils/sort/{collection}   | Reorder items using the collection's sort field          |
| GET    | /utils/hash/generate       | Generate a bcrypt hash of a value                        |
| GET    | /utils/hash/verify         | Verify a plaintext value against a bcrypt hash           |
| POST   | /utils/cache/clear         | Flush the application cache pool                         |
| GET    | /utils/random/string       | Generate a cryptographically secure random string        |

---

## Endpoint Details

### POST /utils/sort/{collection}

Reorders items in the named collection by updating each item's sort field.

**Request body:**
```json
{ "items": [{ "id": "uuid", "sort": 1 }, { "id": "uuid2", "sort": 2 }] }
```

**Responses:**
- `200` — `{ "data": { "updated": 2 } }`
- `404 NOT_FOUND` — Collection does not exist
- `422 NO_SORT_FIELD` — Collection has no sort field configured
- `422 VALIDATION_ERROR` — `items` key missing or empty

---

### GET /utils/hash/generate?value=plaintext

Returns a bcrypt hash of the provided value.

**Responses:**
- `200` — `{ "data": { "hash": "$2y$..." } }`
- `422 VALIDATION_ERROR` — `value` query parameter is empty

---

### GET /utils/hash/verify?value=plaintext&hash=$2y$...

Verifies a plaintext value against a bcrypt hash.

**Responses:**
- `200` — `{ "data": { "valid": true } }` or `{ "data": { "valid": false } }`
- `422 VALIDATION_ERROR` — `value` or `hash` query parameter is empty

---

### POST /utils/cache/clear

Flushes the Symfony cache pool injected as `CacheInterface`.

**Response:**
- `200` — `{ "data": { "cleared": true } }`

---

### GET /utils/random/string?length=32&charset=ABC...

Returns a cryptographically secure random string using `random_int()`.

| Query param | Default | Constraint              |
|-------------|---------|-------------------------|
| `length`    | 32      | Clamped to `[1, 256]`   |
| `charset`   | `A-Za-z0-9` | Any non-empty string |

**Response:**
- `200` — `{ "data": { "random": "xG7..." } }`

---

## Authorization

All five actions call:
```php
$this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
$this->guard->authorize(Client::WEB, Client::CLI);
$this->denyAccessUnlessGranted('ROLE_ADMIN');
```

Unauthenticated callers receive `UnauthorizedException`.  
Authenticated non-admin callers receive `AccessDeniedException`.

---

## Dependencies

| Dependency                        | How injected         | Purpose                         |
|-----------------------------------|----------------------|---------------------------------|
| `RequestGuard`                    | Constructor          | Auth / client validation        |
| `Connection`                      | Action parameter     | SQL UPDATE for sort             |
| `CollectionMetaRepositoryInterface` | Action parameter   | Looks up collection and sort field |
| `CacheInterface`                  | Action parameter     | Cache pool to clear             |
