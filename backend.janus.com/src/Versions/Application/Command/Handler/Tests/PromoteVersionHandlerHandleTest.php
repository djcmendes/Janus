<?php

/**
 * @file PromoteVersionHandlerHandleTest.php
 *
 * Tests for PromoteVersionHandler::handle().
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\PromoteVersionHandler;
use App\Versions\Application\Command\PromoteVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Exception\VersionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that handle() calls promote(), returns a VersionDto, and throws on failures.
 */
#[CoversClass(PromoteVersionHandler::class)]
#[CoversMethod(PromoteVersionHandler::class, 'handle')]
final class PromoteVersionHandlerHandleTest extends PromoteVersionHandlerTest
{
    /**
     * Test that handle() returns a VersionDto on success.
     */
    public function testHandleReturnsVersionDto(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());
        $this->connection->method('executeStatement')->willReturn(1);

        $result = $this->class->handle(new PromoteVersionCommand(self::VERSION_UUID));

        $this->assertInstanceOf(VersionDto::class, $result);
    }

    /**
     * Test that handle() throws VersionNotFoundException when the version does not exist.
     */
    public function testHandleThrowsWhenVersionNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(VersionNotFoundException::class);

        $this->class->handle(new PromoteVersionCommand(self::VERSION_UUID));
    }

    /**
     * Test that handle() calls executeStatement() (via VersionService::promote) when found.
     */
    public function testHandleCallsPromoteWhenVersionFound(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());
        $this->connection->expects($this->once())->method('executeStatement')->willReturn(1);

        $this->class->handle(new PromoteVersionCommand(self::VERSION_UUID));
    }

    /**
     * Test that handle() maps the returned DTO collection from the Version.
     */
    public function testHandleMapsDtoFields(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());
        $this->connection->method('executeStatement')->willReturn(1);

        $dto = $this->class->handle(new PromoteVersionCommand(self::VERSION_UUID));

        $this->assertSame('articles', $dto->collection);
        $this->assertSame('main', $dto->key);
    }
}
