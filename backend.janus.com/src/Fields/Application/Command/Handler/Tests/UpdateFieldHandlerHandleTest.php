<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Fields\Application\Command\Handler\UpdateFieldHandler;
use App\Fields\Application\Command\UpdateFieldCommand;
use App\Fields\Application\DTO\FieldDto;
use App\Fields\Domain\Exception\FieldNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: UpdateFieldHandler::class)]
final class UpdateFieldHandlerHandleTest extends UpdateFieldHandlerTest
{
    public function testHandleReturnsDto(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('save');

        $result = $this->handler->handle(new UpdateFieldCommand('articles', 'title', label: 'New Label'));

        $this->assertInstanceOf(FieldDto::class, $result);
    }

    public function testHandleThrowsWhenNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $this->repository->method('findByCollectionAndField')->willReturn(null);

        $this->handler->handle(new UpdateFieldCommand('articles', 'missing'));
    }

    public function testHandleUpdatesLabelWhenProvided(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('save');

        $dto = $this->handler->handle(new UpdateFieldCommand('articles', 'title', label: 'Updated Label'));

        $this->assertSame('Updated Label', $dto->label);
    }

    public function testHandleSkipsLabelWhenUnchanged(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('save');

        $dto = $this->handler->handle(new UpdateFieldCommand('articles', 'title'));

        $this->assertSame('Old Label', $dto->label);
    }

    public function testHandleUpdatesRequiredWhenProvided(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('save');

        $dto = $this->handler->handle(new UpdateFieldCommand('articles', 'title', required: true));

        $this->assertTrue($dto->required);
    }

    public function testHandleUpdatesHiddenWhenProvided(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);
        $this->repository->method('save');

        $dto = $this->handler->handle(new UpdateFieldCommand('articles', 'title', hidden: true));

        $this->assertTrue($dto->hidden);
    }

    public function testHandleSavesFieldToRepository(): void
    {
        $field = $this->makeFieldMeta();
        $this->repository->method('findByCollectionAndField')->willReturn($field);

        $this->repository->expects($this->once())->method('save');

        $this->handler->handle(new UpdateFieldCommand('articles', 'title'));
    }
}
