<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldsByCollectionQuery;
use App\Fields\Application\Query\Handler\GetFieldsByCollectionHandler;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: GetFieldsByCollectionHandler::class)]
final class GetFieldsByCollectionHandlerHandleTest extends GetFieldsByCollectionHandlerTest
{
    public function testHandleReturnsDtoArray(): void
    {
        $field = $this->makeFieldMeta('articles');
        $this->repository->method('findByCollection')->willReturn([$field]);

        $result = $this->handler->handle(new GetFieldsByCollectionQuery('articles'));

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(FieldDto::class, $result[0]);
    }

    public function testHandleReturnsEmptyArrayWhenNoFields(): void
    {
        $this->repository->method('findByCollection')->willReturn([]);

        $result = $this->handler->handle(new GetFieldsByCollectionQuery('empty-collection'));

        $this->assertSame([], $result);
    }

    public function testHandlePassesCollectionToRepository(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findByCollection')
            ->with('articles')
            ->willReturn([]);

        $this->handler->handle(new GetFieldsByCollectionQuery('articles'));
    }
}
