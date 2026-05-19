<?php

/**
 * @file VersionRepositoryFindPaginatedTest.php
 *
 * Tests for VersionRepository::findPaginated().
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Repository\VersionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies findPaginated() returns mapped domain Versions with correct query parameters.
 */
#[CoversClass(className: VersionRepository::class)]
#[CoversMethod(VersionRepository::class, 'findPaginated')]
final class VersionRepositoryFindPaginatedTest extends VersionRepositoryTest
{
    /**
     * Test that findPaginated() returns an array of domain Version entities.
     */
    public function testFindPaginatedReturnsDomainVersions(): void
    {
        $this->query->method('getResult')->willReturn([$this->makeVersionEntity()]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Version::class, $result[0]);
    }

    /**
     * Test that findPaginated() returns an empty array when the repository has no results.
     */
    public function testFindPaginatedReturnsEmptyArrayForNoResults(): void
    {
        $this->query->method('getResult')->willReturn([]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertSame([], $result);
    }

    /**
     * Test that findPaginated() passes the limit to setMaxResults.
     */
    public function testFindPaginatedForwardsLimit(): void
    {
        $this->queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with(10)
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0);
    }

    /**
     * Test that findPaginated() passes the offset to setFirstResult.
     */
    public function testFindPaginatedForwardsOffset(): void
    {
        $this->queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with(5)
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 5);
    }

    /**
     * Test that findPaginated() does not add a WHERE clause when no filters are set.
     */
    public function testFindPaginatedAddsNoWhereWithoutFilters(): void
    {
        $this->queryBuilder->expects($this->never())->method('andWhere');
        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 0, null, null);
    }

    /**
     * Test that findPaginated() adds a collection filter when provided.
     */
    public function testFindPaginatedAddsCollectionFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');
        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 0, 'articles', null);
    }

    /**
     * Test that findPaginated() adds an item filter when provided.
     */
    public function testFindPaginatedAddsItemFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');
        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 0, null, 'item-uuid-1');
    }
}
