<?php

/**
 * @file DeleteCollectionHandlerHandleTest.php
 *
 * Tests for DeleteCollectionHandler::handle().
 *
 * @package App\Collections\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler\Tests;

use App\Collections\Application\Command\DeleteCollectionCommand;
use App\Collections\Application\Command\Handler\DeleteCollectionHandler;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: DeleteCollectionHandler::class)]
#[CoversMethod(DeleteCollectionHandler::class, 'handle')]
final class DeleteCollectionHandlerHandleTest extends DeleteCollectionHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testHandleDeletesFieldsBeforeCollection(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->expects($this->once())->method('deleteByCollection')->with('articles');
        $this->repository->method('delete');

        $this->class->handle(new DeleteCollectionCommand('articles'));
    }

    public function testHandleCallsDeleteOnRepository(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('deleteByCollection');
        $this->repository->expects($this->once())->method('delete');

        $this->class->handle(new DeleteCollectionCommand('articles'));
    }

    public function testHandleReturnsVoid(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());
        $this->fieldRepository->method('deleteByCollection');
        $this->repository->method('delete');

        $result = $this->class->handle(new DeleteCollectionCommand('articles'));

        $this->assertNull($result);
    }

    // Failure paths ────────────────────────────────────────────────

    public function testHandleThrowsCollectionNotFoundException(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $this->expectException(CollectionNotFoundException::class);

        $this->class->handle(new DeleteCollectionCommand('articles'));
    }

    public function testHandleExceptionMessageContainsCollectionName(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        try {
            $this->class->handle(new DeleteCollectionCommand('articles'));
            $this->fail('Expected CollectionNotFoundException was not thrown.');
        } catch (CollectionNotFoundException $e) {
            $this->assertStringContainsString('articles', $e->getMessage());
        }
    }
}
