<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Fields\Domain\Enum\FieldType;
use App\Fields\Infrastructure\Persistence\Doctrine\Entity\FieldMetaEntity;
use App\Fields\Infrastructure\Persistence\Doctrine\Mapper\FieldMetaMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaMapper::class)]
final class FieldMetaMapperToPersistenceTest extends FieldMetaMapperTest
{
    public function testToPersistenceReturnsEntity(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertInstanceOf(FieldMetaEntity::class, $entity);
    }

    public function testToPersistenceMapsId(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $entity->getId());
    }

    public function testToPersistenceMapsCollection(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertSame('articles', $entity->getCollection());
    }

    public function testToPersistenceMapsField(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertSame('title', $entity->getField());
    }

    public function testToPersistenceMapsType(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertSame(FieldType::STRING, $entity->getType());
    }

    public function testToPersistenceMapsLabel(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertSame('Article Title', $entity->getLabel());
    }

    public function testToPersistenceMapsNullUpdatedAt(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertNull($entity->getUpdatedAt());
    }

    public function testToPersistenceMapsCreatedAt(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertEquals(new \DateTimeImmutable('2024-01-01T00:00:00Z'), $entity->getCreatedAt());
    }

    public function testToPersistenceMapsNullOptions(): void
    {
        $entity = $this->mapper->toPersistence($this->makeDomain());

        $this->assertNull($entity->getOptions());
    }
}
