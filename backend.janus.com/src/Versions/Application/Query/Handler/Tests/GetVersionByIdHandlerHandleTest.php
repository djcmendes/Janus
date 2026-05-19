<?php

/**
 * @file GetVersionByIdHandlerHandleTest.php
 *
 * Tests for GetVersionByIdHandler::handle().
 *
 * @package App\Versions\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler\Tests;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Application\Query\GetVersionByIdQuery;
use App\Versions\Application\Query\Handler\GetVersionByIdHandler;
use App\Versions\Domain\Exception\VersionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that handle() returns a VersionDto for found records and throws for missing ones.
 */
#[CoversClass(className: GetVersionByIdHandler::class)]
#[CoversMethod(GetVersionByIdHandler::class, 'handle')]
final class GetVersionByIdHandlerHandleTest extends GetVersionByIdHandlerTest
{
    /**
     * Test that handle() returns a VersionDto when the record exists.
     */
    public function testHandleReturnsDtoForExistingVersion(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());

        $result = $this->class->handle(new GetVersionByIdQuery(self::LOOKUP_UUID));

        $this->assertInstanceOf(VersionDto::class, $result);
    }

    /**
     * Test that handle() throws VersionNotFoundException when no record exists.
     */
    public function testHandleThrowsWhenVersionNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(VersionNotFoundException::class);

        $this->class->handle(new GetVersionByIdQuery(self::LOOKUP_UUID));
    }

    /**
     * Test that handle() passes the UUID from the query to the repository.
     */
    public function testHandlePassesIdToRepository(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with(self::LOOKUP_UUID)
            ->willReturn($this->makeVersion());

        $this->class->handle(new GetVersionByIdQuery(self::LOOKUP_UUID));
    }

    /**
     * Test that handle() maps the Version collection onto the returned DTO.
     */
    public function testHandleMapsDtoFieldsFromVersion(): void
    {
        $this->repository->method('findById')->willReturn($this->makeVersion());

        $dto = $this->class->handle(new GetVersionByIdQuery(self::LOOKUP_UUID));

        $this->assertSame('articles', $dto->collection);
        $this->assertSame('main', $dto->key);
    }
}
