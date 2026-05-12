# Janus — TODO

---

## Backend

### Exception Handling & Observability

#### [ ] Standardise uncaught exception responses
Currently, any exception that is not explicitly caught in a controller (e.g. a Doctrine
failure in `findPaginated()`) bypasses the project's JSON envelope and returns Symfony's
default error format, which is inconsistent with the `{ errors: [{ message, extensions: { code } }] }`
contract the frontend expects.

**Implementation:**
- Create `src/Shared/Presentation/EventSubscriber/ExceptionSubscriber.php`
- Listen on `KernelEvents::EXCEPTION`
- Map exception types to HTTP status codes and controlled error messages
- Always return the standard JSON envelope

#### [ ] Infrastructure exception logging to InfluxDB (separate channel)
Doctrine and other infrastructure exceptions should be routed to InfluxDB independently
of application-level logs so they can be monitored and alerted on separately.

**Implementation:**
1. Add `config/packages/monolog.yaml` with a dedicated `infrastructure` channel
2. Create `src/Shared/Log/InfluxDbHandler.php` — a custom Monolog handler that writes
   to InfluxDB using the Line Protocol
3. Register the handler as a Symfony service and wire it to the `infrastructure` channel
4. In `ExceptionSubscriber`, inject `LoggerInterface $infrastructureLogger`
   (autowired via `#[Autowire(service: 'monolog.logger.infrastructure')]`) and log
   infrastructure exceptions to that channel

**Files to create:**
```
src/Shared/Log/InfluxDbHandler.php
src/Shared/Presentation/EventSubscriber/ExceptionSubscriber.php
config/packages/monolog.yaml
```

---

## Prompt Compliance Tracker

Track which prompts have been applied to each backend module.
Copy the prompt text from the collapsible sections below to re-run any prompt.

| Module          | double-model | php-tests | php-docs | readme |
|-----------------|:------------:|:---------:|:--------:|:------:|
| Heimdall        | —            | [ ]       | [ ]      | [ ]    |
| Users           | —            | [ ]       | [ ]      | [ ]    |
| Settings        | —            | [ ]       | [ ]      | [ ]    |
| Activity        | —            | [x]       | [x]      | [ ]    |
| Server          | —            | [ ]       | [ ]      | [ ]    |
| Roles           | —            | [ ]       | [ ]      | [ ]    |
| Permissions     | —            | [ ]       | [ ]      | [ ]    |
| Collections     | [x]          | [x]       | [x]      | [x]    |
| Fields          | —            | [ ]       | [ ]      | [ ]    |
| Items           | —            | [ ]       | [ ]      | [ ]    |
| Relations       | —            | [ ]       | [ ]      | [ ]    |
| Files           | —            | [ ]       | [ ]      | [ ]    |
| Assets          | —            | [ ]       | [ ]      | [ ]    |
| Revisions       | —            | [ ]       | [ ]      | [ ]    |
| Comments        | [x]          | [x]       | [x]      | [x]    |
| Presets         | —            | [ ]       | [ ]      | [ ]    |
| Notifications   | —            | [ ]       | [ ]      | [ ]    |
| Shares          | —            | [ ]       | [ ]      | [ ]    |
| Dashboards      | [x]          | [x]       | [x]      | [x]    |
| Panels          | —            | [ ]       | [ ]      | [ ]    |
| Flows           | —            | [ ]       | [ ]      | [ ]    |
| Extensions      | [x]          | [x]       | [x]      | [x]    |
| Translations    | —            | [ ]       | [ ]      | [ ]    |
| Schema          | —            | [ ]       | [ ]      | [ ]    |
| Versions        | [x]          | [x]       | [x]      | [x]    |
| Deployments     | [x]          | [x]       | [x]      | [x]    |
| Utils           | —            | [ ]       | [ ]      | [ ]    |

> **Legend:** `[x]` = done · `[ ]` = pending · `—` = not applicable (no persistence layer / no double-model needed)

---

### Prompt: `generate_clean_architecture_double_model`

```
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
```

---

### Prompt: `generate_php_tests`

```
# Generate PHPUnit Test Suite

Generate a full PHPUnit test suite for the class at `{FILE_PATH}`.

## Context

- PHP 8.3, Symfony 7, PHPUnit 10+, Doctrine ORM 3.6+
- All production classes are `final` — **never mock them directly**
- Use **real instances backed by mocked dependencies** (interfaces and non-final Symfony/Doctrine classes)

---

## File structure

Create one abstract base file and one file per public method:

```
{DIR}/Tests/
  {ClassName}Test.php             ← abstract base (setUp, tearDown, factories, scenario builders)
  {ClassName}BaseTest.php         ← constructor / interface compliance
  {ClassName}{MethodName}Test.php ← one file per public method
