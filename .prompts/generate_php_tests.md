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
