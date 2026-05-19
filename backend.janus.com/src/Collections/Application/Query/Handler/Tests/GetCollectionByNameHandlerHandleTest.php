<?php

/**
 * @file GetCollectionByNameHandlerHandleTest.php
 *
 * Tests for GetCollectionByNameHandler::handle().
 *
 * @package App\Collections\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query\Handler\Tests;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Application\Query\GetCollectionByNameQuery;
use App\Collections\Application\Query\Handler\GetCollectionByNameHandler;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: GetCollectionByNameHandler::class)]
#[CoversMethod(GetCollectionByNameHandler::class, 'handle')]
final class GetCollectionByNameHandlerHandleTest extends GetCollectionByNameHandlerTest
{
    private const string LOOKUP_NAME = 'articles';

    // Happy path ───────────────────────────────────────────────────

    public function testHandleReturnsCollectionDto(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());

        $result = $this->class->handle(new GetCollectionByNameQuery(self::LOOKUP_NAME));

        $this->assertInstanceOf(CollectionDto::class, $result);
    }

    public function testHandleDtoNameMatchesLookedUpCollection(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());

        $dto = $this->class->handle(new GetCollectionByNameQuery(self::LOOKUP_NAME));

        $this->assertSame('articles', $dto->name);
    }

    // Failure paths ────────────────────────────────────────────────

    public function testHandleThrowsCollectionNotFoundException(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $this->expectException(CollectionNotFoundException::class);

        $this->class->handle(new GetCollectionByNameQuery(self::LOOKUP_NAME));
    }

    public function testHandleExceptionMessageContainsLookupName(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        try {
            $this->class->handle(new GetCollectionByNameQuery(self::LOOKUP_NAME));
            $this->fail('Expected CollectionNotFoundException was not thrown.');
        } catch (CollectionNotFoundException $e) {
            $this->assertStringContainsString(self::LOOKUP_NAME, $e->getMessage());
        }
    }
}