```

---

## Abstract base — `{ClassName}Test.php`

- `abstract class {ClassName}Test extends TestCase`
- **File docblock**: `@file`, `@package`, `@author`
- **Class docblock**: strategy note explaining why real instances are used (final class policy)
- **Typed `MockObject` properties** with `@var MockObject&InterfaceName` PHPDoc
- **`$class`** — the real SUT (`protected {ClassName} $class`)
- **`$reflection`** — `ReflectionClass` for reading private/protected properties
- `setUp()` — wire all mocks into the real SUT; use `setContainer()` for Symfony controllers
- `tearDown()` — `unset()` all properties
- **`make{Entity}()`** factory with `@param` and `@return` PHPDoc, deterministic test values

### For Doctrine repositories

```php
// ClassMetadata — disableOriginalConstructor, then set name manually
$this->classMetadata = $this->getMockBuilder(ClassMetadata::class)
                            ->disableOriginalConstructor()
                            ->getMock();
$this->classMetadata->name = {Entity}::class;

// QueryBuilder fluent chain — configure ALL methods to return $this->queryBuilder
$this->queryBuilder = $this->createMock(QueryBuilder::class);
$this->queryBuilder->method('select')->willReturn($this->queryBuilder);
$this->queryBuilder->method('from')->willReturn($this->queryBuilder);
$this->queryBuilder->method('orderBy')->willReturn($this->queryBuilder);
$this->queryBuilder->method('setMaxResults')->willReturn($this->queryBuilder);
$this->queryBuilder->method('setFirstResult')->willReturn($this->queryBuilder);
$this->queryBuilder->method('andWhere')->willReturn($this->queryBuilder);
$this->queryBuilder->method('setParameter')->willReturn($this->queryBuilder);
$this->queryBuilder->method('getQuery')->willReturn($this->query);

// EntityManager
$this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
$this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);

// ManagerRegistry
$this->registry->method('getManagerForClass')->willReturn($this->entityManager);

// SUT
$this->class = new {ClassName}($this->registry);
```

### For Symfony controllers

```php
// Wire container for denyAccessUnlessGranted() + json()
$this->container->method('has')
                ->with('security.authorization_checker')
                ->willReturn(true);
$this->container->method('get')
                ->with('security.authorization_checker')
                ->willReturn($this->authorizationChecker);
$this->class->setContainer($this->container);
```

### Scenario builder helpers (for failure/alternate paths)

```php
/**
 * @return {ClassName} Controller wired for [scenario description].
 */
private function buildClassWith{Scenario}(): {ClassName}
{
    // construct fresh SUT with altered mock config
}
```

---

## `#[Covers*]` attributes — PHPUnit 10+ syntax, no docblock tags

```php
#[CoversClass({ClassName}::class)]                        // on every file
#[CoversMethod({ClassName}::class, 'methodName')]         // on per-method files
```

---

## DataProvider methods

```php
/**
 * @return array<string, array{
 *     paramA: TypeA,
 *     paramB: TypeB,
 *     expectedX: TypeX,
 * }>
 */
public static function {scenario}Provider(): array { ... }
```

Annotate with `#[DataProvider('{scenario}Provider')]` on the test method.

---

## Test method naming

`test{MethodName}{WhatItDoes}` — e.g. `testFindPaginatedSetsMaxResultsFromLimit`

---

## Per-method test file template

```php
#[CoversClass({ClassName}::class)]
#[CoversMethod({ClassName}::class, '{method}')]
final class {ClassName}{Method}Test extends {ClassName}Test
{
    // DataProvider (if needed) ─────────────────────────────────────

    // Happy path ───────────────────────────────────────────────────

    // Edge cases / branching ───────────────────────────────────────

    // Failure / exception paths ────────────────────────────────────
}
```

---

## What to cover per method

| Return type | Required cases |
|---|---|
| `array` (paginated) | non-empty result, empty result, limit forwarded, offset forwarded, no filters → no WHERE, each filter → correct WHERE+param, all filters combined |
| `int` (count) | correct integer, zero, string cast to int, no filters → no WHERE, each filter, all filters |
| `?Entity` (find) | found, null (not found), correct args forwarded |
| `void` (persist) | persist called with correct arg, flush called once, both in single call, return value is null |
| HTTP response | 2xx happy path, each DTO field present in JSON, guard/auth failure returns 4xx, not-found returns 404 with correct error code |

---

## Constants

```php
/** @var string */
private const string LOOKUP_UUID = '...';
```

