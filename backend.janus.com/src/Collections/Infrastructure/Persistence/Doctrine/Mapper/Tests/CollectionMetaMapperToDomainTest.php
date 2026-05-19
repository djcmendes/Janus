<?php

/**
 * @file CollectionMetaMapperToDomainTest.php
 *
 * Tests for CollectionMetaMapper::toDomain().
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Infrastructure\Persistence\Doctrine\Mapper\CollectionMetaMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMetaMapper::class)]
#[CoversMethod(CollectionMetaMapper::class, 'toDomain')]
final class CollectionMetaMapperToDomainTest extends CollectionMetaMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToDomainReturnsDomainCollectionMeta(): void
    {
        $this->assertInstanceOf(CollectionMeta::class, $this->class->toDomain($this->makeEntity()));
    }

    public function testToDomainMapsId(): void
    {
        $this->assertSame(self::FIXED_UUID, $this->class->toDomain($this->makeEntity())->getId());
    }

    public function testToDomainMapsName(): void
    {
        $this->assertSame('articles', $this->class->toDomain($this->makeEntity())->getName());
    }

    public function testToDomainMapsLabel(): void
    {
        $this->assertSame('Articles', $this->class->toDomain($this->makeEntity())->getLabel());
    }

    public function testToDomainMapsIcon(): void
    {
        $this->assertSame('mdi-file-document', $this->class->toDomain($this->makeEntity())->getIcon());
    }

    public function testToDomainMapsNote(): void
    {
        $this->assertSame('Main blog articles collection.', $this->class->toDomain($this->makeEntity())->getNote());
    }

    public function testToDomainMapsHidden(): void
    {
        $this->assertFalse($this->class->toDomain($this->makeEntity())->isHidden());
    }

    public function testToDomainMapsSingleton(): void
    {
        $this->assertFalse($this->class->toDomain($this->makeEntity())->isSingleton());
    }

    public function testToDomainMapsSortField(): void
    {
        $this->assertSame('sort', $this->class->toDomain($this->makeEntity())->getSortField());
    }

    public function testToDomainMapsCreatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $entity = $this->makeEntity()->setCreatedAt($ts);

        $this->assertEquals($ts, $this->class->toDomain($entity)->getCreatedAt());
    }

    public function testToDomainMapsUpdatedAt(): void
    {
        $ts     = new \DateTimeImmutable('2024-06-01T12:00:00+00:00');
        $entity = $this->makeEntity()->setUpdatedAt($ts);

        $this->assertEquals($ts, $this->class->toDomain($entity)->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToDomainHandlesNullOptionalFields(): void
    {
        $entity = $this->makeEntity()
            ->setLabel(null)
            ->setIcon(null)
            ->setNote(null)
            ->setSortField(null)
            ->setUpdatedAt(null);

        $domain = $this->class->toDomain($entity);

        $this->assertNull($domain->getLabel());
        $this->assertNull($domain->getIcon());
        $this->assertNull($domain->getNote());
        $this->assertNull($domain->getSortField());
        $this->assertNull($domain->getUpdatedAt());
    }

    // Roundtrip ────────────────────────────────────────────────────

    public function testRoundtripPreservesId(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getId(), $result->getId());
    }

    public function testRoundtripPreservesName(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getName(), $result->getName());
    }

    public function testRoundtripPreservesLabel(): void
    {
        $domain = $this->makeDomain();
        $result = $this->class->toDomain($this->class->toPersistence($domain));

        $this->assertSame($domain->getLabel(), $result->getLabel());
    }
}
