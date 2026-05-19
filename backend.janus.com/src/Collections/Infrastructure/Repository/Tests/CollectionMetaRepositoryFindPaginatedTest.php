<?php

/**
 * @file CollectionMetaRepositoryFindPaginatedTest.php
 *
 * Tests for CollectionMetaRepository::findPaginated().
 *
 * @package App\Collections\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Infrastructure\Repository\CollectionMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMetaRepository::class)]
#[CoversMethod(CollectionMetaRepository::class, 'findPaginated')]
final class CollectionMetaRepositoryFindPaginatedTest extends CollectionMetaRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testFindPaginatedReturnsMappedDomainEntities(): void
    {
        $entity = $this->makeCollectionMetaEntity();

        $this->persister->method('loadAll')->willReturn([$entity]);

        $result = $this->class->findPaginated(10, 0);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(CollectionMeta::class, $result[0]);
    }

    public function testFindPaginatedMapsAllReturnedEntities(): void
    {
        $entity1 = $this->makeCollectionMetaEntity();
        $entity2 = $this->makeCollectionMetaEntity();

        $this->persister->method('loadAll')->willReturn([$entity1, $entity2]);

        $result = $this->class->findPaginated(10, 0);

        $this->assertCount(2, $result);
    }

    public function testFindPaginatedMapsEntityFieldsCorrectly(): void
    {
        $entity = $this->makeCollectionMetaEntity();

        $this->persister->method('loadAll')->willReturn([$entity]);

        $result = $this->class->findPaginated(10, 0);

        $this->assertSame('articles', $result[0]->getName());
        $this->assertSame('Articles', $result[0]->getLabel());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testFindPaginatedReturnsEmptyArrayWhenNoResults(): void
    {
        $this->persister->method('loadAll')->willReturn([]);

        $result = $this->class->findPaginated(10, 0);

        $this->assertSame([], $result);
    }
}
