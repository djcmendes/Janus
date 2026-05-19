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
#[CoversClass(className:  ActivityRepository::class)]
#[CoversMethod(className: ActivityRepository::class, methodName: 'findById')]
final class ActivityRepositoryFindByIdTest extends ActivityRepositoryTest
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that findById() returns a domain Activity when the record exists.
     */
    public function testFindByIdReturnsDomainActivityForExistingId(): void
    {
        $this->entityManager->method(constraint: 'find')
                            ->willReturn(value: $this->makeActivityEntity());

        $result = $this->class->findById(id: self::LOOKUP_UUID);

        $this->assertInstanceOf(expected: Activity::class, actual: $result);
    }

    /**
     * Test that findById() maps the ActivityEntity action onto the returned domain Activity.
     */
    public function testFindByIdMapsEntityToDomainActivity(): void
    {
        $this->entityManager->method(constraint:'find')
                            ->willReturn(value: $this->makeActivityEntity(action: 'delete', collection: 'articles', item: '7'));

        $result = $this->class->findById(id: self::LOOKUP_UUID);

        $this->assertNotNull(actual: $result);
        $this->assertSame(expected: 'delete', actual: $result->action);
        $this->assertSame(expected: 'articles', actual: $result->collection);
    }

    /**
     * Test that findById() returns null when no record exists for the given UUID.
     */
    public function testFindByIdReturnsNullForNonExistentId(): void
    {
        $this->entityManager->method(constraint: 'find')
                            ->willReturn(value: null);

        $result = $this->class->findById(id: self::LOOKUP_UUID);

        $this->assertNull(actual: $result);
    }

    /**
     * Test that findById() passes ActivityEntity::class and the UUID to the entity manager.
     */
    public function testFindByIdPassesCorrectClassAndIdToEntityManager(): void
    {
        $this->entityManager->expects(invocationRule: $this->once())
                            ->method(constraint: 'find')
                            ->with(ActivityEntity::class, self::LOOKUP_UUID)
                            ->willReturn(value: null);

        $this->class->findById(id: self::LOOKUP_UUID);
    }
}
