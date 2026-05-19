<?php

/**
 * @file GetCollectionsHandlerHandleTest.php
 *
 * Tests for GetCollectionsHandler::handle().
 *
 * @package App\Collections\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query\Handler\Tests;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Application\Query\GetCollectionsQuery;
use App\Collections\Application\Query\Handler\GetCollectionsHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: GetCollectionsHandler::class)]
#[CoversMethod(GetCollectionsHandler::class, 'handle')]
final class GetCollectionsHandlerHandleTest extends GetCollectionsHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testHandleReturnsArrayWithDataAndTotal(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('count')->willReturn(0);

        $result = $this->class->handle(new GetCollectionsQuery(limit: 25, offset: 0));

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    public function testHandleMapsEntitiesToDtos(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeCollectionMeta()]);
        $this->repository->method('count')->willReturn(1);

        $result = $this->class->handle(new GetCollectionsQuery(limit: 25, offset: 0));

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(CollectionDto::class, $result['data'][0]);
    }

    public function testHandleReturnsTotalFromRepository(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('count')->willReturn(42);

        $result = $this->class->handle(new GetCollectionsQuery(limit: 25, offset: 0));

        $this->assertSame(42, $result['total']);
    }

    public function testHandleForwardsPaginationParametersToRepository(): void
    {
        $this->repository
             ->expects($this->once())
             ->method('findPaginated')
             ->with(10, 20)
             ->willReturn([]);

        $this->repository->method('count')->willReturn(0);

        $this->class->handle(new GetCollectionsQuery(limit: 10, offset: 20));
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testHandleReturnsEmptyDataAndZeroTotalWhenNoRecords(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('count')->willReturn(0);

        $result = $this->class->handle(new GetCollectionsQuery(limit: 25, offset: 0));

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['total']);
    }
}
