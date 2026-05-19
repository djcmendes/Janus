<?php

/**
 * @file ActivitySetIpTest.php
 *
 * Tests for Activity::setIp().
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
 * Tests for Activity::setIp() — storing and retrieving the remote IP address.
 */
#[CoversClass(className:  Activity::class)]
#[CoversMethod(className: Activity::class, methodName: 'setIp')]
final class ActivitySetIpTest extends ActivityTest
{
    /**
     * Test that setIp() stores the provided IP address.
     */
    public function testSetIpStoresValue(): void
    {
        $this->class->setIp('127.0.0.1');

        $this->assertSame('127.0.0.1', $this->class->getIp());
    }

    /**
     * Test that setIp() returns the same Activity instance for fluent chaining.
     */
    public function testSetIpReturnsStaticInstance(): void
    {
        $result = $this->class->setIp('127.0.0.1');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setIp() accepts null to clear the IP address.
     */
    public function testSetIpAcceptsNull(): void
    {
        $this->class->setIp(null);

        $this->assertNull($this->class->getIp());
    }
}
