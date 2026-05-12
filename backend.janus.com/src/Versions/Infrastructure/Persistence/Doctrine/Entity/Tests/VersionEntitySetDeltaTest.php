<?php

/**
 * @file VersionEntitySetDeltaTest.php
 *
 * Tests for VersionEntity::setDelta().
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
 * Verifies that setDelta() stores the value, handles null, and returns static.
 */
#[CoversClass(VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setDelta')]
final class VersionEntitySetDeltaTest extends VersionEntityTest
{
    public function testSetDeltaStoresArray(): void
    {
        $delta = ['title' => 'Hello'];
        $this->class->setDelta($delta);

        $this->assertSame($delta, $this->class->getDelta());
    }

    public function testSetDeltaWithNullClearsDelta(): void
    {
        $this->class->setDelta(['x' => 1]);
        $this->class->setDelta(null);

        $this->assertNull($this->class->getDelta());
    }

    public function testSetDeltaReturnsStatic(): void
    {
        $result = $this->class->setDelta(null);
        $this->assertSame($this->class, $result);
    }
}
