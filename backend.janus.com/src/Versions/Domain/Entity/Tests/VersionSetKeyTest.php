<?php

/**
 * @file VersionSetKeyTest.php
 *
 * Tests for Version::setKey().
 *
 * @package App\Versions\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Entity\Tests;

use App\Versions\Domain\Entity\Version;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that setKey() stores the new label, returns static for fluency, and sets updatedAt.
 */
#[CoversClass(className: Version::class)]
#[CoversMethod(Version::class, 'setKey')]
final class VersionSetKeyTest extends VersionTest
{
    /**
     * Test that setKey() updates the version label returned by getKey().
     */
    public function testSetKeyUpdatesKey(): void
    {
        $this->class->setKey('draft');

        $this->assertSame('draft', $this->class->getKey());
    }

    /**
     * Test that setKey() returns the same Version instance for fluent chaining.
     */
    public function testSetKeyReturnsStatic(): void
    {
        $result = $this->class->setKey('draft');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setKey() sets updatedAt to a non-null DateTimeImmutable.
     */
    public function testSetKeySetsUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setKey('v2');

        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getUpdatedAt());
    }

    /**
     * Test that calling setKey() twice records the second call's timestamp in updatedAt.
     */
    public function testSetKeyUpdatesUpdatedAtOnEachCall(): void
    {
        $this->class->setKey('v1');
        $first = $this->class->getUpdatedAt();

        $this->class->setKey('v2');
        $second = $this->class->getUpdatedAt();

        $this->assertGreaterThanOrEqual($first->getTimestamp(), $second->getTimestamp());
    }
}
