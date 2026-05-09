<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\ExtensionMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExtensionMapper::class)]
final class ExtensionMapperToDomainTest extends ExtensionMapperTest
{
    public function testToDomainReturnsDomainExtension(): void
    {
        $entity = $this->makeEntity();
        $domain = $this->mapper->toDomain($entity);

        $this->assertInstanceOf(Extension::class, $domain);
    }

    public function testToDomainMapsId(): void
    {
        $entity = $this->makeEntity(id: 'dddddddd-0000-7000-8000-000000000004');
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame('dddddddd-0000-7000-8000-000000000004', $domain->getId());
    }

    public function testToDomainMapsName(): void
    {
        $entity = $this->makeEntity(name: 'layout-grid');
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame('layout-grid', $domain->getName());
    }

    public function testToDomainMapsType(): void
    {
        $entity = $this->makeEntity(type: ExtensionType::LAYOUT);
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame(ExtensionType::LAYOUT, $domain->getType());
    }

    public function testToDomainMapsVersion(): void
    {
        $entity = $this->makeEntity(version: '4.2.0');
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame('4.2.0', $domain->getVersion());
    }

    public function testToDomainMapsEnabled(): void
    {
        $entity = $this->makeEntity(enabled: false);
        $domain = $this->mapper->toDomain($entity);

        $this->assertFalse($domain->isEnabled());
    }

    public function testToDomainMapsDescription(): void
    {
        $entity = $this->makeEntity(description: 'A layout component');
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame('A layout component', $domain->getDescription());
    }

    public function testToDomainMapsNullDescription(): void
    {
        $entity = $this->makeEntity(description: null);
        $domain = $this->mapper->toDomain($entity);

        $this->assertNull($domain->getDescription());
    }

    public function testToDomainMapsMeta(): void
    {
        $entity = $this->makeEntity(meta: ['entry' => 'dist/layout.js']);
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame(['entry' => 'dist/layout.js'], $domain->getMeta());
    }

    public function testToDomainMapsNullMeta(): void
    {
        $entity = $this->makeEntity(meta: null);
        $domain = $this->mapper->toDomain($entity);

        $this->assertNull($domain->getMeta());
    }

    public function testToDomainMapsCreatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2023-01-10T09:00:00Z');
        $entity = $this->makeEntity(createdAt: $ts);
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame($ts, $domain->getCreatedAt());
    }

    public function testToDomainMapsUpdatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2024-09-15T15:30:00Z');
        $entity = $this->makeEntity(updatedAt: $ts);
        $domain = $this->mapper->toDomain($entity);

        $this->assertSame($ts, $domain->getUpdatedAt());
    }
}
