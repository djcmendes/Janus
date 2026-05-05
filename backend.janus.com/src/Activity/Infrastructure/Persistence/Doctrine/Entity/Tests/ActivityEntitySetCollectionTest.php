<?php

/**
 * @file ActivityEntitySetCollectionTest.php
 *
 * Tests for ActivityEntity::setCollection() and ActivityEntity::getCollection().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(ActivityEntity::class)]
#[CoversMethod(ActivityEntity::class, 'setCollection')]
final class ActivityEntitySetCollectionTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetCollectionStoresValue(): void
    {
        $this->class->setCollection('articles');

        $this->assertSame('articles', $this->class->getCollection());
    }

    public function testSetCollectionReturnsStaticInstance(): void
    {
        $result = $this->class->setCollection('articles');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetCollectionAcceptsNull(): void
    {
        $this->class->setCollection(null);

        $this->assertNull($this->class->getCollection());
    }
}
