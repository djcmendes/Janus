<?php

/**
 * @file VersionEntityBaseTest.php
 *
 * Tests for VersionEntity construction and getter correctness.
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

/**
 * Verifies that VersionEntity stores all fields correctly after population via setters.
 */
#[CoversClass(className: VersionEntity::class)]
final class VersionEntityBaseTest extends VersionEntityTest
{
    public function testGetIdReturnsUuid(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->class->getId());
    }

    public function testGetCollectionReturnsStoredValue(): void
    {
        $this->assertSame('articles', $this->class->getCollection());
    }

    public function testGetItemReturnsStoredValue(): void
    {
        $this->assertSame('item-uuid-1', $this->class->getItem());
    }

    public function testGetKeyReturnsStoredValue(): void
    {
        $this->assertSame('main', $this->class->getKey());
    }

    public function testGetDataReturnsStoredArray(): void
    {
        $this->assertSame(['title' => 'Hello'], $this->class->getData());
    }

    public function testGetDeltaReturnsNullWhenUnset(): void
    {
        $this->assertNull($this->class->getDelta());
    }

    public function testGetUserIdReturnsStoredValue(): void
    {
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $this->class->getUserId());
    }

    public function testGetCreatedAtReturnsDateTimeImmutable(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getCreatedAt());
    }

    public function testGetUpdatedAtReturnsNullWhenUnset(): void
    {
        $this->assertNull($this->class->getUpdatedAt());
    }
}
