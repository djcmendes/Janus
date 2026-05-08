<?php

/**
 * @file CollectionMetaMapperTest.php
 *
 * Abstract base for all CollectionMetaMapper test suites.
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use App\Collections\Infrastructure\Persistence\Doctrine\Mapper\CollectionMetaMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for CollectionMetaMapper tests.
 *
 * Strategy: CollectionMetaMapper, CollectionMeta, and CollectionMetaEntity are all
 * instantiated as real objects. All three classes are pure with no injectable
 * dependencies, so no mocking is required.
 */
#[CoversClass(CollectionMetaMapper::class)]
abstract class CollectionMetaMapperTest extends TestCase
{
    protected const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * @var CollectionMetaMapper
     */
    protected CollectionMetaMapper $class;

    /**
     * @var ReflectionClass<CollectionMetaMapper>
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
        $this->class      = new CollectionMetaMapper();
        $this->reflection = new ReflectionClass(CollectionMetaMapper::class);
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
     * @return CollectionMetaEntity A hydrated Doctrine model ready for toDomain() tests.
     */
    protected function makeEntity(): CollectionMetaEntity
    {
        return (new CollectionMetaEntity())
            ->setId(Uuid::fromString(self::FIXED_UUID))
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

    /**
     * Creates a fully-populated domain CollectionMeta with deterministic test values.
     *
     * @return CollectionMeta A hydrated domain entity ready for toPersistence() tests.
     */
    protected function makeDomain(): CollectionMeta
    {
        $collection = new CollectionMeta('articles');
        $collection->setLabel('Articles');
        $collection->setIcon('mdi-file-document');
        $collection->setNote('Main blog articles collection.');
        $collection->setHidden(false);
        $collection->setSingleton(false);
        $collection->setSortField('sort');

        return $collection;
    }
}
