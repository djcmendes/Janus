<?php

/**
 * @file ActivityEntitySetUserIdTest.php
 *
 * Tests for ActivityEntity::setUserId() and ActivityEntity::setUserId { get; set }.
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
 * Test class for ActivityEntity::setUserId() — storing and retrieving the userId.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setUserId')]
final class ActivityEntitySetUserIdTest extends ActivityEntityTest
{
    /**
     * Test that setUserId() stores the values.
     */
    public function testSetUserIdStoresValue(): void
    {
        $this->class->setUserId(userId: 'user-uuid');

        $this->assertSame(expected: 'user-uuid', actual: $this->class->userId);
    }

    /**
     * Test that setUserId() returns static instance
     */
    public function testSetUserIdReturnsStaticInstance(): void
    {
        $result = $this->class->setUserId(userId: 'user-uuid');

        $this->assertSame(expected: $this->class, actual: $result);
    }

    /**
     * Test that setUserId() accepts null value
     */
    public function testSetUserIdAcceptsNull(): void
    {
        $this->class->setUserId(userId: null);

        $this->assertNull(actual: $this->class->userId);
    }
}
