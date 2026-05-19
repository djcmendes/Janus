<?php

/**
 * @file ApplySchemaHandlerTest.php
 *
 * Abstract base for ApplySchemaHandler test suites.
 *
 * Strategy: All 13 constructor dependencies of ApplySchemaHandler are either
 * repository interfaces (mocked) or final handler/service classes (wired as real
 * objects backed by the same mocked interfaces). PHPUnit 12 cannot double final
 * classes, so every concrete class is instantiated directly rather than mocked.
 *
 * @package App\Schema\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Command\Handler\Tests;

use App\Collections\Application\Command\Handler\CreateCollectionHandler;
use App\Collections\Application\Command\Handler\DeleteCollectionHandler;
use App\Collections\Application\Command\Handler\UpdateCollectionHandler;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\Handler\CreateFieldHandler;
use App\Fields\Application\Command\Handler\DeleteFieldHandler;
use App\Fields\Application\Command\Handler\UpdateFieldHandler;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Relations\Application\Command\Handler\CreateRelationHandler;
use App\Relations\Application\Command\Handler\DeleteRelationHandler;
use App\Relations\Domain\Repository\RelationRepositoryInterface;
use App\Schema\Application\Command\ApplySchemaCommand;
use App\Schema\Application\Command\Handler\ApplySchemaHandler;
use App\Schema\Domain\Service\SchemaDiffService;
use App\Schema\Domain\Service\SchemaSnapshotService;
use App\Schema\Domain\Service\SchemaSnapshotServiceInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Shared setup and helpers for all ApplySchemaHandler test suites.
 *
 * Repository interfaces are mocked. All final handler/service classes are
 * constructed as real objects backed by those mocked interfaces.
 */
#[CoversClass(className: ApplySchemaHandler::class)]
abstract class ApplySchemaHandlerTest extends TestCase
{
    /** @var MockObject&CollectionMetaRepositoryInterface */
    protected MockObject $collectionRepository;

    /** @var MockObject&FieldMetaRepositoryInterface */
    protected MockObject $fieldRepository;

    /** @var MockObject&RelationRepositoryInterface */
    protected MockObject $relationRepository;

    /** @var MockObject&Connection */
    protected MockObject $connection;

    /** @var ApplySchemaHandler */
    protected ApplySchemaHandler $class;

    /** @var array<mixed> Mutable return value for collectionRepository::findPaginated */
    protected array $collectionReturn = [];

    /** @var array<mixed> Mutable return value for fieldRepository::findPaginated */
    protected array $fieldReturn = [];

    /** @var array<mixed> Mutable return value for relationRepository::findPaginated */
    protected array $relationReturn = [];

    public function setUp(): void
    {
        $this->collectionReturn = [];
        $this->fieldReturn      = [];
        $this->relationReturn   = [];

        $this->collectionRepository = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->fieldRepository      = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->relationRepository   = $this->createMock(RelationRepositoryInterface::class);
        $this->connection           = $this->createMock(Connection::class);

        $this->collectionRepository->method('findPaginated')
            ->willReturnCallback(fn() => $this->collectionReturn);
        $this->fieldRepository->method('findPaginated')
            ->willReturnCallback(fn() => $this->fieldReturn);
        $this->relationRepository->method('findPaginated')
            ->willReturnCallback(fn() => $this->relationReturn);

        $this->class = $this->buildHandler();
    }

    public function tearDown(): void
    {
        unset(
            $this->collectionRepository,
            $this->fieldRepository,
            $this->relationRepository,
            $this->connection,
            $this->class,
        );
    }

    /**
     * Builds a fresh ApplySchemaHandler wiring all real objects from current mock state.
     */
    protected function buildHandler(): ApplySchemaHandler
    {
        $schemaManager = new SchemaManagerService($this->connection);
        $diffService   = new SchemaDiffService();

        $snapshotService = new SchemaSnapshotService(
            collectionRepository: $this->collectionRepository,
            fieldRepository:      $this->fieldRepository,
            relationRepository:   $this->relationRepository,
        );

        return new ApplySchemaHandler(
            snapshotService:         $snapshotService,
            diffService:             $diffService,
            createCollectionHandler: new CreateCollectionHandler($this->collectionRepository, $this->fieldRepository, $schemaManager),
            updateCollectionHandler: new UpdateCollectionHandler($this->collectionRepository),
            deleteCollectionHandler: new DeleteCollectionHandler($this->collectionRepository, $schemaManager, $this->fieldRepository),
            collectionRepository:    $this->collectionRepository,
            createFieldHandler:      new CreateFieldHandler($this->fieldRepository, $this->collectionRepository, $schemaManager),
            updateFieldHandler:      new UpdateFieldHandler($this->fieldRepository),
            deleteFieldHandler:      new DeleteFieldHandler($this->fieldRepository, $schemaManager),
            fieldRepository:         $this->fieldRepository,
            createRelationHandler:   new CreateRelationHandler($this->relationRepository),
            deleteRelationHandler:   new DeleteRelationHandler($this->relationRepository),
            relationRepository:      $this->relationRepository,
        );
    }

    /** Returns an ApplySchemaCommand with an empty snapshot. */
    protected function emptyCommand(bool $force = false): ApplySchemaCommand
    {
        return new ApplySchemaCommand(
            snapshot: ['version' => 1, 'collections' => [], 'relations' => []],
            force:    $force,
        );
    }

    /** Returns a command whose snapshot contains one collection (no fields). */
    protected function commandWithCollection(string $name, bool $force = false): ApplySchemaCommand
    {
        return new ApplySchemaCommand(
            snapshot: [
                'version'     => 1,
                'collections' => [[
                    'collection' => $name,
                    'meta'       => ['label' => null, 'icon' => null, 'note' => null, 'hidden' => false, 'singleton' => false, 'sort_field' => null],
                    'fields'     => [],
                ]],
                'relations' => [],
            ],
            force: $force,
        );
    }

    /** Returns a command whose snapshot contains one relation. */
    protected function commandWithRelation(string $manyCollection, string $manyField, bool $force = false): ApplySchemaCommand
    {
        return new ApplySchemaCommand(
            snapshot: [
                'version'     => 1,
                'collections' => [],
                'relations'   => [[
                    'many_collection'     => $manyCollection,
                    'many_field'          => $manyField,
                    'one_collection'      => null,
                    'one_field'           => null,
                    'junction_collection' => null,
                ]],
            ],
            force: $force,
        );
    }
}
