<?php

/**
 * @file UpdateVersionHandlerHandleTest.php
 *
 * Tests for UpdateVersionHandler::handle().
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\UpdateVersionHandler;
use App\Versions\Application\Command\UpdateVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Exception\VersionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that handle() applies partial updates and throws for missing versions.
 */
#[CoversClass(UpdateVersionHandler::class)]
#[CoversMethod(UpdateVersionHandler::class, 'handle')]
final class UpdateVersionHandlerHandleTest extends UpdateVersionHandlerTest
{
    /**
     * Test that handle() returns a VersionDto on success.
     */
    public function testHandleReturnsVersionDto(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());
        $this->repository->method('save');

        $result = $this->class->handle(new UpdateVersionCommand(self::VERSION_UUID));

        $this->assertInstanceOf(VersionDto::class, $result);
    }

    /**
     * Test that handle() throws VersionNotFoundException when the version does not exist.
     */
    public function testHandleThrowsWhenVersionNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(VersionNotFoundException::class);

        $this->class->handle(new UpdateVersionCommand(self::VERSION_UUID));
    }

    /**
     * Test that handle() updates the key when a new key is provided.
     */
    public function testHandleUpdatesKeyWhenProvided(): void
    {
        $version = $this->makeVersion();
        $this->repository->method('findById')->willReturn($version);
        $this->repository->method('save');

        $dto = $this->class->handle(new UpdateVersionCommand(self::VERSION_UUID, key: 'draft'));

        $this->assertSame('draft', $dto->key);
    }

    /**
     * Test that handle() does not change the key when UNCHANGED sentinel is used.
     */
    public function testHandlePreservesKeyWhenUnchanged(): void
    {
        $version = $this->makeVersion();
        $this->repository->method('findById')->willReturn($version);
        $this->repository->method('save');

        $dto = $this->class->handle(new UpdateVersionCommand(self::VERSION_UUID));

        $this->assertSame('main', $dto->key);
    }

    /**
     * Test that handle() calls save() on the repository exactly once.
     */
    public function testHandleCallsSaveOnce(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());
        $this->repository->expects($this->once())->method('save');

        $this->class->handle(new UpdateVersionCommand(self::VERSION_UUID));
    }
}
