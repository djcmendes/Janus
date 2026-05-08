<?php

/**
 * @file CollectionMetaToArrayTest.php
 *
 * Tests for CollectionMeta::toArray().
 *
 * @package App\Collections\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Entity\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(CollectionMeta::class)]
#[CoversMethod(CollectionMeta::class, 'toArray')]
final class CollectionMetaToArrayTest extends CollectionMetaTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->class->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('icon', $array);
        $this->assertArrayHasKey('note', $array);
        $this->assertArrayHasKey('hidden', $array);
        $this->assertArrayHasKey('singleton', $array);
        $this->assertArrayHasKey('sort_field', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }

    public function testToArrayMapsNameCorrectly(): void
    {
        $this->assertSame('articles', $this->class->toArray()['name']);
    }

    public function testToArrayFormatsCreatedAtAsAtomString(): void
    {
        $result = $this->class->toArray()['created_at'];

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $result,
        );
    }

    public function testToArrayMapsAllPopulatedFields(): void
    {
        $collection = $this->makeCollectionMeta();
        $array      = $collection->toArray();

        $this->assertSame('Articles', $array['label']);
        $this->assertSame('mdi-file-document', $array['icon']);
        $this->assertSame('Main blog articles collection.', $array['note']);
        $this->assertFalse($array['hidden']);
        $this->assertFalse($array['singleton']);
        $this->assertSame('sort', $array['sort_field']);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToArrayReturnsNullForUnsetOptionalFields(): void
    {
        $array = $this->class->toArray();

        $this->assertNull($array['label']);
        $this->assertNull($array['icon']);
        $this->assertNull($array['note']);
        $this->assertNull($array['sort_field']);
        $this->assertNull($array['updated_at']);
    }

    public function testToArrayFormatsUpdatedAtAsAtomStringWhenSet(): void
    {
        $this->class->setLabel('Articles');
        $result = $this->class->toArray()['updated_at'];

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $result,
        );
    }
}
