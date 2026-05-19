<?php

/**
 * @file SaveVersionHandlerTest.php
 *
 * Abstract base for all SaveVersionHandler test suites.
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\SaveVersionHandler;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for SaveVersionHandler tests.
 *
 * Strategy: SaveVersionHandler is final — it is instantiated with a mocked repository interface.
 */
#[CoversClass(className: SaveVersionHandler::class)]
abstract class SaveVersionHandlerTest extends TestCase
{
    /**
     * @var MockObject&VersionRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * @var SaveVersionHandler
     */
    protected SaveVersionHandler $class;

    /**
     * @var ReflectionClass<SaveVersionHandler>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(VersionRepositoryInterface::class);
        $this->class      = new SaveVersionHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(SaveVersionHandler::class);
    }

    protected function tearDown(): void
    {
        unset($this->repository, $this->class, $this->reflection);
    }
}
