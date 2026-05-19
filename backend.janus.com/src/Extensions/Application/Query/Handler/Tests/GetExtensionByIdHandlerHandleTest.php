<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler\Tests;

use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Application\Query\GetExtensionByIdQuery;
use App\Extensions\Application\Query\Handler\GetExtensionByIdHandler;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: GetExtensionByIdHandler::class)]
final class GetExtensionByIdHandlerHandleTest extends GetExtensionByIdHandlerTest
{
    public function testHandleReturnsDtoWhenFound(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);

        $dto = $this->handler->handle(new GetExtensionByIdQuery($extension->getId()));

        $this->assertInstanceOf(ExtensionDto::class, $dto);
        $this->assertSame($extension->getId(), $dto->id);
    }

    public function testHandleThrowsWhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(ExtensionNotFoundException::class);

        $this->handler->handle(new GetExtensionByIdQuery('nonexistent-id'));
    }
}
