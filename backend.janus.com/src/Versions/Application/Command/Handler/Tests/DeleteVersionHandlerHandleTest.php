<?php

/**
 * @file DeleteVersionHandlerHandleTest.php
 *
 * Tests for DeleteVersionHandler::handle().
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\DeleteVersionCommand;
use App\Versions\Application\Command\Handler\DeleteVersionHandler;
use App\Versions\Domain\Exception\VersionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that handle() deletes via the repository and throws for missing versions.
 */
#[CoversClass(DeleteVersionHandler::class)]
#[CoversMethod(DeleteVersionHandler::class, 'handle')]
final class DeleteVersionHandlerHandleTest extends DeleteVersionHandlerTest
{
    /**
     * Test that handle() calls delete() on the repository exactly once.
     */
    public function testHandleCallsDeleteOnce(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());
        $this->repository->expects($this->once())->method('delete');

        $this->class->handle(new DeleteVersionCommand(self::VERSION_UUID));
    }

    /**
     * Test that handle() throws VersionNotFoundException when no version exists.
     */
    public function testHandleThrowsWhenVersionNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(VersionNotFoundException::class);

        $this->class->handle(new DeleteVersionCommand(self::VERSION_UUID));
    }

    /**
     * Test that handle() does not call delete() when the version is not found.
     */
    public function testHandleDoesNotDeleteWhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);
        $this->repository->expects($this->never())->method('delete');

        try {
            $this->class->handle(new DeleteVersionCommand(self::VERSION_UUID));
        } catch (VersionNotFoundException) {
        }
    }

    /**
     * Test that handle() returns void.
     */
    public function testHandleReturnsVoid(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());
        $this->repository->method('delete');

        $result = $this->class->handle(new DeleteVersionCommand(self::VERSION_UUID));

        $this->assertNull($result);
    }
}
