<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Fields\Application\Command\DeleteFieldCommand;
use App\Fields\Application\Command\Handler\DeleteFieldHandler;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Exception\FieldNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: DeleteFieldHandler::class)]
final class DeleteFieldHandlerHandleTest extends DeleteFieldHandlerTest
{
    public function testHandleDeletesField(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->connection->method('executeStatement')->willReturn(1);

        $this->repository->expects($this->once())->method('delete');

        $this->handler->handle(new DeleteFieldCommand('articles', 'title'));
    }

    public function testHandleThrowsWhenNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $this->repository->method('findByCollectionAndField')->willReturn(null);

        $this->handler->handle(new DeleteFieldCommand('articles', 'missing'));
    }

    public function testHandleDropsColumnForNonAliasType(): void
    {
        $field = $this->makeFieldMeta(FieldType::STRING);
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('delete');

        // SchemaManagerService calls executeStatement with DROP COLUMN DDL
        $this->connection->expects($this->once())->method('executeStatement');

        $this->handler->handle(new DeleteFieldCommand('articles', 'title'));
    }

    public function testHandleSkipsDropColumnForAliasType(): void
    {
        $field = $this->makeFieldMeta(FieldType::ALIAS);
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('delete');

        // No DDL for alias type
        $this->connection->expects($this->never())->method('executeStatement');

        $this->handler->handle(new DeleteFieldCommand('articles', 'title'));
    }
}
