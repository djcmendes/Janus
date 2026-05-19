<?php

/**
 * @file CreateCollectionHandlerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all CreateCollectionHandler test cases.
 *
 * Strategy: CreateCollectionHandler is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its dependencies — CollectionMetaRepositoryInterface
 * and FieldMetaRepositoryInterface (interfaces) — are mocked normally. SchemaManagerService
 * is final with a non-final Connection dependency, which is mocked to suppress real DDL.
 *
 * @package App\Collections\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler\Tests;

use App\Collections\Application\Command\Handler\CreateCollectionHandler;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * CreateCollectionHandler test suites.
 */
#[CoversClass(className: CreateCollectionHandler::class)]
abstract class CreateCollectionHandlerTest extends TestCase
{
    /**
     * Mock of the collection repository interface.
     * @var MockObject&CollectionMetaRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * Mock of the field repository interface.
     * @var MockObject&FieldMetaRepositoryInterface
     */
    protected MockObject $fieldRepository;

    /**
     * Mock of the DBAL connection — prevents real DDL from executing.
     * @var MockObject&Connection
     */
    protected MockObject $connection;

    /**
     * Real SchemaManagerService instance backed by a mocked Connection.
     * @var SchemaManagerService
     */
    protected SchemaManagerService $schemaManager;

    /**
     * The system under test.
     * @var CreateCollectionHandler
     */
    protected CreateCollectionHandler $class;

    /**
     * Reflection of CreateCollectionHandler class.
     * @var ReflectionClass<CreateCollectionHandler>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->repository      = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->fieldRepository = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->connection      = $this->createMock(Connection::class);
        $this->schemaManager   = new SchemaManagerService($this->connection);

        $this->class      = new CreateCollectionHandler(
            $this->repository,
            $this->fieldRepository,
            $this->schemaManager,
        );
        $this->reflection = new ReflectionClass(CreateCollectionHandler::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset(
            $this->repository,
            $this->fieldRepository,
            $this->connection,
            $this->schemaManager,
            $this->class,
            $this->reflection,
        );
    }
}
