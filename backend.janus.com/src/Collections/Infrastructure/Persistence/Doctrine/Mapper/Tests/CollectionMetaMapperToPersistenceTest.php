<?php

/**
 * @file CollectionMetaMapperToPersistenceTest.php
 *
 * Tests for CollectionMetaMapper::toPersistence().
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use App\Collections\Infrastructure\Persistence\Doctrine\Mapper\CollectionMetaMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Uid\Uuid;

#[CoversClass(CollectionMetaMapper::class)]
#[CoversMethod(CollectionMetaMapper::class, 'toPersistence')]
final class CollectionMetaMapperToPersistenceTest extends CollectionMetaMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToPersistenceReturnsCollectionMetaEntity(): void
    {
        $this->assertInstanceOf(CollectionMetaEntity::class, $this->class->toPersistence($this->makeDomain()));
    }

    public function testToPersistenceMapsIdAsUuid(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);

        $this->assertInstanceOf(Uuid::class, $entity->getId());
        $this->assertSame($domain->getId(), (string) $entity->getId());
    }

    public function testToPersistenceMapsName(): void
    {
        $this->assertSame('articles', $this->class->toPersistence($this->makeDomain())->getName());
    }

    public function testToPersistenceMapsLabel(): void
    {
        $this->assertSame('Articles', $this->class->toPersistence($this->makeDomain())->getLabel());
    }

    public function testToPersistenceMapsIcon(): void
    {
        $this->assertSame('mdi-file-document', $this->class->toPersistence($this->makeDomain())->getIcon());
    }

    public function testToPersistenceMapsNote(): void
    {
        $this->assertSame('Main blog articles collection.', $this->class->toPersistence($this->makeDomain())->getNote());
    }

    public function testToPersistenceMapsHidden(): void
    {
        $this->assertFalse($this->class->toPersistence($this->makeDomain())->isHidden());
    }

    public function testToPersistenceMapsSingleton(): void
    {
        $this->assertFalse($this->class->toPersistence($this->makeDomain())->isSingleton());
    }

    public function testToPersistenceMapsSortField(): void
    {
        $this->assertSame('sort', $this->class->toPersistence($this->makeDomain())->getSortField());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToPersistenceHandlesNullOptionalFields(): void
    {
        $domain = new CollectionMeta('posts');
        $entity = $this->class->toPersistence($domain);

        $this->assertNull($entity->getLabel());
        $this->assertNull($entity->getIcon());
        $this->assertNull($entity->getNote());
        $this->assertNull($entity->getSortField());
        $this->assertNull($entity->getUpdatedAt());
    }

    // Roundtrip ────────────────────────────────────────────────────

    public function testRoundtripPreservesId(): void
    {
        $entity = $this->makeEntity();
        $result = $this->class->toPersistence($this->class->toDomain($entity));

        $this->assertSame((string) $entity->getId(), (string) $result->getId());
    }

    public function testRoundtripPreservesName(): void
    {
        $entity = $this->makeEntity();
        $result = $this->class->toPersistence($this->class->toDomain($entity));

        $this->assertSame($entity->getName(), $result->getName());
    }
}
