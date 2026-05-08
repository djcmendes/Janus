<?php

/**
 * @file GetActivityByIdHandlerHandleTest.php
 *
 * Tests for GetActivityByIdHandler::handle().
 *
 * @package App\Activity\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler\Tests;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Application\Query\GetActivityByIdQuery;
use App\Activity\Application\Query\Handler\GetActivityByIdHandler;
use App\Activity\Domain\Exception\ActivityNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for GetActivityByIdHandler::handle().
 *
 * Covers: DTO returned for an existing record, exception thrown when no record
 * found, and the correct UUID forwarded to the repository lookup.
 */
#[CoversClass(GetActivityByIdHandler::class)]
#[CoversMethod(GetActivityByIdHandler::class, 'handle')]
final class GetActivityByIdHandlerHandleTest extends GetActivityByIdHandlerTest
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that handle() returns an ActivityDto when the repository finds a matching record.
     */
    public function testHandleReturnsActivityDtoForExistingRecord(): void
    {
        $this->repository->method(constraint: 'findById')
                         ->willReturn(value: $this->makeActivity());

        $result = $this->class->handle(query: new GetActivityByIdQuery(id: self::LOOKUP_UUID));

        $this->assertInstanceOf(expected: ActivityDto::class, actual: $result);
    }

    /**
     * Test that the returned DTO action matches the action of the found entity.
     */
    public function testHandleDtoActionMatchesEntity(): void
    {
        $this->repository->method(constraint: 'findById')
                         ->willReturn(value: $this->makeActivity(action: 'delete', collection: 'posts', item: '5'));

        $result = $this->class->handle(query: new GetActivityByIdQuery(id: self::LOOKUP_UUID));

        $this->assertSame(expected: 'delete', actual: $result->action);
    }

    /**
     * Test that the returned DTO collection matches the collection of the found entity.
     */
    public function testHandleDtoCollectionMatchesEntity(): void
    {
        $this->repository->method(constraint: 'findById')
                         ->willReturn(value: $this->makeActivity(action: 'create', collection: 'articles'));

        $result = $this->class->handle(query: new GetActivityByIdQuery(id: self::LOOKUP_UUID));

        $this->assertSame(expected: 'articles', actual: $result->collection);
    }

    /**
     * Test that handle() forwards the query UUID to the repository's findById() method.
     */
    public function testHandleForwardsIdToRepository(): void
    {
        $this->repository->expects($this->once())
                         ->method(constraint: 'findById')
                         ->with(arguments: self::LOOKUP_UUID)
                         ->willReturn(value: $this->makeActivity());

        $this->class->handle(query: new GetActivityByIdQuery(id: self::LOOKUP_UUID));
    }

    /**
     * Test that handle() throws ActivityNotFoundException when the repository returns null.
     */
    public function testHandleThrowsActivityNotFoundExceptionWhenRecordDoesNotExist(): void
    {
        $this->repository->method(constraint: 'findById')
                         ->willReturn(value: null);

        $this->expectException(exception: ActivityNotFoundException::class);

        $this->class->handle(query: new GetActivityByIdQuery(id: self::LOOKUP_UUID));
    }

    /**
     * Test that the ActivityNotFoundException message contains the UUID that was not found.
     */
    public function testHandleExceptionMessageContainsLookupId(): void
    {
        $this->repository->method(constraint: 'findById')
                         ->willReturn(value: null);

        try {
            $this->class->handle(query: new GetActivityByIdQuery(id: self::LOOKUP_UUID));
            $this->fail(message: 'Expected ActivityNotFoundException was not thrown.');
        } catch (ActivityNotFoundException $e) {
            $this->assertStringContainsString(needle: self::LOOKUP_UUID, haystack: $e->getMessage());
        }
    }
}
