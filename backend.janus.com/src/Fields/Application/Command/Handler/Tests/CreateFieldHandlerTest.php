<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\Handler\CreateFieldHandler;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class CreateFieldHandlerTest extends TestCase
{
    /** @var MockObject&FieldMetaRepositoryInterface */
    protected MockObject $fieldRepository;

    /** @var MockObject&CollectionMetaRepositoryInterface */
    protected MockObject $collectionRepository;

    /** @var MockObject&Connection */
    protected MockObject $connection;

    protected SchemaManagerService $schemaManager;
    protected CreateFieldHandler $handler;

    public function setUp(): void
    {
        $this->fieldRepository      = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->collectionRepository = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->connection           = $this->createMock(Connection::class);
        $this->schemaManager        = new SchemaManagerService($this->connection);

        $this->handler = new CreateFieldHandler(
            fieldRepository:      $this->fieldRepository,
            collectionRepository: $this->collectionRepository,
            schemaManager:        $this->schemaManager,
        );
    }

    public function tearDown(): void
    {
        unset($this->fieldRepository, $this->collectionRepository, $this->connection, $this->schemaManager, $this->handler);
    }

    protected function makeCollectionMeta(): CollectionMeta
    {
        return CollectionMeta::reconstitute(
            id:        'cccccccc-0000-7000-8000-000000000001',
            name:      'articles',
            label:     null,
            icon:      null,
            note:      null,
            hidden:    false,
            singleton: false,
            sortField: null,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: null,
        );
    }
}
