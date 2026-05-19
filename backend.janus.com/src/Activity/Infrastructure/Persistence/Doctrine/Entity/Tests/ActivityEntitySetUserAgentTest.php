<?php

/**
 * @file ActivityEntitySetUserAgentTest.php
 *
 * Tests for ActivityEntity::setUserAgent() and ActivityEntity::userAgent { get; set }.
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
 * Test class for ActivityEntity::setUserAgent() — storing and retrieving the userAgent.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setUserAgent')]
final class ActivityEntitySetUserAgentTest extends ActivityEntityTest
{
    /**
     * Test that setUserAgent() stores the values.
     */
    public function testSetUserAgentStoresValue(): void
    {
        $this->class->setUserAgent(userAgent: 'Mozilla/5.0');

        $this->assertSame(expected: 'Mozilla/5.0', actual: $this->class->userAgent);
    }

    /**
     * Test that setUserAgent() returns static instance
     */
    public function testSetUserAgentReturnsStaticInstance(): void
    {
        $result = $this->class->setUserAgent(userAgent: 'Mozilla/5.0');

        $this->assertSame(expected: $this->class, actual: $result);
    }

    /**
     * Test that setUserAgent() accepts null value
     */
    public function testSetUserAgentAcceptsNull(): void
    {
        $this->class->setUserAgent(userAgent: null);

        $this->assertNull(actual: $this->class->userAgent);
    }
}
