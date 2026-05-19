<?php

/**
 * @file CollectionMetaReconstituteTest.php
 *
 * Tests for CollectionMeta::reconstitute().
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
#[CoversMethod(CollectionMeta::class, 'reconstitute')]
final class CollectionMetaReconstituteTest extends CollectionMetaTest
{
    /** @var string */
    private const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    public function testReconstituteUsesSuppliedId(): void
    {
        $collection = CollectionMeta::reconstitute(
            self::FIXED_UUID, 'articles', null, null, null,
            false, false, null, new \DateTimeImmutable(), null,
        );

        $this->assertSame(self::FIXED_UUID, $collection->getId());
    }

    public function testReconstituteUsesSuppliedCreatedAt(): void
    {
        $createdAt  = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $collection = CollectionMeta::reconstitute(
            self::FIXED_UUID, 'articles', null, null, null,
            false, false, null, $createdAt, null,
        );

        $this->assertSame($createdAt, $collection->getCreatedAt());
    }

    public function testReconstitutePopulatesAllFields(): void
    {
        $createdAt  = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $updatedAt  = new \DateTimeImmutable('2024-06-01T12:00:00+00:00');

        $collection = CollectionMeta::reconstitute(
            id:        self::FIXED_UUID,
            name:      'articles',
            label:     'Articles',
            icon:      'mdi-file',
            note:      'Main articles.',
            hidden:    true,
            singleton: true,
            sortField: 'sort',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $this->assertSame('articles', $collection->getName());
        $this->assertSame('Articles', $collection->getLabel());
        $this->assertSame('mdi-file', $collection->getIcon());
        $this->assertSame('Main articles.', $collection->getNote());
        $this->assertTrue($collection->isHidden());
        $this->assertTrue($collection->isSingleton());
        $this->assertSame('sort', $collection->getSortField());
        $this->assertSame($updatedAt, $collection->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testReconstituteAcceptsNullForAllOptionalFields(): void
    {
        $collection = CollectionMeta::reconstitute(
            self::FIXED_UUID, 'articles', null, null, null,
            false, false, null, new \DateTimeImmutable(), null,
        );

        $this->assertNull($collection->getLabel());
        $this->assertNull($collection->getIcon());
        $this->assertNull($collection->getNote());
        $this->assertNull($collection->getSortField());
        $this->assertNull($collection->getUpdatedAt());
    }

    public function testReconstituteDoesNotRegenerateId(): void
    {
        $collection = CollectionMeta::reconstitute(
            self::FIXED_UUID, 'articles', null, null, null,
            false, false, null, new \DateTimeImmutable(), null,
        );

        $this->assertSame(self::FIXED_UUID, $collection->getId());
    }
}
