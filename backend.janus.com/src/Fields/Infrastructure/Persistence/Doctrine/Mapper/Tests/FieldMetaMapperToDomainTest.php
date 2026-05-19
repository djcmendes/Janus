<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Infrastructure\Persistence\Doctrine\Mapper\FieldMetaMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaMapper::class)]
final class FieldMetaMapperToDomainTest extends FieldMetaMapperTest
{
    public function testToDomainReturnsFieldMeta(): void
    {
        $domain = $this->mapper->toDomain($this->makeEntity());

        $this->assertInstanceOf(FieldMeta::class, $domain);
    }

    public function testToDomainMapsId(): void
    {
        $domain = $this->mapper->toDomain($this->makeEntity());

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $domain->getId());
    }

    public function testToDomainMapsCollection(): void
    {
        $domain = $this->mapper->toDomain($this->makeEntity());

        $this->assertSame('articles', $domain->getCollection());
    }

    public function testToDomainMapsField(): void
    {
        $domain = $this->mapper->toDomain($this->makeEntity());

        $this->assertSame('title', $domain->getField());
    }

    public function testToDomainMapsType(): void
    {
        $domain = $this->mapper->toDomain($this->makeEntity());

        $this->assertSame(FieldType::STRING, $domain->getType());
    }

    public function testToDomainMapsLabel(): void
    {
        $domain = $this->mapper->toDomain($this->makeEntity());

        $this->assertSame('Article Title', $domain->getLabel());
    }

    public function testToDomainMapsNullUpdatedAt(): void
    {
        $domain = $this->mapper->toDomain($this->makeEntity());

        $this->assertNull($domain->getUpdatedAt());
    }

    public function testToDomainMapsUpdatedAt(): void
    {
        $dt     = new \DateTimeImmutable('2024-06-01T00:00:00Z');
        $entity = $this->makeEntity()->setUpdatedAt($dt);
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame($dt, $domain->getUpdatedAt());
    }

    public function testToDomainMapsOptions(): void
    {
        $entity = $this->makeEntity()->setOptions(['key' => 'val']);
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame(['key' => 'val'], $domain->getOptions());
    }

    public function testToDomainMapsInterface(): void
    {
        $entity = $this->makeEntity()->setInterface('input-text');
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame('input-text', $domain->getInterface());
    }
}
