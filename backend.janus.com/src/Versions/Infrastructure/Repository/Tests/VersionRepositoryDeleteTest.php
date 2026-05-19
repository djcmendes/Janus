<?php

/**
 * @file VersionRepositoryDeleteTest.php
 *
 * Tests for VersionRepository::delete().
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Infrastructure\Repository\VersionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies delete() removes the entity and flushes via the entity manager.
 */
#[CoversClass(className: VersionRepository::class)]
#[CoversMethod(VersionRepository::class, 'delete')]
final class VersionRepositoryDeleteTest extends VersionRepositoryTest
{
    /**
     * Test that delete() calls remove() and flush() when the entity exists.
     */
    public function testDeleteRemovesAndFlushesWhenEntityFound(): void
    {
        $entity = $this->makeVersionEntity();
        $this->entityManager->method('find')->willReturn($entity);
        $this->entityManager->expects($this->once())->method('remove')->with($entity);
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->delete($this->makeVersion());
    }

    /**
     * Test that delete() does not call remove() when no matching entity is found.
     */
    public function testDeleteDoesNotRemoveWhenEntityNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->expects($this->never())->method('remove');

        $this->class->delete($this->makeVersion());
    }
}
