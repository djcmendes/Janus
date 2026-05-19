<?php

/**
 * @file DeleteVersionHandlerTest.php
 *
 * Abstract base for all DeleteVersionHandler test suites.
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\DeleteVersionHandler;
use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for DeleteVersionHandler tests.
 */
#[CoversClass(className: DeleteVersionHandler::class)]
abstract class DeleteVersionHandlerTest extends TestCase
{
    /** @var string */
    protected const string VERSION_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * @var MockObject&VersionRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * @var DeleteVersionHandler
     */
    protected DeleteVersionHandler $class;

    /**
     * @var ReflectionClass<DeleteVersionHandler>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(VersionRepositoryInterface::class);
        $this->class      = new DeleteVersionHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(DeleteVersionHandler::class);
    }

    protected function tearDown(): void
    {
        unset($this->repository, $this->class, $this->reflection);
    }

    protected function makeVersion(): Version
    {
        return new Version('articles', 'item-uuid-1', 'main', []);
    }
}
