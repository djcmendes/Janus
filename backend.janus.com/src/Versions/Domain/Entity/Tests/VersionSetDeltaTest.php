<?php

/**
 * @file VersionSetDeltaTest.php
 *
 * Tests for Version::setDelta().
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
 * Verifies that setDelta() stores the value, handles null, returns static, and sets updatedAt.
 */
#[CoversClass(Version::class)]
#[CoversMethod(Version::class, 'setDelta')]
final class VersionSetDeltaTest extends VersionTest
{
    /**
     * Test that setDelta() stores and returns a provided array.
     */
    public function testSetDeltaStoresDeltaArray(): void
    {
        $delta = ['title' => 'Updated'];
        $this->class->setDelta($delta);

        $this->assertSame($delta, $this->class->getDelta());
    }

    /**
     * Test that setDelta(null) clears the delta field.
     */
    public function testSetDeltaWithNullClearsDelta(): void
    {
        $this->class->setDelta(['foo' => 'bar']);
        $this->class->setDelta(null);

        $this->assertNull($this->class->getDelta());
    }

    /**
     * Test that setDelta() returns the same Version instance for fluent chaining.
     */
    public function testSetDeltaReturnsStatic(): void
    {
        $result = $this->class->setDelta(['x' => 1]);

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setDelta() sets updatedAt to a non-null DateTimeImmutable.
     */
    public function testSetDeltaSetsUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setDelta(['title' => 'Hello']);

        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getUpdatedAt());
    }
}
