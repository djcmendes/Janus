<?php

/**
 * @file VersionDtoBaseTest.php
 *
 * Tests for VersionDto construction and property access.
 *
 * @package App\Versions\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\DTO\Tests;

use App\Versions\Application\DTO\VersionDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that VersionDto is correctly constructed and exposes all readonly properties.
 */
#[CoversClass(className: VersionDto::class)]
final class VersionDtoBaseTest extends VersionDtoTest
{
    /**
     * Test that the id property is a non-empty string.
     */
    public function testIdIsNonEmptyString(): void
    {
        $this->assertNotEmpty($this->class->id);
        $this->assertIsString($this->class->id);
    }

    /**
     * Test that the collection property matches the entity value.
     */
    public function testCollectionIsSet(): void
    {
        $this->assertSame('articles', $this->class->collection);
    }

    /**
     * Test that the item property matches the entity value.
     */
    public function testItemIsSet(): void
    {
        $this->assertSame('item-uuid-1', $this->class->item);
    }

    /**
     * Test that the key property matches the entity value.
     */
    public function testKeyIsSet(): void
    {
        $this->assertSame('main', $this->class->key);
    }

    /**
     * Test that the data property matches the entity data array.
     */
    public function testDataIsSet(): void
    {
        $this->assertSame(['title' => 'Hello'], $this->class->data);
    }

    /**
     * Test that the userId property matches the entity value.
     */
    public function testUserIdIsSet(): void
    {
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $this->class->userId);
    }

    /**
     * Test that the createdAt property is a non-empty string.
     */
    public function testCreatedAtIsNonEmptyString(): void
    {
        $this->assertNotEmpty($this->class->createdAt);
    }

    /**
     * Test that the updatedAt property is null when the entity was never updated.
     */
    public function testUpdatedAtIsNullForFreshEntity(): void
    {
        $this->assertNull($this->class->updatedAt);
    }
}
