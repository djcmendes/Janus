<?php

/**
 * @file ActivitySetUserAgentTest.php
 *
 * Tests for Activity::setUserAgent().
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
 * Tests for Activity::setUserAgent() — storing and retrieving the HTTP user-agent string.
 */
#[CoversClass(className:  Activity::class)]
#[CoversMethod(className: Activity::class, methodName: 'setUserAgent')]
final class ActivitySetUserAgentTest extends ActivityTest
{
    /**
     * Test that setUserAgent() stores the provided user-agent string.
     */
    public function testSetUserAgentStoresValue(): void
    {
        $this->class->setUserAgent('PHPUnit/10');

        $this->assertSame('PHPUnit/10', $this->class->getUserAgent());
    }

    /**
     * Test that setUserAgent() returns the same Activity instance for fluent chaining.
     */
    public function testSetUserAgentReturnsStaticInstance(): void
    {
        $result = $this->class->setUserAgent('PHPUnit/10');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setUserAgent() accepts null to clear the user-agent.
     */
    public function testSetUserAgentAcceptsNull(): void
    {
        $this->class->setUserAgent(null);

        $this->assertNull($this->class->getUserAgent());
    }
}
