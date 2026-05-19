<?php

/**
 * @file CollectionMetaBaseTest.php
 *
 * Constructor and interface compliance tests for the CollectionMeta domain entity.
 *
 * @package App\Collections\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Entity\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: CollectionMeta::class)]
final class CollectionMetaBaseTest extends CollectionMetaTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfCollectionMeta(): void
    {
        $this->assertInstanceOf(CollectionMeta::class, $this->class);
    }

    public function testConstructorSetsName(): void
    {
        $this->assertSame('articles', $this->class->getName());
    }

    public function testConstructorGeneratesUuidV7String(): void
    {
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $this->class->getId(),
        );
    }

    public function testConstructorSetsCreatedAtToApproximatelyNow(): void
    {
        $before     = new \DateTimeImmutable();
        $collection = new CollectionMeta('posts');
        $after      = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $collection->getCreatedAt());
        $this->assertLessThanOrEqual($after, $collection->getCreatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testConstructorDefaultsLabelToNull(): void
    {
        $this->assertNull($this->class->getLabel());
    }

    public function testConstructorDefaultsIconToNull(): void
    {
        $this->assertNull($this->class->getIcon());
    }

    public function testConstructorDefaultsNoteToNull(): void
    {
        $this->assertNull($this->class->getNote());
    }

    public function testConstructorDefaultsHiddenToFalse(): void
    {
        $this->assertFalse($this->class->isHidden());
    }

    public function testConstructorDefaultsSingletonToFalse(): void
    {
        $this->assertFalse($this->class->isSingleton());
    }

    public function testConstructorDefaultsSortFieldToNull(): void
    {
        $this->assertNull($this->class->getSortField());
    }

    public function testConstructorDefaultsUpdatedAtToNull(): void
    {
        $this->assertNull($this->class->getUpdatedAt());
    }

    public function testEachInstanceReceivesUniqueId(): void
    {
        $a = new CollectionMeta('posts');
        $b = new CollectionMeta('comments');

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
