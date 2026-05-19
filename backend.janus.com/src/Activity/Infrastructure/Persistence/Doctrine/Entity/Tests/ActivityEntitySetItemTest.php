<?php

/**
 * @file ActivityEntitySetItemTest.php
 *
 * Tests for ActivityEntity::setItem() and ActivityEntity::item { get; set }.
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
 * Test class for ActivityEntity::setItem() — storing and retrieving the item.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setItem')]
final class ActivityEntitySetItemTest extends ActivityEntityTest
{

    /**
     * Test that setItem() stores the values.
     */
    public function testSetItemStoresValue(): void
    {
        $this->class->setItem(item: '99');

        $this->assertSame(expected: '99', actual: $this->class->item);
    }

    /**
     * Test that setItem() returns static instance
     */
    public function testSetItemReturnsStaticInstance(): void
    {
        $result = $this->class->setItem(item: '99');

        $this->assertSame(expected: $this->class, actual: $result);
    }

    /**
     * Test that setItem() accepts null value
     */
    public function testSetItemAcceptsNull(): void
    {
        $this->class->setItem(item: null);

        $this->assertNull(actual: $this->class->item);
    }
}
