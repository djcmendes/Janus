<?php

/**
 * @file CollectionMetaSetSingletonTest.php
 *
 * Tests for CollectionMeta::setSingleton().
 *
 * @package App\Collections\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Entity\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMeta::class)]
#[CoversMethod(CollectionMeta::class, 'setSingleton')]
final class CollectionMetaSetSingletonTest extends CollectionMetaTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetSingletonToTrue(): void
    {
        $this->class->setSingleton(true);

        $this->assertTrue($this->class->isSingleton());
    }

    public function testSetSingletonToFalse(): void
    {
        $this->class->setSingleton(true);
        $this->class->setSingleton(false);

        $this->assertFalse($this->class->isSingleton());
    }

    public function testSetSingletonReturnsStatic(): void
    {
        $result = $this->class->setSingleton(true);

        $this->assertSame($this->class, $result);
    }

    public function testSetSingletonUpdatesUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setSingleton(true);

        $this->assertNotNull($this->class->getUpdatedAt());
    }
}
