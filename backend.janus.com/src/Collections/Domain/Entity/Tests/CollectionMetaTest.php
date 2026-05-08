<?php

/**
 * @file CollectionMetaTest.php
 *
 * Abstract base for all CollectionMeta domain entity test suites.
 *
 * @package App\Collections\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Entity\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for CollectionMeta domain entity tests.
 *
 * Strategy: CollectionMeta is a final class with no injectable dependencies.
 * Tests instantiate it directly — no mocking is required.
 */
#[CoversClass(CollectionMeta::class)]
abstract class CollectionMetaTest extends TestCase
{
    protected CollectionMeta $class;

    /**
     * @var ReflectionClass<CollectionMeta>
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
        $this->class      = new CollectionMeta('articles');
        $this->reflection = new ReflectionClass(CollectionMeta::class);
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
     * Creates a fully-populated CollectionMeta entity with deterministic test values.
     *
     * @return CollectionMeta A hydrated entity with all optional fields set.
     */
    protected function makeCollectionMeta(): CollectionMeta
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
