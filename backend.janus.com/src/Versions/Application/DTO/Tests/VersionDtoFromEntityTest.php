<?php

/**
 * @file VersionDtoFromEntityTest.php
 *
 * Tests for VersionDto::fromEntity().
 *
 * @package App\Versions\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\DTO\Tests;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Entity\Version;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that fromEntity() maps every Version field to the correct VersionDto property.
 */
#[CoversClass(VersionDto::class)]
#[CoversMethod(VersionDto::class, 'fromEntity')]
final class VersionDtoFromEntityTest extends VersionDtoTest
{
    /**
     * Test that fromEntity() maps the Version UUID to the id property.
     */
    public function testFromEntityMapsId(): void
    {
        $version = $this->makeVersion();
        $dto     = VersionDto::fromEntity($version);

        $this->assertSame($version->getId(), $dto->id);
    }

    /**
     * Test that fromEntity() maps the collection field.
     */
    public function testFromEntityMapsCollection(): void
    {
        $this->assertSame('articles', $this->class->collection);
    }

    /**
     * Test that fromEntity() maps the item field.
     */
    public function testFromEntityMapsItem(): void
    {
        $this->assertSame('item-uuid-1', $this->class->item);
    }

    /**
     * Test that fromEntity() maps the key field.
     */
    public function testFromEntityMapsKey(): void
    {
        $this->assertSame('main', $this->class->key);
    }

    /**
     * Test that fromEntity() maps the data array.
     */
    public function testFromEntityMapsData(): void
    {
        $this->assertSame(['title' => 'Hello'], $this->class->data);
    }

    /**
     * Test that fromEntity() maps the delta array.
     */
    public function testFromEntityMapsDelta(): void
    {
        $this->assertSame(['title' => 'Hello'], $this->class->delta);
    }

    /**
     * Test that fromEntity() maps the userId field.
     */
    public function testFromEntityMapsUserId(): void
    {
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $this->class->userId);
    }

    /**
     * Test that fromEntity() formats createdAt as an ATOM string.
     */
    public function testFromEntityFormatsCreatedAtAsAtom(): void
    {
        $version = $this->makeVersion();
        $dto     = VersionDto::fromEntity($version);

        $this->assertSame(
            $version->getCreatedAt()->format(DateTimeInterface::ATOM),
            $dto->createdAt,
        );
    }

    /**
     * Test that fromEntity() stores null for updatedAt when the entity was never updated.
     */
    public function testFromEntityMapsNullUpdatedAt(): void
    {
        $dto = VersionDto::fromEntity(new Version('col', 'item', 'key', []));

        $this->assertNull($dto->updatedAt);
    }

    /**
     * Test that fromEntity() maps null delta when the entity has no delta.
     */
    public function testFromEntityMapsNullDelta(): void
    {
        $dto = VersionDto::fromEntity(new Version('col', 'item', 'key', []));

        $this->assertNull($dto->delta);
    }
}
