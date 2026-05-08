<?php

/**
 * @file CreateCollectionHandlerHandleTest.php
 *
 * Tests for CreateCollectionHandler::handle().
 *
 * @package App\Collections\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler\Tests;

use App\Collections\Application\Command\CreateCollectionCommand;
use App\Collections\Application\Command\Handler\CreateCollectionHandler;
use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Exception\CollectionAlreadyExistsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(CreateCollectionHandler::class)]
#[CoversMethod(CreateCollectionHandler::class, 'handle')]
final class CreateCollectionHandlerHandleTest extends CreateCollectionHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testHandleReturnsCollectionDto(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('save');
        $this->fieldRepository->method('save');

        $result = $this->class->handle(new CreateCollectionCommand(name: 'articles'));

        $this->assertInstanceOf(CollectionDto::class, $result);
    }

    public function testHandleDtoNameMatchesCommand(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('save');
        $this->fieldRepository->method('save');

        $dto = $this->class->handle(new CreateCollectionCommand(name: 'articles'));

        $this->assertSame('articles', $dto->name);
    }

    public function testHandleCallsSaveOnRepository(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->expects($this->once())->method('save');
        $this->fieldRepository->method('save');

        $this->class->handle(new CreateCollectionCommand(name: 'articles'));
    }

    public function testHandleCallsSaveOnFieldRepository(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('save');
        $this->fieldRepository->expects($this->once())->method('save');

        $this->class->handle(new CreateCollectionCommand(name: 'articles'));
    }

    // Failure paths ────────────────────────────────────────────────

    public function testHandleThrowsWhenCollectionAlreadyExists(): void
    {
        $existing = new \App\Collections\Domain\Entity\CollectionMeta('articles');
        $this->repository->method('findByName')->willReturn($existing);

        $this->expectException(CollectionAlreadyExistsException::class);

        $this->class->handle(new CreateCollectionCommand(name: 'articles'));
    }

    public function testHandleExceptionMessageContainsCollectionName(): void
    {
        $existing = new \App\Collections\Domain\Entity\CollectionMeta('articles');
        $this->repository->method('findByName')->willReturn($existing);

        try {
            $this->class->handle(new CreateCollectionCommand(name: 'articles'));
            $this->fail('Expected CollectionAlreadyExistsException was not thrown.');
        } catch (CollectionAlreadyExistsException $e) {
            $this->assertStringContainsString('articles', $e->getMessage());
        }
    }
}
