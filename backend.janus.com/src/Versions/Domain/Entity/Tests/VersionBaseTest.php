<?php

/**
 * @file VersionBaseTest.php
 *
 * Tests for Version construction and initial state.
 *
 * @package App\Versions\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Entity\Tests;

use App\Versions\Domain\Entity\Version;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that Version is constructed correctly with generated UUID, timestamps, and all fields set.
 */
#[CoversClass(Version::class)]
final class VersionBaseTest extends VersionTest
{
    /**
     * Test that getId() returns a non-empty UUID string after construction.
     */
    public function testGetIdReturnsNonEmptyUuid(): void
    {
        $this->assertNotEmpty($this->class->getId());
        $this->assertIsString($this->class->getId());
    }

    /**
     * Test that getCollection() returns the collection passed to the constructor.
     */
    public function testGetCollectionReturnsConstructorValue(): void
    {
        $this->assertSame('articles', $this->class->getCollection());
    }

    /**
     * Test that getItem() returns the item identifier passed to the constructor.
     */
    public function testGetItemReturnsConstructorValue(): void
    {
        $this->assertSame('item-uuid-1', $this->class->getItem());
    }

    /**
     * Test that getKey() returns the version label passed to the constructor.
     */
    public function testGetKeyReturnsConstructorValue(): void
    {
        $this->assertSame('main', $this->class->getKey());
    }

    /**
     * Test that getData() returns the data array passed to the constructor.
     */
    public function testGetDataReturnsConstructorValue(): void
    {
        $this->assertSame(['title' => 'Hello'], $this->class->getData());
    }

    /**
     * Test that getDelta() returns null when not provided to the constructor.
     */
    public function testGetDeltaReturnsNullByDefault(): void
    {
        $this->assertNull($this->class->getDelta());
    }

    /**
     * Test that getUserId() returns null when not provided to the constructor.
     */
    public function testGetUserIdReturnsNullByDefault(): void
    {
        $this->assertNull($this->class->getUserId());
    }

    /**
     * Test that getCreatedAt() returns a DateTimeImmutable after construction.
     */
    public function testGetCreatedAtReturnsDateTimeImmutable(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getCreatedAt());
    }

    /**
     * Test that getUpdatedAt() returns null immediately after construction.
     */
    public function testGetUpdatedAtReturnsNullAfterConstruction(): void
    {
        $this->assertNull($this->class->getUpdatedAt());
    }

    /**
     * Test that two Version instances constructed with the same arguments receive different UUIDs.
     */
    public function testConstructorGeneratesUniqueIds(): void
    {
        $a = new Version('articles', 'item-1', 'main', []);
        $b = new Version('articles', 'item-1', 'main', []);

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
