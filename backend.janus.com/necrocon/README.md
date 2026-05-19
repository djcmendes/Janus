# Projeto NECROCON

**(The Necromantic Code Quality Engine)**

NECROCON is Janus's portable code quality module. It centralises all static analysis, formatting, and architecture enforcement rules in one directory so the same standards can be imported into any PHP project with zero duplication.

---

## What It Does

| Sub-module | Tool | Purpose |
|---|---|---|
| `php-cs-fixer/` | PHP CS Fixer 3.x | PSR-12 formatting + PHP 8.x style rules |
| `phpcs/` | PHP_CodeSniffer 4.x | PSR-12 sniff-based validation |
| `phpmd/` | PHP Mess Detector 2.x | Complexity, coupling, naming, unused code |
| `phparkitect/` | PHPArkitect 1.x | DDD/Onion layer boundary enforcement |

---

## How to Run (inside the Docker container)

```bash
# Format all files
composer run cs-fix

# Check formatting only (no writes)
composer run cs-check

# Check with PHP_CodeSniffer
composer run phpcs

# Run PHPStan static analysis (level 8)
composer run phpstan

# Run PHP Mess Detector
composer run phpmd

# Find duplicate code blocks
composer run phpcpd

# Detect magic numbers
composer run phpmnd

# Check architecture rules
composer run phparkitect

# Run composer security audit
composer run audit

# Run ALL checks at once
composer run quality
```

Or via Makefile from the project root:

```bash
make cs-check
make cs-fix
make phpcs
make phpstan
make phpmd
make phpcpd
make phpmnd
make arkitect
make audit
make quality
```

---

## How to Reuse in Another Project

1. Copy the entire `necrocon/` directory into your project root.
2. In your `.php-cs-fixer.dist.php`:
   ```php
   require_once __DIR__ . '/necrocon/php-cs-fixer/JanusRules.php';
   return (new PhpCsFixer\Config())
       ->setRules(JanusRules::rules())
       ->setFinder(JanusRules::finder(__DIR__));
   ```
3. In your `.phpcs.xml`:
   ```xml
   <ruleset name="YourProject">
       <rule ref="necrocon/phpcs/ruleset.xml"/>
       <file>src/</file>
   </ruleset>
   ```
4. Copy `phpstan.neon`, `grumphp.yml`, and the `composer.json` scripts block.
5. Adjust paths and namespaces in `necrocon/phparkitect/config.php` to match your module structure.

---

## Pre-commit Hooks (GrumPHP)

GrumPHP wires all checks to run automatically before every `git commit`. All checks are **read-only** — they report problems but never auto-modify files.

```bash
# Install hooks after cloning
./vendor/bin/grumphp git:init
```

To bypass temporarily (not recommended):
```bash
git commit --no-verify -m "WIP: ..."
```
