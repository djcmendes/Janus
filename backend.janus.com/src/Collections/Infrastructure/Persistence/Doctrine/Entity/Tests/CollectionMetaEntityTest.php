<?php

/**
 * @file CollectionMetaEntityTest.php
 *
 * Abstract base for all CollectionMetaEntity test suites.
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for CollectionMetaEntity tests.
 *
 * Strategy: CollectionMetaEntity has no injectable dependencies. Tests instantiate
 * it directly — no mocking is required. The class is non-final (required for
 * Doctrine proxy generation), so a real instance is used as the SUT.
 */
#[CoversClass(CollectionMetaEntity::class)]
abstract class CollectionMetaEntityTest extends TestCase
{
    protected CollectionMetaEntity $class;

    /**
     * @var ReflectionClass<CollectionMetaEntity>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->class      = new CollectionMetaEntity();
        $this->reflection = new ReflectionClass(CollectionMetaEntity::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset(
            $this->class,
            $this->reflection
        );
    }

    /**
     * Creates a fully-populated CollectionMetaEntity with deterministic test values.
     *
     * @return CollectionMetaEntity A hydrated entity ready for assertion.
     */
    protected function makeEntity(): CollectionMetaEntity
    {
        return (new CollectionMetaEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setName('articles')
            ->setLabel('Articles')
            ->setIcon('mdi-file-document')
            ->setNote('Main blog articles collection.')
            ->setHidden(false)
            ->setSingleton(false)
            ->setSortField('sort')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(new \DateTimeImmutable('2024-06-01T12:00:00+00:00'));
    }
}
