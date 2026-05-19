<?php

/**
 * @file SaveVersionHandlerHandleTest.php
 *
 * Tests for SaveVersionHandler::handle().
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\SaveVersionHandler;
use App\Versions\Application\Command\SaveVersionCommand;
use App\Versions\Application\DTO\VersionDto;
use App\Versions\Domain\Exception\VersionAlreadyExistsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that handle() creates a Version DTO, enforces uniqueness, and persists via the repository.
 */
#[CoversClass(className: SaveVersionHandler::class)]
#[CoversMethod(SaveVersionHandler::class, 'handle')]
final class SaveVersionHandlerHandleTest extends SaveVersionHandlerTest
{
    private function makeCommand(): SaveVersionCommand
    {
        return new SaveVersionCommand(
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       ['title' => 'Hello'],
            delta:      null,
            userId:     'bbbbbbbb-0000-7000-8000-000000000002',
        );
    }

    /**
     * Test that handle() returns a VersionDto on success.
     */
    public function testHandleReturnsVersionDto(): void
    {
        $this->repository->method('findByCollectionItemAndKey')->willReturn(null);
        $this->repository->method('save');

        $result = $this->class->handle($this->makeCommand());

        $this->assertInstanceOf(VersionDto::class, $result);
    }

    /**
     * Test that handle() calls save() on the repository exactly once.
     */
    public function testHandleCallsSaveOnce(): void
    {
        $this->repository->method('findByCollectionItemAndKey')->willReturn(null);
        $this->repository->expects($this->once())->method('save');

        $this->class->handle($this->makeCommand());
    }

    /**
     * Test that handle() throws VersionAlreadyExistsException when a duplicate exists.
     */
    public function testHandleThrowsWhenVersionAlreadyExists(): void
    {
        $existing = new \App\Versions\Domain\Entity\Version('articles', 'item-uuid-1', 'main', []);
        $this->repository->method('findByCollectionItemAndKey')->willReturn($existing);

        $this->expectException(VersionAlreadyExistsException::class);

        $this->class->handle($this->makeCommand());
    }

    /**
     * Test that handle() does not call save() when a duplicate exists.
     */
    public function testHandleDoesNotSaveWhenDuplicateExists(): void
    {
        $existing = new \App\Versions\Domain\Entity\Version('articles', 'item-uuid-1', 'main', []);
        $this->repository->method('findByCollectionItemAndKey')->willReturn($existing);
        $this->repository->expects($this->never())->method('save');

        try {
            $this->class->handle($this->makeCommand());
        } catch (VersionAlreadyExistsException) {
        }
    }

    /**
     * Test that the returned DTO has the correct collection and key.
     */
    public function testHandleReturnsDtoWithCorrectFields(): void
    {
        $this->repository->method('findByCollectionItemAndKey')->willReturn(null);
        $this->repository->method('save');

        $dto = $this->class->handle($this->makeCommand());

        $this->assertSame('articles', $dto->collection);
        $this->assertSame('main', $dto->key);
    }
}
