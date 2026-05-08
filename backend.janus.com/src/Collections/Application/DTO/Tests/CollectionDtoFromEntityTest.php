<?php

/**
 * @file CollectionDtoFromEntityTest.php
 *
 * Tests for CollectionDto::fromEntity().
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
#[CoversMethod(CollectionDto::class, 'fromEntity')]
final class CollectionDtoFromEntityTest extends CollectionDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testFromEntityReturnsCollectionDto(): void
    {
        $this->assertInstanceOf(CollectionDto::class, $this->class);
    }

    public function testFromEntityMapsId(): void
    {
        $entity = $this->makeCollectionMeta();
        $dto    = CollectionDto::fromEntity($entity);

        $this->assertSame($entity->getId(), $dto->id);
    }

    public function testFromEntityMapsName(): void
    {
        $this->assertSame('articles', $this->class->name);
    }

    public function testFromEntityMapsLabel(): void
    {
        $this->assertSame('Articles', $this->class->label);
    }

    public function testFromEntityMapsIcon(): void
    {
        $this->assertSame('mdi-file-document', $this->class->icon);
    }

    public function testFromEntityMapsNote(): void
    {
        $this->assertSame('Main blog articles collection.', $this->class->note);
    }

    public function testFromEntityMapsHidden(): void
    {
        $this->assertFalse($this->class->hidden);
    }

    public function testFromEntityMapsSingleton(): void
    {
        $this->assertFalse($this->class->singleton);
    }

    public function testFromEntityMapsSortField(): void
    {
        $this->assertSame('sort', $this->class->sortField);
    }

    public function testFromEntityFormatsCreatedAtAsAtom(): void
    {
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $this->class->createdAt,
        );
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testFromEntityMapsNullOptionalFields(): void
    {
        $entity = new CollectionMeta('posts');
        $dto    = CollectionDto::fromEntity($entity);

        $this->assertNull($dto->label);
        $this->assertNull($dto->icon);
        $this->assertNull($dto->note);
        $this->assertNull($dto->sortField);
        $this->assertNull($dto->updatedAt);
    }

    public function testFromEntityFormatsUpdatedAtAsAtomWhenSet(): void
    {
        $entity = $this->makeCollectionMeta();
        $entity->setLabel('Updated');
        $dto = CollectionDto::fromEntity($entity);

        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $dto->updatedAt,
        );
    }
}
