<?php

/**
 * @file VersionToArrayTest.php
 *
 * Tests for Version::toArray().
 *
 * @package App\Versions\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Entity\Tests;

use App\Versions\Domain\Entity\Version;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that toArray() produces a complete key-value map of all Version fields.
 */
#[CoversClass(Version::class)]
#[CoversMethod(Version::class, 'toArray')]
final class VersionToArrayTest extends VersionTest
{
    /**
     * Test that toArray() returns an array.
     */
    public function testToArrayReturnsArray(): void
    {
        $this->assertIsArray($this->class->toArray());
    }

    /**
     * Test that toArray() includes the id key.
     */
    public function testToArrayContainsId(): void
    {
        $result = $this->class->toArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertSame($this->class->getId(), $result['id']);
    }

    /**
     * Test that toArray() includes the collection key.
     */
    public function testToArrayContainsCollection(): void
    {
        $result = $this->class->toArray();

        $this->assertArrayHasKey('collection', $result);
        $this->assertSame('articles', $result['collection']);
    }

    /**
     * Test that toArray() includes the item key.
     */
    public function testToArrayContainsItem(): void
    {
        $result = $this->class->toArray();

        $this->assertArrayHasKey('item', $result);
        $this->assertSame('item-uuid-1', $result['item']);
    }

    /**
     * Test that toArray() includes the key field.
     */
    public function testToArrayContainsKey(): void
    {
        $result = $this->class->toArray();

        $this->assertArrayHasKey('key', $result);
        $this->assertSame('main', $result['key']);
    }

    /**
     * Test that toArray() includes the data array.
     */
    public function testToArrayContainsData(): void
    {
        $result = $this->class->toArray();

        $this->assertArrayHasKey('data', $result);
        $this->assertSame(['title' => 'Hello'], $result['data']);
    }

    /**
     * Test that toArray() includes delta as null when not set.
     */
    public function testToArrayContainsDeltaAsNull(): void
    {
        $result = $this->class->toArray();

        $this->assertArrayHasKey('delta', $result);
        $this->assertNull($result['delta']);
    }

    /**
     * Test that toArray() formats createdAt as an ATOM string.
     */
    public function testToArrayFormatsCreatedAtAsAtom(): void
    {
        $result = $this->class->toArray();

        $this->assertSame(
            $this->class->getCreatedAt()->format(DateTimeInterface::ATOM),
            $result['createdAt'],
        );
    }

    /**
     * Test that toArray() formats updatedAt as null when never updated.
     */
    public function testToArrayFormatsUpdatedAtAsNullWhenUnset(): void
    {
        $result = $this->class->toArray();

        $this->assertNull($result['updatedAt']);
    }

    /**
     * Test that toArray() formats updatedAt as an ATOM string after a mutation.
     */
    public function testToArrayFormatsUpdatedAtAsAtomAfterMutation(): void
    {
        $this->class->setKey('draft');

        $result = $this->class->toArray();

        $this->assertSame(
            $this->class->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $result['updatedAt'],
        );
    }
}
