<?php

/**
 * @file CollectionMetaRepositoryDeleteTest.php
 *
 * Tests for CollectionMetaRepository::delete().
 *
 * @package App\Collections\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository\Tests;

use App\Collections\Infrastructure\Repository\CollectionMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(CollectionMetaRepository::class)]
#[CoversMethod(CollectionMetaRepository::class, 'delete')]
final class CollectionMetaRepositoryDeleteTest extends CollectionMetaRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testDeleteCallsRemoveAndFlushWhenEntityFound(): void
    {
        $collection = $this->makeCollectionMeta();
        $entity     = $this->makeCollectionMetaEntity();

        $this->entityManager->method('find')->willReturn($entity);
        $this->entityManager->expects($this->once())->method('remove')->with($entity);
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->delete($collection);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testDeleteDoesNothingWhenEntityNotFound(): void
    {
        $collection = $this->makeCollectionMeta();

        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->expects($this->never())->method('remove');
        $this->entityManager->expects($this->never())->method('flush');

        $this->class->delete($collection);
    }
}