---

## Style rules

- `declare(strict_types=1);` at top of every file
- `final class` for all test files (except the abstract base)
- No comments except docblocks
- One blank line between sections (Data Providers / Happy path / Edge cases / Failure paths)
- Use `$this->assertSame()` over `assertEquals()` for identity checks
- Use `$this->exactly(N)` for methods expected to be called a fixed number of times
- Use `$this->never()` when a method must not be called
```

---

### Prompt: `generate_php_docs`

```
# Generate PHP Documentation

Add PHPDoc blocks to every documentable element in `{FILE_PATH}`.

## What to document

| Element | Docblock tags required |
|---|---|
| File (top of file, before `declare`) | `@file`, `@package`, `@author` |
| Class / interface / trait | Description sentence, `@extends` / `@implements` if present |
| Class constant | `@var Type` inline above the constant |
| Constructor | `@param` for each argument |
| Public / protected method | Description sentence, `@param`, `@return`, `@throws` |
| Private method | Same as public if non-trivial; skip getters/setters that are self-evident |
| MockObject property (test files) | `@var MockObject&InterfaceName` |

---

## Format rules

### File docblock

```php
/**
 * @file FileName.php
 *
 * One sentence describing what this file contains or its role in the module.
 *
 * @package App\Module\SubNamespace
 * @author  David Mendes
 */
```

### Class docblock

```php
/**
 * One sentence describing the class responsibility.
 *
 * Add a second sentence only when there is a non-obvious behaviour worth
 * calling out (e.g. ordering guarantees, singleton pattern, caching).
 *
 * @extends  ParentClass<TypeParam>   ← only if generic parent
 * @implements InterfaceName          ← only if it adds clarity
 */
```

### Constant

```php
/** @var string */
private const string MY_CONST = 'value';
```

### Constructor

```php
/**
 * @param DependencyA $depA  Short description of what this dependency does.
 * @param DependencyB $depB  Short description.
 */
```

### Method

```php
/**
 * One sentence: what does this method do and what does it return.
 *
 * Add a second sentence only when there is a non-obvious side-effect,
 * precondition, or ordering constraint worth calling out.
 *
 * @param  Type   $name  What the caller should pass.
 * @param  Type   $name  What the caller should pass.
 *
 * @return Type  What the return value represents; use `void` if nothing.
 *
 * @throws ExceptionClass  When and why this exception is thrown.
 */
```

---

## Description style

- **One sentence per element** — if you need two, the second must add non-obvious information.
- Write what the element **does**, not what it **is**. Avoid restating the name.
  - Bad: `Returns the user ID.`
  - Good: `Returns the UUID of the authenticated user extracted from the JWT payload.`
- Use the **imperative mood**: "Persists…", "Returns…", "Validates…", "Throws…"
- For `@param`: describe what the caller should pass, not the type (the type hint already does that).
- For `@return`: describe what the value represents, not its type.
- For `@throws`: state the condition that triggers the exception, not just the class name.
- **Never** add `@param` or `@return` for `void` returns or zero-argument methods — omit the tag entirely.

---

## Alignment

Align `@param` values in a column when there are two or more parameters:

```php
 * @param string      $collection Filter by collection name, or null for all.
 * @param string|null $action     Filter by action type, or null for all.
 * @param int         $limit      Maximum number of records to return.
```

---

## What NOT to add

- Do not add `@author` to individual methods — only the file block.
- Do not add `@since`, `@version`, or `@deprecated` unless explicitly asked.
- Do not wrap existing logic in try/catch to support `@throws` — only document exceptions the current code actually throws or lets propagate.
- Do not reformat code outside the docblocks.
- Do not add comments inside method bodies.
```

---

### Prompt: `generate_module_readme`

```
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

Annotated tree of every subdirectory and its role.

### 3. REST Endpoints

Table of every HTTP route the module exposes, including auth requirement per endpoint.

### 4. Query Parameters (if applicable)

Table for endpoints that accept filter/pagination parameters.

### 5. Response Envelope

Show the exact JSON shape returned, including the `data` / `meta` / `errors` envelope.

### 6. Key Classes

Table of the most important classes and their roles (only non-trivial ones).

### 7. External Dependencies

Two sub-sections — internal modules and third-party packages.
List only dependencies the module **directly** imports.

---

## Style rules

- Use present tense: "Returns…", "Stores…", "Validates…"
- Keep descriptions to one sentence per row
- Do not duplicate information already obvious from the class or method name
- Do not add a "Contributing" or "License" section
- Do not add placeholders or TODOs — if something is not implemented, omit the section
```
