<?php

/**
 * @file VersionDtoTest.php
 *
 * Abstract base for all VersionDto test suites.
 *
 * @package App\Versions\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\DTO\Tests;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Entity\Version;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for VersionDto tests.
 *
 * Strategy: VersionDto is final with no injectable dependencies.
 * Tests instantiate it via fromEntity() using a real Version domain entity.
 */
#[CoversClass(className: VersionDto::class)]
abstract class VersionDtoTest extends TestCase
{
    /**
     * @var VersionDto
     */
    protected VersionDto $class;

    /**
     * @var ReflectionClass<VersionDto>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = VersionDto::fromEntity($this->makeVersion());
        $this->reflection = new ReflectionClass(VersionDto::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated Version entity for DTO conversion tests.
     *
     * @return Version A hydrated entity with all fields set to deterministic values.
     */
    protected function makeVersion(): Version
    {
        return new Version(
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       ['title' => 'Hello'],
            delta:      ['title' => 'Hello'],
            userId:     'bbbbbbbb-0000-7000-8000-000000000002',
        );
    }
}
