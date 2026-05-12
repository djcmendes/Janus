<?php

/**
 * @file VersionEntitySetItemTest.php
 *
 * Tests for VersionEntity::setItem().
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that setItem() stores the value and returns static.
 */
#[CoversClass(VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setItem')]
final class VersionEntitySetItemTest extends VersionEntityTest
{
    public function testSetItemStoresValue(): void
    {
        $this->class->setItem('new-item-uuid');
        $this->assertSame('new-item-uuid', $this->class->getItem());
    }

    public function testSetItemReturnsStatic(): void
    {
        $result = $this->class->setItem('x');
        $this->assertSame($this->class, $result);
    }
}
