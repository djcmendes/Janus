<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldByCollectionAndNameQuery;
use App\Fields\Application\Query\Handler\GetFieldByCollectionAndNameHandler;
use App\Fields\Domain\Exception\FieldNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetFieldByCollectionAndNameHandler::class)]
final class GetFieldByCollectionAndNameHandlerHandleTest extends GetFieldByCollectionAndNameHandlerTest
{
    public function testHandleReturnsDto(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);

        $result = $this->handler->handle(new GetFieldByCollectionAndNameQuery('articles', 'title'));

        $this->assertInstanceOf(FieldDto::class, $result);
    }

    public function testHandleThrowsWhenNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $this->repository->method('findByCollectionAndField')->willReturn(null);

        $this->handler->handle(new GetFieldByCollectionAndNameQuery('articles', 'missing'));
    }

    public function testHandlePassesCollectionAndFieldToRepository(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository
            ->expects($this->once())
            ->method('findByCollectionAndField')
            ->with('articles', 'title')
            ->willReturn($field);

        $this->handler->handle(new GetFieldByCollectionAndNameQuery('articles', 'title'));
    }
}
