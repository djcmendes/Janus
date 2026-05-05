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
