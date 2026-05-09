<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command\Handler\Tests;

use App\Extensions\Application\Command\Handler\UpdateExtensionHandler;
use App\Extensions\Application\Command\UpdateExtensionCommand;
use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UpdateExtensionHandler::class)]
final class UpdateExtensionHandlerHandleTest extends UpdateExtensionHandlerTest
{
    public function testHandleThrowsWhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(ExtensionNotFoundException::class);

        $this->handler->handle(new UpdateExtensionCommand(
            id:      'nonexistent-id',
            enabled: UpdateExtensionCommand::UNCHANGED,
            version: UpdateExtensionCommand::UNCHANGED,
            meta:    UpdateExtensionCommand::UNCHANGED,
        ));
    }

    public function testHandleCallsSaveAndReturnsDto(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->expects($this->once())->method('save');

        $result = $this->handler->handle(new UpdateExtensionCommand(
            id:      $extension->getId(),
            enabled: UpdateExtensionCommand::UNCHANGED,
            version: UpdateExtensionCommand::UNCHANGED,
            meta:    UpdateExtensionCommand::UNCHANGED,
        ));

        $this->assertInstanceOf(ExtensionDto::class, $result);
    }

    public function testHandleUpdatesEnabledField(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->method('save');

        $this->handler->handle(new UpdateExtensionCommand(
            id:      $extension->getId(),
            enabled: true,
            version: UpdateExtensionCommand::UNCHANGED,
            meta:    UpdateExtensionCommand::UNCHANGED,
        ));

        $this->assertTrue($extension->isEnabled());
    }

    public function testHandleUpdatesVersionField(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->method('save');

        $this->handler->handle(new UpdateExtensionCommand(
            id:      $extension->getId(),
            enabled: UpdateExtensionCommand::UNCHANGED,
            version: '9.9.9',
            meta:    UpdateExtensionCommand::UNCHANGED,
        ));

        $this->assertSame('9.9.9', $extension->getVersion());
    }

    public function testHandleUpdatesMetaField(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->method('save');

        $this->handler->handle(new UpdateExtensionCommand(
            id:      $extension->getId(),
            enabled: UpdateExtensionCommand::UNCHANGED,
            version: UpdateExtensionCommand::UNCHANGED,
            meta:    ['entry' => 'new.js'],
        ));

        $this->assertSame(['entry' => 'new.js'], $extension->getMeta());
    }

    public function testHandleSkipsUnchangedFields(): void
    {
        $extension = $this->makeExtension();
        $this->repository->method('findById')->willReturn($extension);
        $this->repository->method('save');

        $this->handler->handle(new UpdateExtensionCommand(
            id:      $extension->getId(),
            enabled: UpdateExtensionCommand::UNCHANGED,
            version: UpdateExtensionCommand::UNCHANGED,
            meta:    UpdateExtensionCommand::UNCHANGED,
        ));

        $this->assertFalse($extension->isEnabled());
        $this->assertSame('1.0.0', $extension->getVersion());
        $this->assertNull($extension->getMeta());
    }
}
