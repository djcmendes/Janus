<?php

/**
 * @file VersionEntitySetUserIdTest.php
 *
 * Tests for VersionEntity::setUserId().
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
 * Verifies that setUserId() stores the value, handles null, and returns static.
 */
#[CoversClass(VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setUserId')]
final class VersionEntitySetUserIdTest extends VersionEntityTest
{
    public function testSetUserIdStoresValue(): void
    {
        $this->class->setUserId('cccccccc-0000-7000-8000-000000000003');
        $this->assertSame('cccccccc-0000-7000-8000-000000000003', $this->class->getUserId());
    }

    public function testSetUserIdWithNullClearsValue(): void
    {
        $this->class->setUserId(null);
        $this->assertNull($this->class->getUserId());
    }

    public function testSetUserIdReturnsStatic(): void
    {
        $result = $this->class->setUserId(null);
        $this->assertSame($this->class, $result);
    }
}
