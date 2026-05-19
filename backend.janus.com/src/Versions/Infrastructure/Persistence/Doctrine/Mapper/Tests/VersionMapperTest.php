<?php

/**
 * @file VersionMapperTest.php
 *
 * Abstract base for all VersionMapper test suites.
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use App\Versions\Infrastructure\Persistence\Doctrine\Mapper\VersionMapper;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for VersionMapper tests.
 *
 * Strategy: VersionMapper is a final pure service with no dependencies.
 * It is instantiated directly — no mocking is required.
 */
#[CoversClass(className: VersionMapper::class)]
abstract class VersionMapperTest extends TestCase
{
    /** @var string */
    protected const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * @var VersionMapper
     */
    protected VersionMapper $class;

    /**
     * @var ReflectionClass<VersionMapper>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new VersionMapper();
        $this->reflection = new ReflectionClass(VersionMapper::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated VersionEntity for toDomain() tests.
     *
     * @return VersionEntity A hydrated persistence model with all fields set.
     */
    protected function makeEntity(): VersionEntity
    {
        return (new VersionEntity())
            ->setId(Uuid::fromString(self::FIXED_UUID))
            ->setCollection('articles')
            ->setItem('item-uuid-1')
            ->setKey('main')
            ->setData(['title' => 'Hello'])
            ->setDelta(['title' => 'Hello'])
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setCreatedAt(new DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);
    }

    /**
     * Creates a fully-populated domain Version for toPersistence() tests.
     *
     * @return Version A hydrated domain entity with all fields set.
     */
    protected function makeDomain(): Version
    {
        return Version::reconstitute(
            id:         self::FIXED_UUID,
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       ['title' => 'Hello'],
            delta:      ['title' => 'Hello'],
            userId:     'bbbbbbbb-0000-7000-8000-000000000002',
            createdAt:  new DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            updatedAt:  null,
        );
    }
}
