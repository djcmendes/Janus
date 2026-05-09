<?php

/**
 * @file DashboardRepositoryFindPaginatedTest.php
 *
 * Tests for DashboardRepository::findPaginated().
 *
 * @package App\Dashboards\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Infrastructure\Repository\DashboardRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies findPaginated() maps query results to domain Dashboard entities.
 */
#[CoversClass(DashboardRepository::class)]
final class DashboardRepositoryFindPaginatedTest extends DashboardRepositoryTest
{
    /**
     * Test that findPaginated() returns an array.
     */
    public function testFindPaginatedReturnsArray(): void
    {
        $this->query->method('getResult')->willReturn([]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertIsArray($result);
    }

    /**
     * Test that findPaginated() maps each entity to a Dashboard domain object.
     */
    public function testFindPaginatedMapsToDomain(): void
    {
        $entity = $this->makeDashboardEntity();
        $this->query->method('getResult')->willReturn([$entity]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Dashboard::class, $result[0]);
    }

    /**
     * Test that findPaginated() applies the userId filter via andWhere.
     */
    public function testFindPaginatedAppliesUserIdFilter(): void
    {
        $this->queryBuilder->expects($this->once())
                           ->method('andWhere')
                           ->with('d.userId = :userId')
                           ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, 'user-uuid-001');
    }

    /**
     * Test that findPaginated() does not call andWhere when userId is null.
     */
    public function testFindPaginatedSkipsFilterWhenUserIdNull(): void
    {
        $this->queryBuilder->expects($this->never())->method('andWhere');
        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, null);
    }

    /**
     * Test that findPaginated() returns an empty array when the query yields no results.
     */
    public function testFindPaginatedReturnsEmptyArrayWhenNoResults(): void
    {
        $this->query->method('getResult')->willReturn([]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertSame([], $result);
    }
}
