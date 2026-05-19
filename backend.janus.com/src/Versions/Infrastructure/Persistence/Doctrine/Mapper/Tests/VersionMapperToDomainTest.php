<?php

/**
 * @file VersionMapperToDomainTest.php
 *
 * Tests for VersionMapper::toDomain().
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Persistence\Doctrine\Mapper\VersionMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that toDomain() converts all VersionEntity fields to a correct domain Version.
 */
#[CoversClass(className: VersionMapper::class)]
#[CoversMethod(VersionMapper::class, 'toDomain')]
final class VersionMapperToDomainTest extends VersionMapperTest
{
    /**
     * Test that toDomain() returns a domain Version instance.
     */
    public function testToDomainReturnsVersionInstance(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertInstanceOf(Version::class, $result);
    }

    /**
     * Test that toDomain() maps the UUID string id correctly.
     */
    public function testToDomainMapsId(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame(self::FIXED_UUID, $result->getId());
    }

    /**
     * Test that toDomain() maps the collection field.
     */
    public function testToDomainMapsCollection(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('articles', $result->getCollection());
    }

    /**
     * Test that toDomain() maps the item field.
     */
    public function testToDomainMapsItem(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('item-uuid-1', $result->getItem());
    }

    /**
     * Test that toDomain() maps the key field.
     */
    public function testToDomainMapsKey(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame('main', $result->getKey());
    }

    /**
     * Test that toDomain() maps the data array.
     */
    public function testToDomainMapsData(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertSame(['title' => 'Hello'], $result->getData());
    }

    /**
     * Test that toDomain() maps the createdAt timestamp from the entity.
     */
    public function testToDomainMapsCreatedAt(): void
    {
        $entity = $this->makeEntity();
        $result = $this->class->toDomain($entity);

        $this->assertSame(
            $entity->getCreatedAt()->getTimestamp(),
            $result->getCreatedAt()->getTimestamp(),
        );
    }

    /**
     * Test that toDomain() maps null updatedAt correctly.
     */
    public function testToDomainMapsNullUpdatedAt(): void
    {
        $result = $this->class->toDomain($this->makeEntity());

        $this->assertNull($result->getUpdatedAt());
    }
}
