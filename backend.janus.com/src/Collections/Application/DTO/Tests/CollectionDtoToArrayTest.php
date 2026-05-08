<?php

/**
 * @file CollectionDtoToArrayTest.php
 *
 * Tests for CollectionDto::toArray().
 *
 * @package App\Collections\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\DTO\Tests;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(CollectionDto::class)]
#[CoversMethod(CollectionDto::class, 'toArray')]
final class CollectionDtoToArrayTest extends CollectionDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->class->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('collection', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('icon', $array);
        $this->assertArrayHasKey('note', $array);
        $this->assertArrayHasKey('hidden', $array);
        $this->assertArrayHasKey('singleton', $array);
        $this->assertArrayHasKey('sort_field', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);
    }

    public function testToArrayUsesCollectionKeyForName(): void
    {
        $this->assertSame('articles', $this->class->toArray()['collection']);
    }

    public function testToArrayMapsSortFieldWithSnakeCase(): void
    {
        $this->assertSame('sort', $this->class->toArray()['sort_field']);
    }

    public function testToArrayMapsAllPopulatedFields(): void
    {
        $array = $this->class->toArray();

        $this->assertSame('Articles', $array['label']);
        $this->assertSame('mdi-file-document', $array['icon']);
        $this->assertSame('Main blog articles collection.', $array['note']);
        $this->assertFalse($array['hidden']);
        $this->assertFalse($array['singleton']);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToArrayIncludesNullFieldsWhenNotSet(): void
    {
        $entity = new CollectionMeta('posts');
        $dto    = CollectionDto::fromEntity($entity);
        $array  = $dto->toArray();

        $this->assertNull($array['label']);
        $this->assertNull($array['icon']);
        $this->assertNull($array['note']);
        $this->assertNull($array['sort_field']);
        $this->assertNull($array['updated_at']);
    }
}
