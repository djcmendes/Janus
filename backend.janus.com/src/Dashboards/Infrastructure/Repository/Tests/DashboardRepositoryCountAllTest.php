<?php

/**
 * @file DashboardRepositoryCountAllTest.php
 *
 * Tests for DashboardRepository::countAll().
 *
 * @package App\Dashboards\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository\Tests;

use App\Dashboards\Infrastructure\Repository\DashboardRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies countAll() returns the integer total from the scalar query result.
 */
#[CoversClass(DashboardRepository::class)]
final class DashboardRepositoryCountAllTest extends DashboardRepositoryTest
{
    /**
     * Test that countAll() returns an integer.
     */
    public function testCountAllReturnsInt(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('42');

        $result = $this->class->countAll();

        $this->assertIsInt($result);
    }

    /**
     * Test that countAll() casts the scalar result to an integer.
     */
    public function testCountAllCastsToInt(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('7');

        $this->assertSame(7, $this->class->countAll());
    }

    /**
     * Test that countAll() applies the userId filter when provided.
     */
    public function testCountAllAppliesUserIdFilter(): void
    {
        $this->queryBuilder->expects($this->once())
                           ->method('andWhere')
                           ->with('d.userId = :userId')
                           ->willReturn($this->queryBuilder);

        $this->query->method('getSingleScalarResult')->willReturn('3');

        $this->class->countAll('user-uuid-001');
    }

    /**
     * Test that countAll() does not apply the filter when userId is null.
     */
    public function testCountAllSkipsFilterWhenUserIdNull(): void
    {
        $this->queryBuilder->expects($this->never())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn('0');

        $this->class->countAll(null);
    }
}
