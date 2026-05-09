<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\RegisterExtensionHandler;
use App\Extensions\Application\Command\RegisterExtensionCommand;
use App\Extensions\Application\DTO\ExtensionDto;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RegisterExtensionHandler::class)]
final class RegisterExtensionHandlerHandleTest extends RegisterExtensionHandlerTest
{
    public function testHandleCallsSave(): void
    {
        $this->repository->expects($this->once())->method('save');

        $this->handler->handle(new RegisterExtensionCommand('my-hook', 'hook', '1.0.0'));
    }

    public function testHandleReturnsDto(): void
    {
        $this->repository->method('save');

        $dto = $this->handler->handle(new RegisterExtensionCommand('my-hook', 'hook', '1.0.0'));

        $this->assertInstanceOf(ExtensionDto::class, $dto);
    }

    public function testHandleMapsName(): void
    {
        $this->repository->method('save');

        $dto = $this->handler->handle(new RegisterExtensionCommand('my-layout', 'layout', '2.0.0'));

        $this->assertSame('my-layout', $dto->name);
    }

    public function testHandleMapsTypeAsString(): void
    {
        $this->repository->method('save');

        $dto = $this->handler->handle(new RegisterExtensionCommand('my-hook', 'hook', '1.0.0'));

        $this->assertSame('hook', $dto->type);
    }

    public function testHandleWithEnabledTrue(): void
    {
        $this->repository->method('save');

        $dto = $this->handler->handle(new RegisterExtensionCommand('my-hook', 'hook', '1.0.0', enabled: true));

        $this->assertTrue($dto->enabled);
    }

    public function testHandleDefaultEnabledIsFalse(): void
    {
        $this->repository->method('save');

        $dto = $this->handler->handle(new RegisterExtensionCommand('my-hook', 'hook', '1.0.0'));

        $this->assertFalse($dto->enabled);
    }
}
