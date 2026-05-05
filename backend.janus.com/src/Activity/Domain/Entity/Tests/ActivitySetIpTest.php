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

#[CoversClass(Activity::class)]
#[CoversMethod(Activity::class, 'setIp')]
final class ActivitySetIpTest extends ActivityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetIpStoresValue(): void
    {
        $this->class->setIp('127.0.0.1');

        $this->assertSame('127.0.0.1', $this->class->getIp());
    }

    public function testSetIpReturnsStaticInstance(): void
    {
        $result = $this->class->setIp('127.0.0.1');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetIpAcceptsNull(): void
    {
        $this->class->setIp(null);

        $this->assertNull($this->class->getIp());
    }
}
