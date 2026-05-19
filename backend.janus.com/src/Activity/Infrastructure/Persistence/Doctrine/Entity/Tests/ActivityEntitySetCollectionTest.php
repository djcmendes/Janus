<?php

/**
 * @file ActivityEntitySetCollectionTest.php
 *
 * Tests for ActivityEntity::setCollection() and ActivityEntity::collection { get; set }.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Test class for ActivityEntity::setCollection() — storing and retrieving the collection.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setCollection')]
final class ActivityEntitySetCollectionTest extends ActivityEntityTest
{
    /**
     * Test that setCollection() stores collection value
     */
    public function testSetCollectionStoresValue(): void
    {
        $this->class->setCollection(collection: 'articles');

        $this->assertSame(expected: 'articles', actual: $this->class->collection);
    }

    /**
     * Test that setCollection() returns static instance
     */
    public function testSetCollectionReturnsStaticInstance(): void
    {
        $result = $this->class->setCollection(collection: 'articles');

        $this->assertSame(expected: $this->class, actual: $result);
    }

    /**
     * Test that setCollection() accepts null value
     */
    public function testSetCollectionAcceptsNull(): void
    {
        $this->class->setCollection(collection: null);

        $this->assertNull(actual: $this->class->collection);
    }
}
