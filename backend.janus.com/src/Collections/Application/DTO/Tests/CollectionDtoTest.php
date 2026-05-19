<?php

/**
 * @file CollectionDtoTest.php
 *
 * Abstract base for all CollectionDto test suites.
 *
 * @package App\Collections\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\DTO\Tests;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for CollectionDto tests.
 *
 * Strategy: CollectionDto is a final readonly class with no injectable dependencies.
 * Tests construct it via fromEntity() using a real CollectionMeta domain entity.
 */
#[CoversClass(className: CollectionDto::class)]
abstract class CollectionDtoTest extends TestCase
{
    /**
     * The DTO instance under test, built from a real CollectionMeta entity.
     * @var CollectionDto
     */
    protected CollectionDto $class;

    /**
     * Reflection of CollectionDto class.
     * @var ReflectionClass<CollectionDto>
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
        $this->class      = CollectionDto::fromEntity($this->makeCollectionMeta());
        $this->reflection = new ReflectionClass(CollectionDto::class);
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
     * @return CollectionMeta A hydrated entity ready for DTO conversion.
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
