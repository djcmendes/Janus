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
#[CoversClass(className:  ActivityRepository::class)]
#[CoversMethod(className: ActivityRepository::class, methodName: 'findPaginated')]
final class ActivityRepositoryFindPaginatedTest extends ActivityRepositoryTest
{
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

    /**
     * Test that findPaginated() maps ActivityEntity results to domain Activity instances.
     */
    public function testFindPaginatedReturnsMappedActivityCollection(): void
    {
        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: [ $this->makeActivityEntity() ]);

        $result = $this->class->findPaginated(limit: 25, offset: 0);

        $this->assertCount(expectedCount: 1, haystack: $result);
        $this->assertInstanceOf(expected: Activity::class, actual: $result[0]);
    }

    /**
     * Test that findPaginated() maps the action field from the ActivityEntity to the domain Activity.
     */
    public function testFindPaginatedMapsEntityActionToDomainActivity(): void
    {
        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: [
                        $this->makeActivityEntity(action: 'delete', collection: 'articles', item: '7')
                    ]);

        $result = $this->class->findPaginated(limit: 25, offset: 0);

        $this->assertSame(expected: 'delete', actual: $result[0]->action);
        $this->assertSame(expected: 'articles', actual: $result[0]->collection);
    }

    /**
     * Test that findPaginated() returns an empty array when no records match.
     */
    public function testFindPaginatedReturnsEmptyArrayWhenNoRecordsExist(): void
    {
        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: []);

        $result = $this->class->findPaginated(limit: 25, offset: 0);

        $this->assertSame(expected: [], actual: $result);
    }

    /**
     * Test that findPaginated() passes the limit value to setMaxResults().
     */
    public function testFindPaginatedSetsMaxResultsFromLimit(): void
    {
        $this->queryBuilder->expects(invocationRule: $this->once())
                           ->method(constraint: 'setMaxResults')
                           ->with(10)
                           ->willReturn(value: $this->queryBuilder);

        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: []);

        $this->class->findPaginated(limit: 10, offset: 0);
    }

    /**
     * Test that findPaginated() passes the offset value to setFirstResult().
     */
    public function testFindPaginatedSetsFirstResultFromOffset(): void
    {
        $this->queryBuilder->expects(invocationRule: $this->once())
                           ->method(constraint: 'setFirstResult')
                           ->with(50)
                           ->willReturn(value: $this->queryBuilder);

        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: []);

        $this->class->findPaginated(limit: 25, offset: 50);
    }

    /**
     * Test that findPaginated() adds no WHERE clause when all filters are null.
     */
    public function testFindPaginatedDoesNotApplyWhereClauseWhenNoFilters(): void
    {
        $this->queryBuilder->expects(invocationRule: $this->never())
                           ->method(constraint: 'andWhere');

        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: []);

        $this->class->findPaginated(limit: 25, offset: 0);
    }

    /**
     * Test that findPaginated() adds the correct WHERE clause and parameter for a single filter.
     *
     * @param string|null $collection    Collection name filter, or null to skip.
     * @param string|null $action        Action type filter (e.g. 'create', 'delete'), or null to skip.
     * @param string|null $userId        User UUID filter, or null to skip.
     * @param string      $expectedWhere DQL WHERE clause fragment expected to be passed to andWhere().
     * @param string      $expectedParam Named parameter key expected to be bound via setParameter().
     * @param string      $expectedValue Value expected to be bound to the named parameter.
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
        $this->queryBuilder->expects(invocationRule: $this->once())
                           ->method(constraint: 'andWhere')
                           ->with(arguments: $expectedWhere)
                           ->willReturn(value: $this->queryBuilder);

        $this->queryBuilder->expects(invocationRule: $this->once())
                           ->method(constraint: 'setParameter')
                           ->with($expectedParam, $expectedValue)
                           ->willReturn(value: $this->queryBuilder);

        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: []);

        $this->class->findPaginated(limit: 25, offset: 0, collection: $collection, action: $action, userId: $userId);
    }

    /**
     * Test that findPaginated() applies all three filters when all are provided.
     */
    public function testFindPaginatedAppliesAllFiltersWhenAllProvided(): void
    {
        $this->queryBuilder->expects(invocationRule: $this->exactly(count: 3))
                           ->method(constraint: 'andWhere')
                           ->willReturn(value: $this->queryBuilder);

        $this->queryBuilder->expects(invocationRule: $this->exactly(count:3))
                           ->method(constraint: 'setParameter')
                           ->willReturn(value: $this->queryBuilder);

        $this->query->method(constraint: 'getResult')
                    ->willReturn(value: []);

        $this->class->findPaginated(limit: 25,  offset: 0, collection: 'posts', action: 'create', userId: 'user-uuid');
    }
}
