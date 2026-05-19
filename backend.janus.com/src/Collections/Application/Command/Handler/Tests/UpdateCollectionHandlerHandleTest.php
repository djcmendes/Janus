<?php

/**
 * @file UpdateCollectionHandlerHandleTest.php
 *
 * Tests for UpdateCollectionHandler::handle().
 *
 * @package App\Collections\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler\Tests;

use App\Collections\Application\Command\Handler\UpdateCollectionHandler;
use App\Collections\Application\Command\UpdateCollectionCommand;
use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: UpdateCollectionHandler::class)]
#[CoversMethod(UpdateCollectionHandler::class, 'handle')]
final class UpdateCollectionHandlerHandleTest extends UpdateCollectionHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testHandleReturnsCollectionDto(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->repository->method('save');

        $result = $this->class->handle(new UpdateCollectionCommand(name: 'articles'));

        $this->assertInstanceOf(CollectionDto::class, $result);
    }

    public function testHandleAppliesLabelUpdate(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);
        $this->repository->method('save');

        $dto = $this->class->handle(new UpdateCollectionCommand(name: 'articles', label: 'Updated Articles'));

        $this->assertSame('Updated Articles', $dto->label);
    }

    public function testHandleAppliesHiddenUpdate(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);
        $this->repository->method('save');

        $dto = $this->class->handle(new UpdateCollectionCommand(name: 'articles', hidden: true));

        $this->assertTrue($dto->hidden);
    }

    public function testHandleCallsSaveOnRepository(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->repository->expects($this->once())->method('save');

        $this->class->handle(new UpdateCollectionCommand(name: 'articles'));
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testHandleDoesNotChangeLabelWhenNull(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);
        $this->repository->method('save');

        $dto = $this->class->handle(new UpdateCollectionCommand(name: 'articles', label: null));

        $this->assertSame('Articles', $dto->label);
    }

    public function testHandleDoesNotChangeIconWhenUnchanged(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);
        $this->repository->method('save');

        $dto = $this->class->handle(new UpdateCollectionCommand(
            name: 'articles',
            icon: UpdateCollectionCommand::UNCHANGED,
        ));

        $this->assertSame('mdi-file', $dto->icon);
    }

    // Failure paths ────────────────────────────────────────────────

    public function testHandleThrowsCollectionNotFoundException(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $this->expectException(CollectionNotFoundException::class);

        $this->class->handle(new UpdateCollectionCommand(name: 'articles'));
    }

    public function testHandleExceptionMessageContainsCollectionName(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        try {
            $this->class->handle(new UpdateCollectionCommand(name: 'articles'));
            $this->fail('Expected CollectionNotFoundException was not thrown.');
        } catch (CollectionNotFoundException $e) {
            $this->assertStringContainsString('articles', $e->getMessage());
        }
    }
}
