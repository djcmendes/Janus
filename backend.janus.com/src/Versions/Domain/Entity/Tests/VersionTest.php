<?php

/**
 * @file VersionTest.php
 *
 * Abstract base for all Version domain entity test suites.
 *
 * @package App\Versions\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Entity\Tests;

use App\Versions\Domain\Entity\Version;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for Version domain entity tests.
 *
 * Strategy: Version is a final class with no injectable dependencies.
 * Tests instantiate it directly — no mocking is required.
 */
#[CoversClass(className: Version::class)]
abstract class VersionTest extends TestCase
{
    /**
     * Instance of the class being tested.
     * @var Version
     */
    protected Version $class;

    /**
     * Reflection of Version class.
     * @var ReflectionClass<Version>
     */
    protected ReflectionClass $reflection;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->class      = new Version('articles', 'item-uuid-1', 'main', ['title' => 'Hello']);
        $this->reflection = new ReflectionClass(Version::class);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated Version entity with deterministic test values.
     *
     * @return Version A hydrated entity with all optional fields set.
     */
    protected function makeVersion(): Version
    {
        return new Version(
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       ['title' => 'Hello', 'body' => 'World'],
            delta:      ['title' => 'Hello'],
            userId:     'bbbbbbbb-0000-7000-8000-000000000002',
        );
    }

    /**
     * Creates a reconstituted Version with all fields set to deterministic test values.
     *
     * @return Version A hydrated entity via reconstitute() for mapper / persistence tests.
     */
    protected function makeReconstitutedVersion(): Version
    {
        return Version::reconstitute(
            id:         'aaaaaaaa-0000-7000-8000-000000000001',
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       ['title' => 'Hello'],
            delta:      null,
            userId:     'bbbbbbbb-0000-7000-8000-000000000002',
            createdAt:  new DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            updatedAt:  null,
        );
    }
}
