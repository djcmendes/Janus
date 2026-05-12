<?php

/**
 * @file PromoteVersionHandlerTest.php
 *
 * Abstract base for all PromoteVersionHandler test suites.
 *
 * @package App\Versions\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Command\Handler\Tests;

use App\Versions\Application\Command\Handler\PromoteVersionHandler;
use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use App\Versions\Domain\Service\VersionService;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for PromoteVersionHandler tests.
 */
#[CoversClass(PromoteVersionHandler::class)]
abstract class PromoteVersionHandlerTest extends TestCase
{
    /** @var string */
    protected const string VERSION_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * @var MockObject&VersionRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * Mock of the DBAL connection — controls promote() behaviour in VersionService.
     * @var MockObject&Connection
     */
    protected MockObject $connection;

    /**
     * Real VersionService backed by the mocked connection.
     * @var VersionService
     */
    protected VersionService $versionService;

    /**
     * @var PromoteVersionHandler
     */
    protected PromoteVersionHandler $class;

    /**
     * @var ReflectionClass<PromoteVersionHandler>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->repository     = $this->createMock(VersionRepositoryInterface::class);
        $this->connection     = $this->createMock(Connection::class);
        $this->versionService = new VersionService(connection: $this->connection);
        $this->class          = new PromoteVersionHandler(
            repository:     $this->repository,
            versionService: $this->versionService,
        );
        $this->reflection = new ReflectionClass(PromoteVersionHandler::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->repository,
            $this->connection,
            $this->versionService,
            $this->class,
            $this->reflection,
        );
    }

    protected function makeVersion(): Version
    {
        return new Version('articles', 'item-uuid-1', 'main', ['title' => 'Hello']);
    }
}
