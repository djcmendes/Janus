<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldsQuery;
use App\Fields\Application\Query\Handler\GetFieldsHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: GetFieldsHandler::class)]
final class GetFieldsHandlerHandleTest extends GetFieldsHandlerTest
{
    public function testHandleReturnsDataAndTotalKeys(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->handler->handle(new GetFieldsQuery(25, 0));

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    public function testHandleReturnsMappedDtos(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findPaginated')->willReturn([$field]);
        $this->repository->method('countAll')->willReturn(1);

        $result = $this->handler->handle(new GetFieldsQuery(25, 0));

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(FieldDto::class, $result['data'][0]);
    }

    public function testHandleReturnsTotalCount(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(42);

        $result = $this->handler->handle(new GetFieldsQuery(25, 0));

        $this->assertSame(42, $result['total']);
    }

    public function testHandlePassesLimitAndOffsetToRepository(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(10, 20)
            ->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $this->handler->handle(new GetFieldsQuery(10, 20));
    }
}
