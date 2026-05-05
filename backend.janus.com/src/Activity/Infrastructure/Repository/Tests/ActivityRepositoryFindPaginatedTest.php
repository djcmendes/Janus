<?php

/**
 * @file ActivityRepositoryFindPaginatedTest.php
 *
 * Tests for ActivityRepository::findPaginated().
 *
 * @package App\Activity\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository\Tests;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Infrastructure\Repository\ActivityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for ActivityRepository::findPaginated().
 *
 * Covers: result mapping from ActivityEntity to domain Activity, empty results,
 * limit/offset forwarding, no WHERE clause when filters are null, and each
 * individual filter adding the correct WHERE clause and parameter.
 */
#[CoversClass(ActivityRepository::class)]
#[CoversMethod(ActivityRepository::class, 'findPaginated')]
final class ActivityRepositoryFindPaginatedTest extends ActivityRepositoryTest
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
                'action'        => 'create',
                'userId'        => null,
                'expectedWhere' => 'a.action = :action',
                'expectedParam' => 'action',
                'expectedValue' => 'create',
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
     * Test that findPaginated() maps ActivityEntity results to domain Activity instances.
     */
    public function testFindPaginatedReturnsMappedActivityCollection(): void
    {
        $this->query->method('getResult')->willReturn([$this->makeActivityEntity()]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Activity::class, $result[0]);
    }

    /**
     * Test that findPaginated() maps the action field from the ActivityEntity to the domain Activity.
     */
    public function testFindPaginatedMapsEntityActionToDomainActivity(): void
    {
        $this->query->method('getResult')
                    ->willReturn([$this->makeActivityEntity('delete', 'articles', '7')]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertSame('delete', $result[0]->getAction());
        $this->assertSame('articles', $result[0]->getCollection());
    }

    /**
     * Test that findPaginated() returns an empty array when no records match.
     */
    public function testFindPaginatedReturnsEmptyArrayWhenNoRecordsExist(): void
    {
        $this->query->method('getResult')->willReturn([]);

        $result = $this->class->findPaginated(25, 0);

        $this->assertSame([], $result);
    }

    // ── Limit & offset forwarding ─────────────────────────────────────────────

    /**
     * Test that findPaginated() passes the limit value to setMaxResults().
     */
    public function testFindPaginatedSetsMaxResultsFromLimit(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with(10)
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0);
    }

    /**
     * Test that findPaginated() passes the offset value to setFirstResult().
     */
    public function testFindPaginatedSetsFirstResultFromOffset(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(50)
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 50);
    }

    // ── Filter handling ───────────────────────────────────────────────────────

    /**
     * Test that findPaginated() adds no WHERE clause when all filters are null.
     */
    public function testFindPaginatedDoesNotApplyWhereClauseWhenNoFilters(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('andWhere');

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 0);
    }

    /**
     * Test that findPaginated() adds the correct WHERE clause and parameter for a single filter.
     *
     * @param string|null $collection
     * @param string|null $action
     * @param string|null $userId
     * @param string      $expectedWhere
     * @param string      $expectedParam
     * @param string      $expectedValue
     */
    #[DataProvider('singleFilterProvider')]
    public function testFindPaginatedAppliesSingleFilter(
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

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 0, $collection, $action, $userId);
    }

    /**
     * Test that findPaginated() applies all three filters when all are provided.
     */
    public function testFindPaginatedAppliesAllFiltersWhenAllProvided(): void
    {
        $this->queryBuilder
            ->expects($this->exactly(3))
            ->method('andWhere')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->expects($this->exactly(3))
            ->method('setParameter')
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 0, 'posts', 'create', 'user-uuid');
    }
}
