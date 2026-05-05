<?php

/**
 * @file ActivityEntitySetItemTest.php
 *
 * Tests for ActivityEntity::setItem() and ActivityEntity::getItem().
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
#[CoversMethod(ActivityEntity::class, 'setItem')]
final class ActivityEntitySetItemTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetItemStoresValue(): void
    {
        $this->class->setItem('99');

        $this->assertSame('99', $this->class->getItem());
    }

    public function testSetItemReturnsStaticInstance(): void
    {
        $result = $this->class->setItem('99');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetItemAcceptsNull(): void
    {
        $this->class->setItem(null);

        $this->assertNull($this->class->getItem());
    }
}
