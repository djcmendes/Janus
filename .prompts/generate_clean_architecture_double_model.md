# Generate Clean Architecture Double Model

Implement **Double Modeling** (strict Clean Architecture) for the `{Module}` resource.
Generate or update the three files below and then update all downstream code, tests, and docs.

---

## What to generate

### 1 — Pure Domain Entity  
**Path**: `src/{Module}/Domain/Entity/{Entity}.php`

- A plain PHP class with **zero framework or Doctrine dependencies**
- Constructor generates a new UUIDv7 string id and `\DateTimeImmutable` timestamp
- A `static reconstitute(...)` factory that accepts all fields (including id and timestamp) and bypasses the auto-generated values — used exclusively by the mapper when loading from persistence
- Private properties, public getters; fluent setters only for mutable fields (`userId`, `ip`, `userAgent`, etc.)
- `toArray(): array<string, string|null>` for JSON serialisation

```php
final class {Entity}
{
    private string $id;
    // ... other properties

    public function __construct(/* required fields only */)
    {
        $this->id        = (string) Uuid::v7();
        $this->timestamp = new \DateTimeImmutable();
        // ...
    }

    public static function reconstitute(
        string $id,
        // ... all fields
        \DateTimeImmutable $timestamp,
    ): self {
        $instance = new self(/* required fields */);
        $instance->id        = $id;      // override generated UUID
        $instance->timestamp = $timestamp;
        // ... override other fields
        return $instance;
    }

    public function getId(): string { return $this->id; }
    // ... other getters / fluent setters
}
```

### 2 — Infrastructure Doctrine Entity  
**Path**: `src/{Module}/Infrastructure/Persistence/Doctrine/Entity/{Entity}Entity.php`

- Holds **all** `#[ORM\Entity]`, `#[ORM\Table]`, `#[ORM\Column]` attributes
- Non-final (Doctrine may create proxy subclasses)
- No-arg construction (Doctrine hydrates via setters / reflection)
- Fluent setters, returning `static`
- `getId()` returns `Uuid` (Doctrine `uuid` column type)

### 3 — Data Mapper  
**Path**: `src/{Module}/Infrastructure/Persistence/Doctrine/Mapper/{Entity}Mapper.php`

- `final` class, no constructor dependencies
- `toDomain({Entity}Entity $entity): {Entity}` — calls `{Entity}::reconstitute(...)` with all fields
- `toPersistence({Entity} $domain): {Entity}Entity` — creates new `{Entity}Entity`, populates via setters

---

## Repository changes

Update `Infrastructure/Repository/{Entity}Repository.php`:

```php
public function __construct(
    ManagerRegistry              $registry,
    private readonly {Entity}Mapper $mapper,
) {
    parent::__construct($registry, {Entity}Entity::class);  // ← entity class changes
}

// record(): mapper->toPersistence($domain) → persist(ActivityEntity) → flush
// findById(): find($id) → mapper->toDomain($entity) ?? null
// findPaginated(): getResult() returns [{Entity}Entity] → array_map(mapper->toDomain(...), ...)
// countAll(): unchanged — count query, no mapping needed
```

---

## Test changes

### Abstract base (`{Entity}RepositoryTest.php`)
- Add `protected {Entity}Mapper $mapper` — use a **real instance** (it's pure, no dependencies)
- Update `$this->classMetadata->name = {Entity}Entity::class`
- Update SUT constructor: `new {Entity}Repository($this->registry, $this->mapper)`
- Add `makeEntityModel()` helper that returns a populated `{Entity}Entity` for use as query results

### RecordTest
- `persist()` now receives an `{Entity}Entity` — use `$this->isInstanceOf({Entity}Entity::class)` in assertions

### FindByIdTest
- `em->find()` is now called with `{Entity}Entity::class` as the first arg
- Return a real `{Entity}Entity` from `willReturn()`
- Assert the result is an instance of the **domain** `{Entity}` (the mapper converted it)

### FindPaginatedTest
- `query->getResult()` returns `[{Entity}Entity, ...]`
- Assert result items are instances of domain `{Entity}`, not `{Entity}Entity`

### New: `{Entity}EntityTest.php`
- Construction, all setters store and return static, getters return correct types

### New: `{Entity}MapperTest.php`
- `toDomain()`: all fields mapped correctly from entity → domain
- `toPersistence()`: all fields mapped correctly from domain → entity
- Roundtrip: `toDomain(toPersistence($domain))->getX() === $domain->getX()` for each field

---

## Docs changes

Update or add `@file`, class docblock, and method docs for all three new/modified files:
- Domain entity: note it is a pure POPO with no framework dependencies
- Infrastructure entity: note it owns all persistence concerns; link to domain entity
- Mapper: document both methods with `@param` and `@return`

---

## No migration needed

Moving `#[ORM\Entity]` from `{Entity}.php` to `{Entity}Entity.php` (same table name) produces no schema diff. Run `doctrine:migrations:diff` to confirm before committing.
