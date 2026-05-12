<?php

/**
 * @file VersionEntityTest.php
 *
 * Abstract base for all VersionEntity test suites.
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for VersionEntity tests.
 *
 * Strategy: VersionEntity is non-final (Doctrine proxy requirement) but has
 * no injectable dependencies. Tests instantiate it directly and use fluent setters.
 */
#[CoversClass(VersionEntity::class)]
abstract class VersionEntityTest extends TestCase
{
    /**
     * @var VersionEntity
     */
    protected VersionEntity $class;

    /**
     * @var ReflectionClass<VersionEntity>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class = (new VersionEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setCollection('articles')
            ->setItem('item-uuid-1')
            ->setKey('main')
            ->setData(['title' => 'Hello'])
            ->setDelta(null)
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setCreatedAt(new DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);

        $this->reflection = new ReflectionClass(VersionEntity::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated VersionEntity with deterministic values.
     *
     * @return VersionEntity A hydrated persistence model ready for mapper tests.
     */
    protected function makeEntity(): VersionEntity
    {
        return (new VersionEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setCollection('articles')
            ->setItem('item-uuid-1')
            ->setKey('main')
            ->setData(['title' => 'Hello'])
            ->setDelta(['title' => 'Hello'])
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setCreatedAt(new DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);
    }
}
