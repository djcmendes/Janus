<?php

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler\Tests;

use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\Handler\DeleteFieldHandler;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class DeleteFieldHandlerTest extends TestCase
{
    /** @var MockObject&FieldMetaRepositoryInterface */
    protected MockObject $repository;

    /** @var MockObject&Connection */
    protected MockObject $connection;

    protected SchemaManagerService $schemaManager;
    protected DeleteFieldHandler $handler;

    public function setUp(): void
    {
        $this->repository    = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->connection    = $this->createMock(Connection::class);
        $this->schemaManager = new SchemaManagerService($this->connection);

        $this->handler = new DeleteFieldHandler(
            repository:    $this->repository,
            schemaManager: $this->schemaManager,
        );
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->connection, $this->schemaManager, $this->handler);
    }

    protected function makeFieldMeta(FieldType $type = FieldType::STRING): FieldMeta
    {
        return FieldMeta::reconstitute(
            id:         'aaaaaaaa-0000-7000-8000-000000000001',
            collection: 'articles',
            field:      'title',
            type:       $type,
            label:      null,
            note:       null,
            required:   false,
            readonly:   false,
            hidden:     false,
            sortOrder:  0,
            interface:  null,
            options:    null,
            createdAt:  new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:  null,
        );
    }
}
