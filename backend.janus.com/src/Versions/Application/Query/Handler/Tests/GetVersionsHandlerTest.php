<?php

/**
 * @file GetVersionsHandlerTest.php
 *
 * Abstract base for all GetVersionsHandler test suites.
 *
 * @package App\Versions\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler\Tests;

use App\Versions\Application\Query\Handler\GetVersionsHandler;
use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetVersionsHandler tests.
 *
 * Strategy: GetVersionsHandler is final — it is instantiated with a mocked repository interface.
 */
#[CoversClass(GetVersionsHandler::class)]
abstract class GetVersionsHandlerTest extends TestCase
{
    /**
     * Mock of the domain repository interface.
     * @var MockObject&VersionRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var GetVersionsHandler
     */
    protected GetVersionsHandler $class;

    /**
     * @var ReflectionClass<GetVersionsHandler>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(VersionRepositoryInterface::class);
        $this->class      = new GetVersionsHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(GetVersionsHandler::class);
    }

    protected function tearDown(): void
    {
        unset($this->repository, $this->class, $this->reflection);
    }

    /**
     * Creates a Version entity for stubbing repository results.
     *
     * @return Version A hydrated entity with deterministic test values.
     */
    protected function makeVersion(): Version
    {
        return new Version('articles', 'item-uuid-1', 'main', ['title' => 'Hello']);
    }
}
