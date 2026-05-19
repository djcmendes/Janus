<?php

/**
 * @file ActivityEntitySetIpTest.php
 *
 * Tests for ActivityEntity::setIp() and ActivityEntity::ip { get; set }.
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
 * Test class for ActivityEntity::setIp() — storing and retrieving the ip.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setIp')]
final class ActivityEntitySetIpTest extends ActivityEntityTest
{
    /**
     * Test that setIp() stores the values.
     */
    public function testSetIpStoresValue(): void
    {
        $this->class->setIp(ip: '10.0.0.1');

        $this->assertSame(expected: '10.0.0.1', actual: $this->class->ip);
    }

    /**
     * Test that setIp() returns static instance
     */
    public function testSetIpReturnsStaticInstance(): void
    {
        $result = $this->class->setIp(ip: '10.0.0.1');

        $this->assertSame(expected: $this->class, actual: $result);
    }

    /**
     * Test that setIp() accepts null value
     */
    public function testSetIpAcceptsNull(): void
    {
        $this->class->setIp(ip: null);

        $this->assertNull(actual: $this->class->ip);
    }
}
