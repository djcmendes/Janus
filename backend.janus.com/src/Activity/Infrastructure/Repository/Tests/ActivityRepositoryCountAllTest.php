<?php

/**
 * @file ActivityRepositoryCountAllTest.php
 *
 * Tests for ActivityRepository::countAll().
 *
 * @package App\Activity\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository\Tests;

use App\Activity\Infrastructure\Repository\ActivityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for ActivityRepository::countAll().
 *
 * Covers: scalar result retrieval, zero count, integer cast from string,
 * no WHERE clause when filters are null, and each individual filter
 * adding the correct WHERE clause and parameter.
 */
#[CoversClass(ActivityRepository::class)]
#[CoversMethod(ActivityRepository::class, 'countAll')]
final class ActivityRepositoryCountAllTest extends ActivityRepositoryTest
{
    // ── Data Providers ────────────────────────────────────────────────────────

    /**
     * Provides a single active filter and the WHERE clause + parameter it should produce.
     *
     * @return array<string, array{
     *     collection: string|null,
     *     action: string|null,
     *     userId: string|null,
     *     expectedWhere: string,
     *     expectedParam: string,
     *     expectedValue: string,
     * }>
     */
    public static function singleFilterProvider(): array
    {
        return [
            'collection filter' => [
                'collection'    => 'posts',
                'action'        => null,
                'userId'        => null,
                'expectedWhere' => 'a.collection = :collection',
                'expectedParam' => 'collection',
                'expectedValue' => 'posts',
            ],
            'action filter' => [
                'collection'    => null,
                'action'        => 'delete',
                'userId'        => null,
                'expectedWhere' => 'a.action = :action',
                'expectedParam' => 'action',
                'expectedValue' => 'delete',
            ],
            'userId filter' => [
                'collection'    => null,
                'action'        => null,
                'userId'        => 'user-uuid',
                'expectedWhere' => 'a.userId = :userId',
                'expectedParam' => 'userId',
                'expectedValue' => 'user-uuid',
            ],
        ];
    }

    // ── Happy path ────────────────────────────────────────────────────────────

    /**
     * Test that countAll() returns the integer count from the query result.
     */
    public function testCountAllReturnsCorrectCount(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn(42);

        $result = $this->class->countAll();

        $this->assertSame(42, $result);
    }

    /**
     * Test that countAll() returns zero when no records exist.
     */
    public function testCountAllReturnsZeroWhenNoRecordsExist(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn(0);

        $result = $this->class->countAll();

        $this->assertSame(0, $result);
    }

    /**
     * Test that countAll() casts the query result to an integer.
     */
    public function testCountAllCastsScalarResultToInt(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('7');

        $result = $this->class->countAll();

        $this->assertIsInt($result);
        $this->assertSame(7, $result);
    }

    // ── Filter handling ───────────────────────────────────────────────────────

    /**
     * Test that countAll() adds no WHERE clause when all filters are null.
     */
    public function testCountAllDoesNotApplyWhereClauseWhenNoFilters(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('andWhere');

        $this->query->method('getSingleScalarResult')->willReturn(0);

        $this->class->countAll();
    }

    /**
     * Test that countAll() adds the correct WHERE clause and parameter for a single filter.
     *
     * @param string|null $collection
     * @param string|null $action
     * @param string|null $userId
     * @param string      $expectedWhere
     * @param string      $expectedParam
     * @param string      $expectedValue
     */
    #[DataProvider('singleFilterProvider')]
    public function testCountAllAppliesSingleFilter(
        ?string $collection,
        ?string $action,
        ?string $userId,
        string  $expectedWhere,
        string  $expectedParam,
        string  $expectedValue,
    ): void {
        $this->queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with($expectedWhere)
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with($expectedParam, $expectedValue)
            ->willReturn($this->queryBuilder);

        $this->query->method('getSingleScalarResult')->willReturn(0);

        $this->class->countAll($collection, $action, $userId);
    }

    /**
     * Test that countAll() applies all three filters when all are provided.
     */
    public function testCountAllAppliesAllFiltersWhenAllProvided(): void
    {
        $this->queryBuilder
            ->expects($this->exactly(3))
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->exactly(3))
            ->method('setParameter')
            ->willReturn($this->queryBuilder);

        $this->query->method('getSingleScalarResult')->willReturn(0);

        $this->class->countAll('posts', 'delete', 'user-uuid');
    }
}
