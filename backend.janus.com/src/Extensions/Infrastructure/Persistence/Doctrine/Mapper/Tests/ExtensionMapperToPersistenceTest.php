<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Infrastructure\Persistence\Doctrine\Entity\ExtensionEntity;
use App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\ExtensionMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionMapper::class)]
final class ExtensionMapperToPersistenceTest extends ExtensionMapperTest
{
    public function testToPersistenceReturnsExtensionEntity(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->mapper->toPersistence($domain);

        $this->assertInstanceOf(ExtensionEntity::class, $entity);
    }

    public function testToPersistenceMapsId(): void
    {
        $domain = $this->makeDomain(id: 'eeeeeeee-0000-7000-8000-000000000005');
        $entity = $this->mapper->toPersistence($domain);

        $this->assertSame('eeeeeeee-0000-7000-8000-000000000005', $entity->getId());
    }

    public function testToPersistenceMapsName(): void
    {
        $domain = $this->makeDomain(name: 'endpoint-api');
        $entity = $this->mapper->toPersistence($domain);

        $this->assertSame('endpoint-api', $entity->getName());
    }

    public function testToPersistenceMapsType(): void
    {
        $domain = $this->makeDomain(type: ExtensionType::ENDPOINT);
        $entity = $this->mapper->toPersistence($domain);

        $this->assertSame(ExtensionType::ENDPOINT, $entity->getType());
    }

    public function testToPersistenceMapsVersion(): void
    {
        $domain = $this->makeDomain(version: '5.0.0');
        $entity = $this->mapper->toPersistence($domain);

        $this->assertSame('5.0.0', $entity->getVersion());
    }

    public function testToPersistenceMapsEnabled(): void
    {
        $domain = $this->makeDomain(enabled: false);
        $entity = $this->mapper->toPersistence($domain);

        $this->assertFalse($entity->isEnabled());
    }

    public function testToPersistenceMapsNullMeta(): void
    {
        $domain = $this->makeDomain(meta: null);
        $entity = $this->mapper->toPersistence($domain);

        $this->assertNull($entity->getMeta());
    }

    public function testToPersistenceMapsCreatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2022-12-01T00:00:00Z');
        $domain = $this->makeDomain(createdAt: $ts);
        $entity = $this->mapper->toPersistence($domain);

        $this->assertSame($ts, $entity->getCreatedAt());
    }

    public function testToPersistenceMapsUpdatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2025-03-20T18:00:00Z');
        $domain = $this->makeDomain(updatedAt: $ts);
        $entity = $this->mapper->toPersistence($domain);

        $this->assertSame($ts, $entity->getUpdatedAt());
    }
}
