<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\DeleteExtensionCommand;
use App\Extensions\Application\Command\Handler\DeleteExtensionHandler;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DeleteExtensionHandler::class)]
final class DeleteExtensionHandlerHandleTest extends DeleteExtensionHandlerTest
{
    public function testHandleCallsDeleteWhenFound(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->expects($this->once())->method('delete')->with($extension);

        $this->handler->handle(new DeleteExtensionCommand($extension->getId()));
    }

    public function testHandleThrowsWhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(ExtensionNotFoundException::class);

        $this->handler->handle(new DeleteExtensionCommand('nonexistent-id'));
    }
}
