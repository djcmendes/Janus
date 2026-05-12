<?php

/**
 * @file VersionRepositorySaveTest.php
 *
 * Tests for VersionRepository::save().
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use App\Versions\Infrastructure\Repository\VersionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies save() persists a VersionEntity and flushes via the entity manager.
 */
#[CoversClass(VersionRepository::class)]
#[CoversMethod(VersionRepository::class, 'save')]
final class VersionRepositorySaveTest extends VersionRepositoryTest
{
    /**
     * Test that save() calls persist() with a VersionEntity when no existing record is found.
     */
    public function testSavePersistsVersionEntityWhenNew(): void
    {
        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(VersionEntity::class));
        $this->entityManager->method('flush');

        $this->class->save($this->makeVersion());
    }

    /**
     * Test that save() calls flush() once when $flush is true.
     */
    public function testSaveFlushesWhenFlagIsTrue(): void
    {
        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->save($this->makeVersion(), true);
    }

    /**
     * Test that save() does not call flush() when $flush is false.
     */
    public function testSaveDoesNotFlushWhenFlagIsFalse(): void
    {
        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->class->save($this->makeVersion(), false);
    }

    /**
     * Test that save() does not call persist() when an existing entity is found (update path).
     */
    public function testSaveDoesNotPersistWhenEntityExists(): void
    {
        $this->entityManager->method('find')->willReturn($this->makeVersionEntity());
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->method('flush');

        $this->class->save($this->makeVersion());
    }
}
