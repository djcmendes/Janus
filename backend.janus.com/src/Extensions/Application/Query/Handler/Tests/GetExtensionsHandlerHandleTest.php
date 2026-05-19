<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler\Tests;

use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Application\Query\GetExtensionsQuery;
use App\Extensions\Application\Query\Handler\GetExtensionsHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: GetExtensionsHandler::class)]
final class GetExtensionsHandlerHandleTest extends GetExtensionsHandlerTest
{
    public function testHandleReturnsDataArray(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findPaginated')->willReturn([$extension]);
        $this->repository->method('countAll')->willReturn(1);

        $result = $this->handler->handle(new GetExtensionsQuery(10, 0));

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(ExtensionDto::class, $result['data'][0]);
    }

    public function testHandleReturnsTotalCount(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeExtension(), $this->makeExtension()]);
        $this->repository->method('countAll')->willReturn(50);

        $result = $this->handler->handle(new GetExtensionsQuery(2, 0));

        $this->assertSame(50, $result['total']);
    }

    public function testHandleReturnsEmptyWhenNoExtensions(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->handler->handle(new GetExtensionsQuery(10, 0));

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['total']);
    }
}
