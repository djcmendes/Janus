<?php

/**
 * @file ActivitySetUserIdTest.php
 *
 * Tests for Activity::setUserId().
 *
 * @package App\Activity\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Entity\Tests;

use App\Activity\Domain\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for Activity::setUserId() — storing and retrieving the acting user's UUID.
 */
#[CoversClass(className:  Activity::class)]
#[CoversMethod(className: Activity::class, methodName: 'setUserId')]
final class ActivitySetUserIdTest extends ActivityTest
{
    /**
     * Test that setUserId() stores the provided user UUID.
     */
    public function testSetUserIdStoresValue(): void
    {
        $this->class->setUserId('user-uuid');

        $this->assertSame('user-uuid', $this->class->userId);
    }

    /**
     * Test that setUserId() returns the same Activity instance for fluent chaining.
     */
    public function testSetUserIdReturnsStaticInstance(): void
    {
        $result = $this->class->setUserId('user-uuid');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setUserId() accepts null to clear the user ID.
     */
    public function testSetUserIdAcceptsNull(): void
    {
        $this->class->setUserId(null);

        $this->assertNull($this->class->userId);
    }
}
