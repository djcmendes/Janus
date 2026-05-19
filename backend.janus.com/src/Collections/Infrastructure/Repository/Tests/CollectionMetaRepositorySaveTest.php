<?php

/**
 * @file CollectionMetaRepositorySaveTest.php
 *
 * Tests for CollectionMetaRepository::save().
 *
 * @package App\Collections\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository\Tests;

use App\Collections\Infrastructure\Repository\CollectionMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMetaRepository::class)]
#[CoversMethod(CollectionMetaRepository::class, 'save')]
final class CollectionMetaRepositorySaveTest extends CollectionMetaRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSaveCallsPersistForNewEntity(): void
    {
        $collection = $this->makeCollectionMeta();

        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->save($collection);
    }

    public function testSaveWithFlushFalseDoesNotCallFlush(): void
    {
        $collection = $this->makeCollectionMeta();

        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->class->save($collection, false);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSaveUpdatesExistingEntityWhenFoundInIdentityMap(): void
    {
        $collection = $this->makeCollectionMeta();
        $entity     = $this->makeCollectionMetaEntity();

        $this->entityManager->method('find')->willReturn($entity);
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->save($collection);
    }
}
