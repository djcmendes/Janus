<?php

/**
 * @file ActivityRepositoryFindByIdTest.php
 *
 * Tests for ActivityRepository::findById().
 *
 * @package App\Activity\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository\Tests;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use App\Activity\Infrastructure\Repository\ActivityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for ActivityRepository::findById().
 *
 * Covers: domain Activity returned when the record exists (mapped from ActivityEntity),
 * null returned when no match, and the correct UUID forwarded to the entity manager.
 */
#[CoversClass(ActivityRepository::class)]
#[CoversMethod(ActivityRepository::class, 'findById')]
final class ActivityRepositoryFindByIdTest extends ActivityRepositoryTest
{
    /** @var string */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that findById() returns a domain Activity when the record exists.
     */
    public function testFindByIdReturnsDomainActivityForExistingId(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn($this->makeActivityEntity());

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertInstanceOf(Activity::class, $result);
    }

    /**
     * Test that findById() maps the ActivityEntity action onto the returned domain Activity.
     */
    public function testFindByIdMapsEntityToDomainActivity(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn($this->makeActivityEntity('delete', 'articles', '7'));

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertSame('delete', $result->getAction());
        $this->assertSame('articles', $result->getCollection());
    }

    /**
     * Test that findById() returns null when no record exists for the given UUID.
     */
    public function testFindByIdReturnsNullForNonExistentId(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn(null);

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertNull($result);
    }

    /**
     * Test that findById() passes ActivityEntity::class and the UUID to the entity manager.
     */
    public function testFindByIdPassesCorrectClassAndIdToEntityManager(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(ActivityEntity::class, self::LOOKUP_UUID)
            ->willReturn(null);

        $this->class->findById(self::LOOKUP_UUID);
    }
}
