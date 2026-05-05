<?php

/**
 * @file ActivityEntitySetIpTest.php
 *
 * Tests for ActivityEntity::setIp() and ActivityEntity::getIp().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(ActivityEntity::class)]
#[CoversMethod(ActivityEntity::class, 'setIp')]
final class ActivityEntitySetIpTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetIpStoresValue(): void
    {
        $this->class->setIp('10.0.0.1');

        $this->assertSame('10.0.0.1', $this->class->getIp());
    }

    public function testSetIpReturnsStaticInstance(): void
    {
        $result = $this->class->setIp('10.0.0.1');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetIpAcceptsNull(): void
    {
        $this->class->setIp(null);

        $this->assertNull($this->class->getIp());
    }
}
