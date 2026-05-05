<?php

/**
 * @file ActivityRepositoryRecordTest.php
 *
 * Tests for ActivityRepository::record().
 *
 * @package App\Activity\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use App\Activity\Infrastructure\Repository\ActivityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for ActivityRepository::record().
 *
 * Covers: entity persistence via ActivityMapper, entity manager flush, and
 * the correct ActivityEntity type forwarded to the entity manager.
 */
#[CoversClass(ActivityRepository::class)]
#[CoversMethod(ActivityRepository::class, 'record')]
final class ActivityRepositoryRecordTest extends ActivityRepositoryTest
{
    /**
     * Test that record() calls persist() on the entity manager with an ActivityEntity.
     */
    public function testRecordPersistsActivityEntityToEntityManager(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(ActivityEntity::class));

        $this->entityManager->method('flush');

        $this->class->record($this->makeActivity());
    }

    /**
     * Test that record() calls flush() on the entity manager exactly once.
     */
    public function testRecordFlushesEntityManager(): void
    {
        $this->entityManager->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->class->record($this->makeActivity());
    }

    /**
     * Test that record() persists and flushes in a single call.
     */
    public function testRecordPersistsAndFlushesInSingleCall(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(ActivityEntity::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->class->record($this->makeActivity('delete', 'articles', '5'));
    }

    /**
     * Test that record() maps the domain action onto the persisted ActivityEntity.
     */
    public function testRecordPersistsEntityWithCorrectAction(): void
    {
        $captured = null;
        $this->entityManager
            ->method('persist')
            ->with($this->callback(static function (ActivityEntity $e) use (&$captured): bool {
                $captured = $e;
                return true;
            }));

        $this->entityManager->method('flush');

        $this->class->record($this->makeActivity('delete', 'articles', '5'));

        $this->assertSame('delete', $captured->getAction());
        $this->assertSame('articles', $captured->getCollection());
    }

    /**
     * Test that record() returns void and produces no output.
     */
    public function testRecordReturnsVoid(): void
    {
        $this->entityManager->method('persist');
        $this->entityManager->method('flush');

        $result = $this->class->record($this->makeActivity());

        $this->assertNull($result);
    }
}
