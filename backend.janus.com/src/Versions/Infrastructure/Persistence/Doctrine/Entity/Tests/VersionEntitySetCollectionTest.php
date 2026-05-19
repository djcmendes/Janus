<?php

/**
 * @file VersionEntitySetCollectionTest.php
 *
 * Tests for VersionEntity::setCollection().
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
 * Verifies that setCollection() stores the value and returns static.
 */
#[CoversClass(className: VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setCollection')]
final class VersionEntitySetCollectionTest extends VersionEntityTest
{
    public function testSetCollectionStoresValue(): void
    {
        $this->class->setCollection('blog_posts');
        $this->assertSame('blog_posts', $this->class->getCollection());
    }

    public function testSetCollectionReturnsStatic(): void
    {
        $result = $this->class->setCollection('x');
        $this->assertSame($this->class, $result);
    }
}
