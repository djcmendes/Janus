<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Fields\Application\Command\CreateFieldCommand;
use App\Fields\Application\Command\Handler\CreateFieldHandler;
use App\Fields\Application\DTO\FieldDto;
use App\Fields\Domain\Exception\FieldAlreadyExistsException;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Entity\FieldMeta;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CreateFieldHandler::class)]
final class CreateFieldHandlerHandleTest extends CreateFieldHandlerTest
{
    private function makeCmd(string $type = 'string', string $field = 'title'): CreateFieldCommand
    {
        return new CreateFieldCommand('articles', $field, $type);
    }

    private function makeExistingField(): FieldMeta
    {
        return FieldMeta::reconstitute(
            id:         'aaaaaaaa-0000-7000-8000-000000000001',
            collection: 'articles',
            field:      'title',
            type:       FieldType::STRING,
            label:      null,
            note:       null,
            required:   false,
            readonly:   false,
            hidden:     false,
            sortOrder:  0,
            interface:  null,
            options:    null,
            createdAt:  new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:  null,
        );
    }

    public function testHandleReturnsFieldDto(): void
    {
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('findByCollectionAndField')->willReturn(null);
        $this->fieldRepository->method('save');
        $this->connection->method('executeStatement')->willReturn(1);

        $dto = $this->handler->handle($this->makeCmd());

        $this->assertInstanceOf(FieldDto::class, $dto);
    }

    public function testHandleThrowsWhenCollectionNotFound(): void
    {
        $this->expectException(CollectionNotFoundException::class);
        $this->collectionRepository->method('findByName')->willReturn(null);

        $this->handler->handle($this->makeCmd());
    }

    public function testHandleThrowsWhenFieldAlreadyExists(): void
    {
        $this->expectException(FieldAlreadyExistsException::class);
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('findByCollectionAndField')->willReturn($this->makeExistingField());

        $this->handler->handle($this->makeCmd());
    }

    public function testHandleThrowsOnInvalidType(): void
    {
        $this->expectException(\ValueError::class);
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('findByCollectionAndField')->willReturn(null);

        $this->handler->handle($this->makeCmd(type: 'invalid-type'));
    }

    public function testHandleSkipsAddColumnForAliasType(): void
    {
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('findByCollectionAndField')->willReturn(null);
        $this->fieldRepository->method('save');

        // No DDL for alias type
        $this->connection->expects($this->never())->method('executeStatement');

        $this->handler->handle($this->makeCmd(type: 'alias'));
    }

    public function testHandleCallsAddColumnForNonAliasType(): void
    {
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('findByCollectionAndField')->willReturn(null);
        $this->fieldRepository->method('save');

        // SchemaManagerService calls executeStatement with ALTER TABLE DDL
        $this->connection->expects($this->once())->method('executeStatement');

        $this->handler->handle($this->makeCmd(type: 'string'));
    }

    public function testHandleSavesFieldToRepository(): void
    {
        $this->collectionRepository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('findByCollectionAndField')->willReturn(null);
        $this->connection->method('executeStatement')->willReturn(1);

        $this->fieldRepository->expects($this->once())->method('save');

        $this->handler->handle($this->makeCmd());
    }
}
